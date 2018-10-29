<?php
namespace clintrials\admin;

class TableMetaFromDb {
	public $columns = array ();
	
	public function getFieldsName(){
		$columnNames = array();
		foreach ( $this->columns as $field ) {
			$columnNames [] = $field->column_name;
		}
		return $columnNames;
	}
	
	public function getFieldNameTypeComments(){
		$columnNames = array();
		foreach ( $this->columns as $field ) {
			if (0) $field = new FieldMetaFromDb(0, 0, 0);
			$columnNames [] = sprintf("%s-%s-%s",
					strtolower($field->column_name), strtolower($field->data_type), $field->column_comment);
		}
		return $columnNames;
	}
}