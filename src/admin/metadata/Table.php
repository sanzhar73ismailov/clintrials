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
	private $tableJrnj;
	private $triggerInsert;
	private $triggerUpdate;
	
	public function __construct() {
	}
	public function createTableJrnl() : void {
		if (empty ( $this->fields )) {
			throw new \Exception ( "no fields in table" );
		}
		$this->tableJrnj = new TableJrnl ( $this );
	}
	public function getTableJrnj() : TableJrnl {
		return $this->tableJrnj;
	}
	public function setTableJrnj(TableJrnl $tableJrnj) : void {
		$this->tableJrnj = $tableJrnj;
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
			if (0) $field = new Field(0, 0, 0);
			$type = strtolower($field->getType());
			if($type=='list' or $type=='boolean'){
				$type = 'int';
			}
			$columnNames [] = sprintf("%s-%s-%s", 
					strtolower($field->getName ()), $type, $field->getComment());
		}
		return $columnNames;
	}

}

