<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class TableMetaFromDb {
	public $columns = array ();
	
	public function getFieldByName(string $name) {
		foreach ( $this->columns as $field ) {
			if ($field->column_name == $name) {
				return $field;
			}
		}
		return false;
	}

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

	public function getFieldBefore(string $name) {
		$filedBefore = new FieldMetaFromDb("", "", "");
		foreach ( $this->columns as $field ) {
			if($field->column_name == $name) {
				break;
			}
			$filedBefore = $field;
		}
		return $filedBefore;
	}
}