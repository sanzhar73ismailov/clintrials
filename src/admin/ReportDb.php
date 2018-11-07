<?php
declare ( strict_types = 1 );
namespace clintrials\admin;

use Logger;

class ReportDb {
	private $logger;
	private $reportTables = array();

	function __construct() {
		$this->logger = Logger::getLogger ( __CLASS__ );
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

	public static function createReport(MetadataCreator $metadataCreator, DdlExecutor $ddlExecutor) : ReportDb {
		$reportDb = new ReportDb();



		foreach ($metadataCreator->getDb()->getTables() as $table) {
			$reportTable = new ReportTable();

			$reportTableValid = true;
			/*
			private $table;
			private $tableExist = false;
			private $tableValid= false;
			private $tableJrnlExist= false;
			private $tableJrnlVaild= false;
			private $triggerInsertExist= false;
			private $triggerUpdateExist= false;
			private $tableValidationResult;
			private $tableJrnlValidationResult;

			*/
	        $reportTable->setTable($table);
	        $reportTable->setTableExist($ddlExecutor->tableExists ($table->getName()));
	        if (!$reportTable->getTableExist()) {
	        	$reportTableValid  = false;
	        }else{
	        	$validationResult = $ddlExecutor->tableMatched ( $table );
	        	$reportTable->setTableValidationResult($validationResult);
	           	$reportTable->setTableValid($validationResult->passed);
	           	if (!$validationResult->passed) {
	           		$reportTableValid  = false;	
	           	}
	           	if($ddlExecutor->triggerExists($table->getTriggerInsert())) {
	           	  $reportTable->setTriggerInsertExist(true);
	           	}else{
	           	  	$reportTable->setTriggerInsertExist(false);
	           	  	$reportTableValid  = false;	
	           	}
	           	if($ddlExecutor->triggerExists($table->getTriggerUpdate())) {
	           		$reportTable->setTriggerUpdateExist(true);
	           		$reportTableValid  = false;	
	           	}
	        }

	        $reportTable->setTableJrnlExist($ddlExecutor->tableExists ($table->getTableJrnj()->getName()));
	        if (!$reportTable->getTableJrnlExist()) {
	        	$reportTableValid  = false;	
	        } else {
	        	$validationResult = $ddlExecutor->tableMatched ( $table->getTableJrnj() );
	        	$reportTable->setTableJrnlValidationResult($validationResult);
	           	$reportTable->setTableJrnlVaild($validationResult->passed);
	        }
	        $reportTable->setReportTableValid($reportTableValid);
	        $reportDb->addReportTable($reportTable);
		}

		return $reportDb;

	}
	
}