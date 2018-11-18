<?php
declare ( strict_types = 1 );
namespace clintrials\admin;

use Logger;
use clintrials\admin\validation\ValidationResult;
use clintrials\admin\metadata\DbSchema;

class ReportDb {
	private $logger;
	private $dbSchema;
	private $reportTables = array();
	private $dbExist = false;
	private $numberXmlTables = 0;
	private $numberDbTables = 0;
    private $validationResult;



	function __construct() {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->validationResult = new ValidationResult();
	}

	public function getReportTableByTableName($tableName) : ReportTable {
		foreach ($this->reportTables as $reportTable) {
		 	if($reportTable->getTable()->getName() == $tableName){
		 		return $reportTable;
		 	}
		 }
		 return null;
	}

	public function getReportTables() : array {
		return $this->reportTables;
	}

	public function setReportTables(array $reportTables) : void {
		$this->reportTables = $reportTables;
	}

	public function addReportTable(ReportTable $reportTable) : void {
		$this->reportTables[] = $reportTable;
	}

	public function getNumberXmlTables() : int {
		return $this->numberXmlTables;
	}

	public function getNumberDbTables() : int {
		return $this->numberDbTables;
	}

	public function getDbSchema() : DbSchema {
		return $this->dbSchema;
	}

	public function getValidationResult() : ValidationResult {
		return $this->validationResult;
	}



	public static function createReport(MetadataCreator $metadataCreator, DdlExecutor $ddlExecutor) : ReportDb {
		$logger = Logger::getLogger ( __CLASS__ );
		$logger->trace ( "START" );
		$reportDb = new ReportDb();

		

        $tables = $metadataCreator->getDb()->getTables();

        $reportDb->dbSchema = $metadataCreator->getDb();
		$reportDb->numberXmlTables = count($tables);
		$reportDb->numberDbTables = $ddlExecutor->getTablesNumber();
		$reportDb->validationResult = new ValidationResult();
		if (!$ddlExecutor->dbExists()) {
			$reportDb->validationResult->errors[] = "Db is not exist";
			$reportDb->validationResult->passed = false;
		}

		if ($reportDb->numberXmlTables == 0) {
			$reportDb->validationResult->errors[] = "In XML no tables";
			$reportDb->validationResult->passed = false;
		}

		if ($reportDb->numberDbTables == 0) {
			$reportDb->validationResult->errors[] = "In Db no tables";
			$reportDb->validationResult->passed = false;
		}

		if ($reportDb->numberXmlTables > $reportDb->numberDbTables) {
			$reportDb->validationResult->errors[] = "Number tables in XML less than in Db";
			$reportDb->validationResult->passed = false;
		}




		foreach ($tables as $table) {
			$logger->trace ( "start report for table: " . $table->getName());

			$reportTable = new ReportTable();

			$reportTableValid = true;
	        $reportTable->setTable($table);
	        $reportTable->setTableExist($ddlExecutor->tableExists ($table->getName()));
	        if (!$reportTable->getTableExist()) {
	        	$reportTableValid  = false;
	        	$logger->trace ( "reportTableValid  = false in 1) getTableExist" );
	        } else {
	        	$validationResult = $ddlExecutor->tableMatched ( $table );
	        	$reportTable->setTableValidationResult($validationResult);
	           	$reportTable->setTableValid($validationResult->passed);
	           	if (!$validationResult->passed) {
	           		$reportTableValid  = false;	
	           		$logger->trace ( "reportTableValid  = false in 2) validationResult->passed" );
	           	}
	           	if ($ddlExecutor->triggerExists($table->getTriggerInsert())) {
	           	  $reportTable->setTriggerInsertExist(true);
	           	} else {
	           	  	$reportTable->setTriggerInsertExist(false);
	           	  	$reportTableValid  = false;	
	           	  	$logger->trace ( "reportTableValid  = false in 3) getTriggerInsertExist" );
	           	}
	           	if ($ddlExecutor->triggerExists($table->getTriggerUpdate())) {
	           		$reportTable->setTriggerUpdateExist(true);
	           	} else {
	           		$reportTable->setTriggerUpdateExist(false);
	           		$reportTableValid  = false;	
	           		$logger->trace ( "reportTableValid  = false in 4) getTriggerUpdateExist" );
	           	}
	        }

	        $reportTable->setTableJrnlExist($ddlExecutor->tableExists ($table->getTableJrnl()->getName()));
	        if (!$reportTable->getTableJrnlExist()) {
	        	$reportTableValid  = false;	
	        	$logger->trace ( "reportTableValid  = false in 5) getTableJrnlExist" );
	        } else {
	        	$validationResult = $ddlExecutor->tableMatched ( $table->getTableJrnl() );
	        	$reportTable->setTableJrnlValidationResult($validationResult);
	           	$reportTable->setTableJrnlVaild($validationResult->passed);
	           	if (!$validationResult->passed) {
	           		$reportTableValid  = false;	
	           		$logger->trace ( "reportTableValid  = false in 6) validationResult->passed for Jrnl table" );
	           	}
	        }
	        $reportTable->setReportTableValid($reportTableValid);
	        $reportDb->addReportTable($reportTable);
            if (!$reportTableValid) {
	        	$reportDb->validationResult->errors[] = "See table's report";
				$reportDb->validationResult->passed = false;
			}



	        $logger->trace ( "finish report for table: " . $table->getName());
		}
        $logger->trace ( "FINISH" );
		return $reportDb;

	}
	
}