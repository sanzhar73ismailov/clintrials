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
			$columnNames [] = (string) $field;
		}
		return $columnNames;
	}
}