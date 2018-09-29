<?php

namespace clintrials\admin\metadata;

class Field extends MetaGeneral {
	private $type;
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
}