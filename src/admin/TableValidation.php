<?php

namespace clintrials\admin;

use clintrials\admin\metadata\Table;

class TableValidation {
		private $table;
		private $tableMetaFromDb;
		public function __construct($table, $tableMetaFromDb) {
			$this->table = $table;
			$this->tableMetaFromDb = $tableMetaFromDb;
		}
		public function validate() {
			$validationResult = new ValidationResult ();
			$table = $this->table;
			$tableMetaFromDb = $this->tableMetaFromDb;
			
			$columnsNamesXml = array ();
			$columnsNamesDb = array ();
			
			foreach ( $table->getFields () as $field ) {
				$columnsNamesXml [] = $field->getName ();
			}
			
			foreach ( $tableMetaFromDb->columns as $field ) {
				$columnsNamesDb [] = $field->column_name;
			}
			
			if (false) {
				$table = new Table ();
				$tableMetaFromDb = new TableMetaFromDb ();
			}
			if (count ( $table->getFields () ) != count ( $tableMetaFromDb->columns )) {
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "Number of columns is different: in xml %s, in db %s", count ( $table->getFields () ), count ( $tableMetaFromDb->columns ) );
			}
			return $validationResult;
		}
	}
	

?>