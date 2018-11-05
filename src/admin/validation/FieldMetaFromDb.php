<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class FieldMetaFromDb {
	// t.COLUMN_NAME, t.DATA_TYPE, t.COLUMN_COMMENT
	public $column_name;
	public $column_comment;
	public $data_type;
	function __construct(string $column_name, string $column_comment, string $data_type) {
		$this->column_name = $column_name;
		$this->column_comment = $column_comment;
		$this->data_type = $data_type;
	}
}