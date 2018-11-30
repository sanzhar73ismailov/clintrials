<?php
declare ( strict_types = 1 );
namespace clintrials\admin\metadata;

use clintrials\admin\metadata\ {
	TableJrnl, 
	Trigger
};
//use clintrials\admin\metadata\Trigger;

class Table extends MetaGeneral {
	protected $fields = array ();
	private $patient = false;
	private $tableJrnl;
	private $triggerInsert;
	private $triggerUpdate;
	
	public function __construct() {
	}
	public function createTableJrnl() : void {
		if (empty ( $this->fields )) {
			throw new \Exception ( "no fields in table" );
		}
		$this->tableJrnl = new TableJrnl ( $this );
	}
	public function getTableJrnl() : TableJrnl {
		return $this->tableJrnl;
	}
	public function setTableJrnl(TableJrnl $tableJrnl) : void {
		$this->tableJrnl = $tableJrnl;
	}
	public function addField(Field $field) {
		$this->fields [] = $field;
	}
	public function addFields(array $fields) : void {
		foreach ( $fields as $field ) {
			$this->fields [] = $field;
		}
	}
	public function isPatient() : bool {
		return $this->patient;
	}
	public function setPatient(bool $patient) : void {
		$this->patient = $patient;
	}
	public function getFields() : array {
		return $this->fields;
	}
	public function setFields(array $fields) : void {
		$this->fields = $fields;
	}
	public function getTriggerInsert() : Trigger{
		return $this->triggerInsert;
	}

	public function getTriggerUpdate() : Trigger{
		return $this->triggerUpdate;
	}

	public function setTriggerInsert(Trigger $triggerInsert) : void {
		$this->triggerInsert = $triggerInsert;
	}

	public function setTriggerUpdate(Trigger $triggerUpdate) : void{
		$this->triggerUpdate = $triggerUpdate;
	}
	
	public function getFieldsName() : array{
		$columnNames = array();
		foreach ( $this->fields as $field ) {
			$columnNames [] = $field->getName ();
		}
		return $columnNames;
	}
	
	public function getFieldNameTypeComments() : array{
		$columnNames = array();
		foreach ( $this->fields as $field ) {
			$columnNames [] = (string) $field;
		}
		return $columnNames;
	}

	public function getFieldByName(string $name) : Field{
		foreach ( $this->fields as $field ) {
			if($field->getName () == $name) {
				return $field;
			}
		}
		return null;
	}

	public function getFieldBefore(string $name) {
		$filedBefore = null;
		foreach ( $this->fields as $field ) {
			if($field->getName () == $name) {
				return $filedBefore;
			}
			$filedBefore = $field;
		}
		return false;
	}

}

