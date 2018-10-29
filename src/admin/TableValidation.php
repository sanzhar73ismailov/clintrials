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
			if (false) {
				$table = new Table ();
				$tableMetaFromDb = new TableMetaFromDb ();
			}
			
			$columnsNamesXml = array ();
			$columnsNamesDb = array ();
			
			$columnsNamesXml = $table->getFieldsName();
			$columnsNamesDb = $tableMetaFromDb->getFieldsName();
			
			if (count ( $columnsNamesXml ) != count ( $columnsNamesDb )) {
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "Number of columns is different: in XML %s, in DB %s", 
						count ( $columnsNamesXml ), count ( $columnsNamesDb ) );
			}
			$resultDiff = array_diff($columnsNamesXml, $columnsNamesDb);
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "XML has columns %s that are not available in DB", implode(",", $resultDiff));
			}
			$resultDiff = array_diff($columnsNamesDb, $columnsNamesXml);
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "DB has columns %s that are not available in XML", implode(",", $resultDiff));
			}
			$columnsNameTypeCommentsXml = $table->getFieldNameTypeComments();
			$columnsNameTypeCommentsDb = $tableMetaFromDb->getFieldNameTypeComments();
			
			$resultDiff = array_diff($columnsNameTypeCommentsXml, $columnsNameTypeCommentsDb);
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "XML has columns with types and comments %s that are not available in DB", implode(",", $resultDiff));
			}
			$resultDiff = array_diff($columnsNameTypeCommentsDb, $columnsNameTypeCommentsXml);
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "DB has columns with types and comments %s that are not available in XML", implode(",", $resultDiff));
			}
			
			return $validationResult;
		}
	}
	

?>