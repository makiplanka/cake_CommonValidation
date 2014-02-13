<?php
App::uses('Validation', 'Utility');

/**
 * 共通バリデーション
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) maki674
 * @link          https://github.com/maki674/common_validation
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ValidationBehavior extends ModelBehavior {

	/**
	 * 配列なら array_shift() する
	 *
	 * @param mixed $check
	 * @return string
	 */
	protected static function _array_shift($check) {
		if (is_array($check)) {
			$check = array_shift($check);
		}
		return $check;
	}

	/**
	 * フィールドが同じ値か
	 *
	 * @param Model $Model
	 * @param mixed $check
	 * @param string $fieldName チェックするフィールド名
	 * @return boolean
	 */
	public function isSame(Model $Model, $check, $fieldName) {
		$check = self::_array_shift($check);
		return $Model->data[$Model->name][$fieldName] === $check;
	}

	/**
	 * 日付の範囲チェック (from < to)
	 *
	 * from, to が date, datetime, timestamp であることを事前に保証する必要があります。
	 * from にバリデーションエラーがあれば true を返し、なければチェックした結果を返します。
	 *
	 * @param Model $Model
	 * @param array $check to のフィールド
	 * @param string $from from のフィールド
	 * @param boolean $equal 同日・同時刻を許可するか (default: false)
	 * @return boolean
	 */
	public function dateFromTo(Model $Model, $check, $from, $equal = false) {
		$check = self::_array_shift($check);
		if (isset($Model->validationErrors[$Model->name][$from])) {
			return true; // from でエラーがあればバリデーションを行わない
		}

		// タイムスタンプ化
		$timestamp = $this->_toTimestamp($check);

		$from = $timestamp($Model->data[$Model->name][$from]);
		$to = $timestamp($check);

		if ($equal) {
			return $from <= $to;
		} else {
			return $from < $to;
		}
	}

	/**
	 * 日付をタイムスタンプ化する
	 *
	 * @param string $target
	 * @throws InvalidArgumentException 日付または timestamp でない
	 * @return integer timestamp
	 */
	protected function _toTimestamp($target) {
		if (Validation::date($target) || Validation::datetime($target)) {
			return strtotime($target);
		} else if (Validation::numeric($target)) {
			return $target;
		} else {
			throw new InvalidArgumentException('引数が日付または timestamp ではありません');
		}
	}

	/**
	 * 他のフォームが入力されていたら必須にする
	 *
	 * @param Model $Model
	 * @param mixed $check
	 * @param mixed $fields これが入力されていれば必須
	 * @param boolean $all $fields がすべて入力されていれば必須扱いにする (default: false)
	 * @return boolean
	 */
	public function notEmptyIfNoOtherEmpty(Model $Model, $check, $fields, $all = false) {
		if (! is_array($fields)) {
			$fields = array($fields);
		}

		if ($all) {
			// すべて必須モード
			$required = true;
			foreach ($fields as $val) {
				if (! Validation::notEmpty($Model->data[$Model->name][$val])) {
					// 空の項目があれば必須ではない
					$required = false;
					break; // foreach end
				}
			}
		} else {
			// どれか入力されていれば必須モード
			$required = false;
			foreach ($fields as $val) {
				if (Validation::notEmpty($Model->data[$Model->name][$val])) {
					$required = true;
					break; //foreach end
				}
			}
		}

		if ($required) {
			$check = $this->_array_shift($check);
			return Validation::notEmpty($check);
		}
		return true;
	}

	/**
	 * 他のフィールドの入力値によって必須かどうかを判断
	 *
	 * @param Model $Model
	 * @param mixed $check
	 * @param string $field 入力値を判断するフィールド
	 * @param mixed $value この入力値ならば必須
	 * @return boolean
	 */
	public function notEmptyIfOtherValue(Model $Model, $check, $field, $value) {
		if (! is_array($value)) {
			$value = array($value);
		}

		foreach ($value as $val) {
			if ($Model->data[$Model->name][$field] == $val) {
				$check = $this->_array_shift($check);
				return Validation::notEmpty($check);
			}
		}
		return true;
	}

}