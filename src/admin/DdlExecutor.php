<?php

namespace clintrials\admin;

use clintrials\admin\metadata\Table;
use Logger;
use PDO;
use PDOException;
use clintrials\admin\metadata\DbSchema;

Logger::configure("configs/" . LOG_SET_FILE);




//include_once 'config.php';
//include_once 'DbSchema.php';
class DdlExecutor {
	private $logger;
	private $db;
	private $conn;
	
	/*
	 * define("HOST", "localhost");
	 * define("DB_USER", "root");
	 * //define("DB_PASS", "elsieltc");
	 * define("DB_PASS", "");
	 * define("DB_NAME", "ovarian");
	 */
	function __construct(DbSchema $db) {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->logger->trace("START");
		$this->db = $db;
		try {
			$this->conn = new PDO ( "mysql:host=" . HOST, DB_USER, DB_PASS );
			// set the PDO error mode to exception
			$this->conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
			throw $e;
		}
		$this->logger->trace("FINISH");
	}
	function createDb() {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec ( $this->db->ddl );
			$result = true;
		} catch ( PDOException $e ) {
			//echo $sql . "<br>" . $e->getMessage ();
			$this->logger->error("error", $e);
			throw $e;
		}
		$this->logger->trace("FINISH, returned " . $result);
		return $result;
	}
	function dbExists() {
		$this->logger->trace("START");
		$result = false;
		try {
			$query = "SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $this->db->getName() . "'";
			$stmt = $this->conn->prepare ( $query );
			$stmt->execute ();
			$row = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			$result = $stmt->rowCount () > 0;
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
			throw $e;
		}
		$this->logger->trace("FINISH, returned " . $result);
		return $result;
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
		if ($this->createDb ()) {
			echo "Database " . $this->db->name . " created successfully<br>";
		} else {
			return;
		}
		echo "Creating tables<br>";
		foreach ( $this->db->tables as $table ) {
			if ($this->createTable ( $table )) {
				echo "Database $table->name created successfully<br>";
			}
		}
	}
}

?>