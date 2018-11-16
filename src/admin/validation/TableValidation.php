<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

use clintrials\admin\metadata\Table;
use Logger;

class TableValidation {
		private $logger;
		private $table;
		private $tableMetaFromDb;
		public function __construct(Table $table, TableMetaFromDb $tableMetaFromDb) {
			$this->table = $table;
			$this->tableMetaFromDb = $tableMetaFromDb;
		}
		public function validate() : ValidationResult {
			$this->logger = Logger::getLogger(__CLASS__);
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
				$validationResult->errors [] = sprintf ( "Table '%s' - number of columns is different: in XML %s, in DB %s", 
						$table->getName(), count ( $columnsNamesXml ), count ( $columnsNamesDb ) );
			}
			$resultDiff = array_diff($columnsNamesXml, $columnsNamesDb);
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "Table '%s' - XML has columns %s that are not available in DB", $table->getName(), implode(",", $resultDiff));
			}
			$resultDiff = array_diff($columnsNamesDb, $columnsNamesXml);
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "Table '%s' - DB has columns %s that are not available in XML", $table->getName(), implode(",", $resultDiff));
			}
			$columnsNameTypeCommentsXml = $table->getFieldNameTypeComments();
			$columnsNameTypeCommentsDb = $tableMetaFromDb->getFieldNameTypeComments();

			$this->logger->debug("show columnsNameTypeCommentsXml for table " . $table->getName() );
			foreach ($columnsNameTypeCommentsXml as $key => $value) {
				$this->logger->trace("$key => $value");
			}
			$this->logger->debug("show columnsNameTypeCommentsXml end");
			$this->logger->debug("show columnsNameTypeCommentsDb for table " . $table->getName() );
			foreach ($columnsNameTypeCommentsDb as $key => $value) {
				$this->logger->trace("$key => $value");
			}
			$this->logger->debug("show columnsNameTypeCommentsDb end");
			$resultDiff = array_diff($columnsNameTypeCommentsXml, $columnsNameTypeCommentsDb);
			$this->logger->debug('resultDiff=' . var_export($resultDiff,true));
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "Table '%s' - XML has columns with types and comments %s that are not available in DB", $table->getName(), implode(",", $resultDiff));
			}
			$resultDiff = array_diff($columnsNameTypeCommentsDb, $columnsNameTypeCommentsXml);
			if(count($resultDiff)){
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf ( "Table '%s' - DB has columns with types and comments %s that are not available in XML", $table->getName(), implode(",", $resultDiff));
			}
			if($validationResult->passed){
				$this->logger->trace("1");
				for ($i=0; $i < count($columnsNameTypeCommentsXml); $i++) { 
					$this->logger->trace("2-" . $i);
					 $colInXml = $columnsNameTypeCommentsXml[$i];
					 $colInDb = $columnsNameTypeCommentsDb[$i];
					 $this->logger->trace(var_export($colInXml, true));
					 $this->logger->trace(var_export($colInDb, true));
					 $this->logger->trace(var_export($colInXml==$colInDb, true));
					 $this->logger->trace(var_export($colInXml===$colInDb, true));
					 if($colInXml !== $colInDb){
					 	$this->logger->trace("3");
					 	$validationResult->passed = false;
					 	$validationResult->errors [] = sprintf ( "Table '%s' - the order of columns in XML is different than in DB. In XML on position '%s' is column '%s', in DB is '%s'", $table->getName(), $i+1, $colInXml, $colInDb);
					 	break;
					 }
				}
				$this->logger->trace("4");
			}
			
			return $validationResult;
		}
	}
	

?>