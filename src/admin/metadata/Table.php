<?php

namespace clintrials\admin\metadata;

use clintrials\admin\metadata\TableJrnl;
use clintrials\admin\metadata\Trigger;

class Table extends MetaGeneral {
	protected $fields = array ();
	private $tableJrnj;
	private $triggerInsert;
	private $triggerUpdate;
	
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

	public function setTriggerInsert($triggerInsert) {
		$this->triggerInsert = $triggerInsert;
	}

	public function setTriggerUpdate($triggerUpdate) {
		$this->triggerUpdate = $triggerUpdate;
	}
	
	public function getFieldsName(){
		$columnNames = array();
		foreach ( $this->fields as $field ) {
			$columnNames [] = $field->getName ();
		}
		return $columnNames;
	}
	
	public function getFieldNameTypeComments(){
		$columnNames = array();
		foreach ( $this->fields as $field ) {
			if (0) $field = new Field(0, 0, 0);
			$columnNames [] = sprintf("%s-%s-%s", 
					strtolower($field->getName ()), strtolower($field->getType()), $field->getComment());
		}
		return $columnNames;
	}

}

