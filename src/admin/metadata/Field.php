<?php

namespace clintrials\admin\metadata;

class Field extends MetaGeneral {
	private $type; //date|float|text|varchar|int
	private $pk = false;
	private $service = false; // служенбное поле (типа user_insert или user_update, которое в каждой таблице есть)
	private $null = false; // null is possible
	private $default = null;
	
	public function __construct($name, $comment, $type){
		$this->name = $name;
		$this->comment = $comment;
		$this->type = $type;
	}
	
	/**
	 *
	 * @return $type
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 *
	 * @param $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getPk() {
		return $this->pk;
	}

	public function getService() {
		return $this->service;
	}

	public function getNull() {
		return $this->null;
	}

	public function getDefault() {
		return $this->default;
	}

	public function setPk($pk) {
		$this->pk = $pk;
	}

	public function setService($service) {
		$this->service = $service;
	}

	public function setNull($null) {
		$this->null = $null;
	}

	public function setDefault($default) {
		$this->default = $default;
	}

	public function cloneField(){
		$field = new Field($this->name, $this->comment, $this->type);
		$field->ddl = $this->ddl;
		$field->pk = $this->pk;
		$field->service = $this->service;
		$field->null = $this->null;
		$field->default = $this->default;
		return $field;
	}
	
	
}