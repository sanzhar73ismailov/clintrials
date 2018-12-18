<?php
declare ( strict_types = 1 );
namespace clintrials\admin\metadata;

use clintrials\admin\metadata\ {
	TableJrnl, 
	Trigger
};
use Exception;
use InvalidArgumentException;

class Table extends MetaGeneral {
	protected $fields = array ();
	private $patient = false;
	private $tableJrnl;
	private $triggerInsert;
	private $triggerUpdate;
	
	public function __construct($name = "") {
		$this->name = $name;
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
	public function getFirstField() {
		if (count($this->fields)) {
			return $this->fields[0];
		}
		return null;
	}

	public function getLastField() {
		if (count($this->fields)) {
			return $this->fields[count($this->fields) - 1];
		}
		return null;
	}

	public function getFieldIndex($field) {
		return $this->getFieldIndexByName($field->getName());
	}

	public function getFieldIndexByName($name) {
		for ($i=0; $i < count($this->fields); $i++) { 
			if($this->fields[$i]->getName() == $name) {
				return $i;
			}
		}
		return -1;
	}

	public function reorderField($field, string $after) {
		$currentIndex = $this->getFieldIndex($field);
		//$prevField = $this->getFieldByName($after);
		$fieldIndex = $this->getFieldIndexByName($after) + 1;

		$this->moveElement($this->fields, $currentIndex, $fieldIndex);
		$this->setFields($this->fields);



	}

	function moveElement(&$array, $a, $b) {
        $out = array_splice($array, $a, 1);
        array_splice($array, $b, 0, $out);
    }



	public function addField(Field $field) {
		if(!$field->getName()){
			throw new InvalidArgumentException("New field has no name");
		}
		$field->setName(trim(strtolower($field->getName())));
		$pattern = "/^[A-Za-z][A-Za-z0-9_]*$/";
		if(!preg_match($pattern, $field->getName())){
			throw new InvalidArgumentException("New field has invalid name {$field->getName()}");
		}
		if (count($this->fields)) {
			if($this->getFieldByName($field->getName()) != null){
				throw new InvalidArgumentException("Field {$field->getName()} alredy exists in table {$this->getName()}");
			}
			$lastField = $this->fields[count($this->fields) - 1];
			$lastField->setNext($field->getName());
			$field->setPrev($lastField->getName());
			//$field->setAfter($this->fields[count($this->fields) - 1]->getName());
		}
		$this->fields [] = $field;
	}
	public function addFields(array $fields) : void {
		foreach ( $fields as $field ) {
			$this->addField($field);
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
		$this->fields = array();
		$this->addFields($fields);

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

	public function getFieldByName(string $name) {
		foreach ( $this->fields as $field ) {
			if($field->getName () == $name) {
				return $field;
			}
		}
		return null;
	}

	public function getFieldBefore(string $name) : Field{
		$filedBefore = new Field ("","","");
		foreach ( $this->fields as $field ) {
			if($field->getName () == $name) {
				break;
			}
			$filedBefore = $field;
		}
		return $filedBefore;
	}


	/**
	* Return field as string + name of field before as hash 
     */
	public function getFieldAsHashByName(string $name) : string{
		$field = $this->getFieldByName($name);
		$fieldBefore = $this->getFieldBefore($name);
		$res = ((string)$field) .  ($fieldBefore ?: "" );

		return $res;
	}

}

