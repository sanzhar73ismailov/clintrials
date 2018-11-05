<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class TableMetaFromDb {
	public $columns = array ();
	
	public function getFieldsName() : array {
		$columnNames = array();
		foreach ( $this->columns as $field ) {
			$columnNames [] = $field->column_name;
		}
		return $columnNames;
	}
	
	public function getFieldNameTypeComments() : array {
		$columnNames = array();
		foreach ( $this->columns as $field ) {
			if (0) $field = new FieldMetaFromDb(0, 0, 0);
			$columnNames [] = sprintf("%s-%s-%s",
					strtolower($field->column_name), strtolower($field->data_type), $field->column_comment);
		}
		return $columnNames;
	}
}