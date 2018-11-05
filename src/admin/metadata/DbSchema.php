<?php
declare ( strict_types = 1 );
namespace clintrials\admin\metadata;

class DbSchema extends MetaGeneral {
	
	private $tables = array ();
	/**
	 * @return Table $tables
	 */
	public function getTables() : array {
		return $this->tables;
	}

	/**
	 * @param multitype: $tables
	 */
	public function setTables(array $tables) : void {
		$this->tables = $tables;
	}
	
	public function getWholeDdl() : string {
		$ddlWhole = $this->ddl . PHP_EOL;
		foreach ( $this->tables as $table ) {
			$ddlWhole .= $table->getDdl() . PHP_EOL . PHP_EOL;
		}
		return $ddlWhole;
	}
	
	public function addTable($table) {
		$this->tables[] = $table;
	}
	
	/**
	 * 
	 * @param $name - string name of table
	 * @return Table: obj if exists or null if not exists
	 */
	public function getTable(string $table_name) : Table {
		$table = null;
		foreach($this->tables as $t){
			if($t->getName() == $table_name){
				$table = $t;
				break;
			}
		}
		return $table;
	}

}

