<?php

namespace clintrials\admin\metadata;

use clintrials\admin\metadata\TableJrnl;

class Table extends MetaGeneral {
	protected $fields = array ();
	private $tableJrnj;
	// private $ddlJrnl;
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
	
	// public function getDdlJrnl() {
	// return $this->ddlJrnl;
	// }
	
	// public function getNameJrnl() {
	// return $this->name . "_jrnl";
	// }
	
	// public function setDdlJrnl($ddlJrnl) {
	// $this->ddlJrnl = $ddlJrnl;
	// }
}

