<?php
namespace clintrials\admin;

use clintrials\admin\metadata\Table;

class TableValidation {
	private $table;
	private $tableMetaFromDb;
	
	public function __construct($table, $tableMetaFromDb){
		$this->table = $table;
		$this->tableMetaFromDb = $tableMetaFromDb;
	}
	
	public function validate(){
		$validationResult = new ValidationResult();
		$table = $this->table;
		$tableMetaFromDb = $this->tableMetaFromDb;
		if (false) {
			$table = new Table();
			$tableMetaFromDb = new TableMetaFromDb();
		}
		if(count($table->getFields()) != count($tableMetaFromDb->$columns)) {
			$validationResult->passed = false;
			$validationResult->errors[] = sprintf("Number of columns is different: in xml %s, in db %s", 
					count($table->getFields()), count($tableMetaFromDb->$columns)); 
		}
		
	}
	
}

class TableMetaFromDb {
	public $columns = array();
}

class FieldMetaFromDb {
	//t.COLUMN_NAME, t.DATA_TYPE, t.COLUMN_COMMENT
	public $column_name;
	public $data_type;
	public $column_comment;
}

class ValidationResult {
	public $passed = true;
	public $errors = array(); // array of error messages
}