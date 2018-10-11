<?php

namespace clintrials\admin\metadata;

use clintrials\admin\metadata\TableJrnl;
use clintrials\admin\metadata\Trigger;

class Table extends MetaGeneral {
	protected $fields = array ();
	private $tableJrnj;
	private $triggerInsert;
	private $triggerUpdate;
	private $triggerDelete;
	
	public function __construct() {
	}
	public function createTableJrnl() {
		if (empty ( $this->fields )) {
			throw new \Exception ( "no fields in table" );
		}
		$this->tableJrnj = new TableJrnl ( $this );
	}
	public function getTableJrnj() {
		return $this->tableJrnj;
	}
	public function setTableJrnj($tableJrnj) {
		$this->tableJrnj = $tableJrnj;
	}
	public function addField($field) {
		$this->fields [] = $field;
	}
	public function addFields($fields) {
		foreach ( $fields as $field ) {
			$this->fields [] = $field;
		}
	}
	public function getFields() {
		return $this->fields;
	}
	public function setFields($fields) {
		$this->fields = $fields;
	}
	public function getTriggerInsert() {
		return $this->triggerInsert;
	}

	public function getTriggerUpdate() {
		return $this->triggerUpdate;
	}

	public function getTriggerDelete() {
		return $this->triggerDelete;
	}

	public function setTriggerInsert($triggerInsert) {
		$this->triggerInsert = $triggerInsert;
	}

	public function setTriggerUpdate($triggerUpdate) {
		$this->triggerUpdate = $triggerUpdate;
	}

	public function setTriggerDelete($triggerDelete) {
		$this->triggerDelete = $triggerDelete;
	}
}

