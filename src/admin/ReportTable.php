<?php
declare ( strict_types = 1 );
namespace clintrials\admin;

use Logger;
use clintrials\admin\validation\ValidationResult;
use clintrials\admin\metadata\ {
	DbSchema,
	Table,
	Trigger
};

class ReportTable {
	private $logger;
	private $table;

	//private $tableExist = false;
	//private $tableValid= false;
	//private $tableJrnlExist= false;
	//private $tableJrnlVaild= false;
	//private $triggerInsertExist= false;
	//private $triggerUpdateExist= false;
	//private $triggerInsertValid= false;
	//private $triggerUpdateValid= false;
	

	private $tableValidationResult;
	private $tableJrnlValidationResult;
	private $triggerInsertValidationResult;
	private $triggerUpdateValidationResult;
	private $reportTableValid = false;


	function __construct() {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->validationResult = new ValidationResult();
	}
	public function getLogger() {
		return $this->logger;
	}

	public function setLogger($logger) {
		$this->logger = $logger;
	}

	public function getTable() : Table {
		return $this->table;
	}

	public function setTable(Table $table) {
		$this->table = $table;
	}

	public function getTableExist() {
		return $this->tableValidationResult->objectExists;
	}

	public function getTableValid() {
		return $this->tableValidationResult->passed;
	}

	public function getTableJrnlExist() {
		return $this->tableJrnlValidationResult->objectExists;
	}

	public function getTableJrnlVaild() {
		return $this->tableJrnlValidationResult->passed;
	}

	public function getTriggerInsertExist() {
		return $this->triggerInsertValidationResult->objectExists;
	}

	public function getTriggerUpdateExist() {
		return $this->triggerUpdateValidationResult->objectExists;
	}

	public function getTriggerInsertValid() {
		return $this->triggerInsertValidationResult->passed;
	}
	
	public function getTriggerUpdateValid() {
		return $this->triggerUpdateValidationResult->passed;
	}
	
	public function getTableValidationResult() {
		return $this->tableValidationResult;
	}

	public function setTableValidationResult($tableValidationResult) {
		$this->tableValidationResult = $tableValidationResult;
	}

	public function getTableJrnlValidationResult() {
		return $this->tableJrnlValidationResult;
	}

	public function setTableJrnlValidationResult($tableJrnlValidationResult) {
		$this->tableJrnlValidationResult = $tableJrnlValidationResult;
	}

	public function getTriggerInsertValidationResult() {
		return $this->triggerInsertValidationResult;
	}
	
	public function setTriggerInsertValidationResult($triggerInsertValidationResult) {
		$this->triggerInsertValidationResult = $triggerInsertValidationResult;
	}
	
	public function getTriggerUpdateValidationResult() {
		return $this->triggerUpdateValidationResult;
	}
	
	public function setTriggerUpdateValidationResult($triggerUpdateValidationResult) {
		$this->triggerUpdateValidationResult = $triggerUpdateValidationResult;
	}
	
	public function getReportTableValid() {
		return $this->reportTableValid;
	}

	public function setReportTableValid($reportTableValid) {
		$this->reportTableValid = $reportTableValid;
	}

	
}