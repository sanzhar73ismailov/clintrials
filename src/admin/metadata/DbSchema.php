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
	
	public function getWholeDdl() {
		$ddlWhole = "";
		$ddlWhole .= $this->ddl . PHP_EOL;
		foreach ( $this->tables as $table ) {
			$ddlWhole .= $table->getDdl() . PHP_EOL . PHP_EOL;
		}
		return $ddlWhole;
	}
	
	public function addTable($table) {
		$this->tables[] = $table;
	}

}
