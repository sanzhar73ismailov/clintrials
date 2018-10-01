<?php

namespace clintrials\admin\metadata;

class Table extends MetaGeneral {
	private $fields = array ();
	/**
	 *
	 * @return the $fields
	 */
	public function getFields() {
		return $this->fields;
	}
	
	/**
	 *
	 * @param multitype: $fields
	 */
	public function setFields($fields) {
		$this->fields = $fields;
	}
	
	public function addField($field) {
		$this->fields[] = $field;
	}
}

