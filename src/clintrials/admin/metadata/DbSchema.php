<?php

namespace clintrials\admin\metadata;

class DbSchema extends MetaGeneral {
	private $tables = array ();
	/**
	 * @return the $tables
	 */
	public function getTables() {
		return $this->tables;
	}

	/**
	 * @param multitype: $tables
	 */
	public function setTables($tables) {
		$this->tables = $tables;
	}

	
	
}

