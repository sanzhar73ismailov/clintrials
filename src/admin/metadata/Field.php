<?php

namespace clintrials\admin\metadata;

class Field extends MetaGeneral {
	private $type; //date|float|text|varchar|int
	private $pk = false;
	private $service = false; // служенбное поле (типа user_insert или user_update, которое в каждой таблице есть)
	private $null = false; // null is possible
	private $default = null;
	
	
	
	/*
	                    case "date":
							$ddl .= " DATE";
							break;
						case "float":
							$ddl .= " float(11,2)";
							break;
						case "text":
							$ddl .= " text";
							break;
						case "varchar":
							$ddl .= " varchar(50)";
							break;
						default:
							$ddl .= " INTEGER(11)";
	 * */
	
	/*
	 * $ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
			$ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
			$ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
			$ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
			$ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
	 * 
	 * */
	
	//public function __construct(){
		
	//}
	
	public function __construct($name, $comment, $type){
		$this->name = $name;
		$this->comment = $comment;
		$this->type = $type;
	}
	
	/**
	 *
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 *
	 * @param field_type $type
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