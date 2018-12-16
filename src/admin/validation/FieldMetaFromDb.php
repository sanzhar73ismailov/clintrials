<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class FieldMetaFromDb {
	// t.COLUMN_NAME, t.DATA_TYPE, t.COLUMN_COMMENT
	public $column_name;
	public $column_comment;
	public $data_type;
	public $is_nullable = true;
	public $prev;
	public $next;
	
	function __construct(string $column_name, string $column_comment, string $data_type, bool $is_nullable=true) {
		$this->column_name = $column_name;
		$this->column_comment = $column_comment;
		$this->data_type = $data_type;
		$this->is_nullable = $is_nullable;
	}

	public function __toString() {
		$isNull =  $this->is_nullable ? 'is_nullable=YES' : 'is_nullable=NO';
		return sprintf("%s-%s-%s-%s", 
			    strtolower($this->column_name), strtolower($this->data_type), $this->column_comment, $isNull);
	}
}