<?php

namespace clintrials\admin\metadata;

class Table extends MetaGeneral {
	private $fields = array ();
	private $ddlJrnl;
	
	public function addField($field) {
		$this->fields[] = $field;
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function setFields($fields) {
		$this->fields = $fields;
	}
	
	public function getDdlJrnl() {
		return $this->ddlJrnl;
	}
	
	public function getNameJrnl() {
		return $this->name . "_jrnl";
	}

	public function setDdlJrnl($ddlJrnl) {
		$this->ddlJrnl = $ddlJrnl;
	}

	
	
}

