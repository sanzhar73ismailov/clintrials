<?php
declare ( strict_types = 1 );
namespace clintrials\admin\metadata;

class Field extends MetaGeneral {
	private $type; //date|float|text|varchar|int
	private $pk = false;
	private $service = false; // служенбное поле (типа user_insert или user_update, которое в каждой таблице есть)
	private $null = false; // null is possible
	private $default = null;
	
	public function __construct(string $name, string $comment, string $type){
		$this->name = $name;
		$this->comment = $comment;
		$this->type = $type;
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

	public function cloneField() : Field {
		$field = new Field($this->name, $this->comment, $this->type);
		$field->ddl = $this->ddl;
		$field->pk = $this->pk;
		$field->service = $this->service;
		$field->null = $this->null;
		$field->default = $this->default;
		return $field;
	}
	
	
}