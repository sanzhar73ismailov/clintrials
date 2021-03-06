<?php
declare ( strict_types = 1 );
namespace clintrials\admin\metadata;

use Exception;

class Field extends MetaGeneral {
	private $type; //date|float|text|varchar|int|list|boolean|timestamp
	private $pk = false;
	private $service = false; // служенбное поле (типа user_insert или user_update, которое в каждой таблице есть)
	private $null = true; // null is possible
	private $default = null;
	private $after = null;
	
    private $prev = null;
	private $next = null;
	
	public function __construct(string $name = "", string $comment = "", string $type = "", string $after = ""){
		$this->name = $name;
		$this->comment = $comment;
		$this->type = $type;
		$this->after = $after;
	}

	private function buildDdl() : string {
		$ddl = $this->getName () . " ";
			switch ($this->getType ()) {
				case "date" :
					$ddl .= "DATE";
					break;
				case "float" :
					$ddl .= "FLOAT(11,2)";
					break;
				case "text" :
					$ddl .= "TEXT";
					break;
				case "varchar" :
					$ddl .= "VARCHAR(50)";
					break;
				case "timestamp" :
					$ddl .= "TIMESTAMP";
					break;
				case "int" :
				case "integer" :
				case "list" :
				case "boolean" :
					$ddl .= "INTEGER(11)";
					break;
				default :
					throw new Exception ( "type of field is unknown: " . $this->getType () );
			}
			
			if (! $this->getNull ()) {
				$ddl .= " NOT NULL";
			}
			if ($this->getDefault () != null) {
				if ($this->getType () == 'timestamp') {
					$ddl .= " DEFAULT " . $this->getDefault () . "";
				} else {
					$ddl .= " DEFAULT '" . $this->getDefault () . "'";
				}
			}
			$ddl .= " COMMENT '" . $this->getComment () . "'";
			$ddl .= " COMMENT '" . $this->getComment () . "'";
			$ddl .= $this->after ? " AFTER " . $this->after : "";

			return $ddl;
	}


	public function getPrev() {
		return $this->prev;
	}

	public function setPrev($prev) {
		$this->prev = $prev;
	}

	public function getNext() {
		return $this->next;
	}

	public function setNext($next) {
		$this->next = $next;
	}


	/**
	 *
	 * @return $after
	 */
	public function getAfter() : string {
		return $this->after;
	}
	
	/**
	 *
	 * @param $after
	 */
	public function setAfter(string $after) {
		$this->after = $after;
	}

	/**
	 *
	 * @return $type
	 */
	public function getType() : string {
		return $this->type;
	}
	
	/**
	 *
	 * @param $type
	 */
	public function setType(string $type) {
		$this->type = $type;
	}
	
	public function getPk() : bool {
		return $this->pk;
	}

	public function getService() : bool {
		return $this->service;
	}

	public function getNull() : bool {
		return $this->null;
	}

	public function getDefault() {
		return $this->default;
	}

	public function setPk(bool $pk) : void {
		$this->pk = $pk;
	}

	public function setService(bool $service) : void{
		$this->service = $service;
	}

	public function setNull(bool $null) : void {
		$this->null = $null;
	}

	public function setDefault(string $default) : void {
		$this->default = $default;
	}

	public function getDdl() : string {
		return $this->buildDdl();
	}

	public function cloneField() : Field {
		$field = new Field($this->name, $this->comment, $this->type);
		$field->ddl = $this->ddl;
		$field->pk = $this->pk;
		$field->service = $this->service;
		$field->null = $this->null;
		$field->default = $this->default;
		return $field;
	}

	public function __toString() {
		$isNull =  $this->null ? 'is_nullable=YES' : 'is_nullable=NO';
		$type = strtolower($this->type);
		if($type=='list' or $type=='boolean'){
				$type = 'int';
		}
		return sprintf("%s-%s-%s-%s", 
					strtolower($this->name), $type, $this->comment, $isNull);
	}
	
	
}