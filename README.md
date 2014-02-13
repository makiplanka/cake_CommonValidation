# CakePHP よく使いそうなバリデーションまとめ

バリデーションを Behavior にまとめました。

## ValidationBehavior
使用頻度の高いバリデーションです。

### isSame
入力値が指定したキーと同じ値かチェックします。厳密な型チェック (===) を行います。

	'rule' => array('isSame', string $fieldName)

- `$fieldName`: $this->data[$this->name][**fieldName**] の部分を指定
 
### dateFromTo
日付の範囲チェック (from < to) を行います。このバリデーションルールは to のフィールドに指定します。

	'rule' => array('dateFromTo', string $from [, boolean $equal = false])
	
- `$from`: $this->data[$this->name][**from**] の部分を指定
- `$equal`: 同時刻を許可するかを指定

### notEmptyIfNoOtherEmpty
他のキーが入力されていたら必須チェックを行います。たとえば、あるフィールドが入力されていたら、このフィールドを必須にしたい時に使用します。

	'rule' => array('notEmptyIfNoOtherEmpty', mixed $fields [, boolean $all = false])

- `$fields`: 入力をチェックしたい他のキーを添字配列で指定
- `$all`: `$fields` がすべて入力されていたら必須にする

### notEmptyIfOtherValue
他のキーの入力値の内容によって必須チェックを行います。たとえば、ある選択肢が選択されていたら、このフィールドを必須にしたい時に使用します。

入力値の比較は (==) で行います。

	'rule' => array('notEmptyIfOtherValue', string $field, mixed $value)
	
- `$field`: $this->data[$this->name][**field**] の部分を指定
- `$value`: 必須条件にしたい `$field` の値を文字列または配列で指定
