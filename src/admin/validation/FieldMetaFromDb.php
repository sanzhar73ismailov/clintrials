<?php
namespace clintrials\admin\validation;

class FieldMetaFromDb {
	// t.COLUMN_NAME, t.DATA_TYPE, t.COLUMN_COMMENT
	public $column_name;
	public $column_comment;
	public $data_type;
	function __construct($column_name, $column_comment, $data_type) {
		$this->column_name = $column_name;
		$this->column_comment = $column_comment;
		$this->data_type = $data_type;
	}
}