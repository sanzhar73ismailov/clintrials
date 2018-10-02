<?php

namespace clintrials\admin;

use clintrials\admin\metadata\Table;
use Logger;
use PDO;
use PDOException;
use clintrials\admin\metadata\DbSchema;
use phpDocumentor\Reflection\Types\String_;

//Logger::configure("configs/" . LOG_SET_FILE);
//include_once 'config.php';
//include_once 'DbSchema.php';
class DdlExecutor {
	private $logger;
	private $db;
	private $conn;
	
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
			$this->conn->exec ( $this->db->getDdl() );
			$result = true;
		} catch ( PDOException $e ) {
			//echo $sql . "<br>" . $e->getMessage ();
			$this->logger->error("error", $e);
			throw $e;
		}
		$this->logger->trace("FINISH, returned " . $result);
		return $result;
	}
	
	function dropDb() {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec ( "drop database " . $this->db->getName() );
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
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	//show tables like "clin_test_lab"
	function tableExists($table_name) {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			$query = "show tables like '" . $table_name . "'";
			$stmt = $this->conn->prepare ( $query );
			$stmt->execute ();
			$row = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			$this->logger->trace("\$stmt->rowCount ()=" . $stmt->rowCount ());
			$result = $stmt->rowCount () > 0;
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function runSql($sql) {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			$result = $this->conn->exec ( $sql ) !== false;
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function createTable(Table $table) {
		return $this->createTableFromDdl($table->getDdl());
	}
	
	function createTableJrnl(Table $table) {
		return $this->createTableFromDdl($table->getDdlJrnl());
	}
	
	function createTableFromDdl($ddl) {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			$this->logger->trace("table ddl: " . $ddl);
			$this->conn->exec ($ddl);
			$result = true;
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
			$result = false;
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
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