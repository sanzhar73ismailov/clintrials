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
		$logger = Logger::getLogger ( __CLASS__ );
		$logger->trace ( "START" );
		$reportDb = new ReportDb();



		foreach ($metadataCreator->getDb()->getTables() as $table) {
			$logger->trace ( "start report for table: " . $table->getName());

			$reportTable = new ReportTable();

			$reportTableValid = true;
	        $reportTable->setTable($table);
	        $reportTable->setTableExist($ddlExecutor->tableExists ($table->getName()));
	        if (!$reportTable->getTableExist()) {
	        	$reportTableValid  = false;
	        	$logger->trace ( "reportTableValid  = false in 1" );
	        } else {
	        	$validationResult = $ddlExecutor->tableMatched ( $table );
	        	$reportTable->setTableValidationResult($validationResult);
	           	$reportTable->setTableValid($validationResult->passed);
	           	if (!$validationResult->passed) {
	           		$reportTableValid  = false;	
	           		$logger->trace ( "reportTableValid  = false in 2" );
	           	}
	           	if ($ddlExecutor->triggerExists($table->getTriggerInsert())) {
	           	  $reportTable->setTriggerInsertExist(true);
	           	} else {
	           	  	$reportTable->setTriggerInsertExist(false);
	           	  	$reportTableValid  = false;	
	           	  	$logger->trace ( "reportTableValid  = false in 3" );
	           	}
	           	if ($ddlExecutor->triggerExists($table->getTriggerUpdate())) {
	           		$reportTable->setTriggerUpdateExist(true);
	           	} else {
	           		$reportTable->setTriggerUpdateExist(false);
	           		$reportTableValid  = false;	
	           		$logger->trace ( "reportTableValid  = false in 4" );
	           	}
	        }

	        $reportTable->setTableJrnlExist($ddlExecutor->tableExists ($table->getTableJrnj()->getName()));
	        if (!$reportTable->getTableJrnlExist()) {
	        	$reportTableValid  = false;	
	        	$logger->trace ( "reportTableValid  = false in 5" );
	        } else {
	        	$validationResult = $ddlExecutor->tableMatched ( $table->getTableJrnj() );
	        	$reportTable->setTableJrnlValidationResult($validationResult);
	           	$reportTable->setTableJrnlVaild($validationResult->passed);
	        }
	        $reportTable->setReportTableValid($reportTableValid);
	        $reportDb->addReportTable($reportTable);
	        $logger->trace ( "finish report for table: " . $table->getName());
		}
        $logger->trace ( "FINISH" );
		return $reportDb;

	}
	
}