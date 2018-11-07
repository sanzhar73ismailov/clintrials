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
	private $tableExist = false;
	private $tableValid= false;
	private $tableJrnlExist= false;
	private $tableJrnlVaild= false;
	private $triggerInsertExist= false;
	private $triggerUpdateExist= false;
	private $tableValidationResult;
	private $tableJrnlValidationResult;
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
		return $this->tableExist;
	}

	public function setTableExist($tableExist) {
		$this->tableExist = $tableExist;
	}

	public function getTableValid() {
		return $this->tableValid;
	}

	public function setTableValid($tableValid) {
		$this->tableValid = $tableValid;
	}

	public function getTableJrnlExist() {
		return $this->tableJrnlExist;
	}

	public function setTableJrnlExist($tableJrnlExist) {
		$this->tableJrnlExist = $tableJrnlExist;
	}

	public function getTableJrnlVaild() {
		return $this->tableJrnlVaild;
	}

	public function setTableJrnlVaild($tableJrnlVaild) {
		$this->tableJrnlVaild = $tableJrnlVaild;
	}

	public function getTriggerInsertExist() {
		return $this->triggerInsertExist;
	}

	public function setTriggerInsertExist($triggerInsertExist) {
		$this->triggerInsertExist = $triggerInsertExist;
	}

	public function getTriggerUpdateExist() {
		return $this->triggerUpdateExist;
	}

	public function setTriggerUpdateExist($triggerUpdateExist) {
		$this->triggerUpdateExist = $triggerUpdateExist;
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
	public function getReportTableValid() {
		return $this->reportTableValid;
	}

	public function setReportTableValid($reportTableValid) {
		$this->reportTableValid = $reportTableValid;
	}


	
}