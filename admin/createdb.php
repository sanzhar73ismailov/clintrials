<?php
include_once 'config.php';
include_once 'metadata.php';
class DdlExecutor {
	private $db;
	private $conn;
	/*
	 * define("HOST", "localhost");
	 * define("DB_USER", "root");
	 * //define("DB_PASS", "elsieltc");
	 * define("DB_PASS", "");
	 * define("DB_NAME", "ovarian");
	 */
	function __construct($db) {
		$this->db = $db;
		try {
			$this->conn = new PDO ( "mysql:host=" . HOST, DB_USER, DB_PASS );
			// set the PDO error mode to exception
			$this->conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch ( PDOException $e ) {
			echo $sql . "<br>" . $e->getMessage ();
		}
	}
	function createDb() {
		try {
			$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $this->db->name . "';
			$this->conn->exec ( $this->db->ddl );
			return true;
		} catch ( PDOException $e ) {
			echo $sql . "<br>" . $e->getMessage ();
			return false;
		}
	}
	function createTable(Table $table) {
		try {
			$this->conn->exec ( $this->db->ddl );
			return true;
		} catch ( PDOException $e ) {
			echo $sql . "<br>" . $e->getMessage ();
			return false;
		}
	}
	function createDbWhole() {
		if($this->createDb()){
			echo "Database " . $this->db->name . " created successfully<br>";
		}else{
			return;
		}
		echo "Creating tables<br>";
		foreach ($this->db->tables as $table){
			if($this->createTable($table)){
				echo "Database $table->name created successfully<br>";
			}
		}
	}
}

?>