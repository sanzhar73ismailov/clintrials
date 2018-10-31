<?php

namespace clintrials\admin;

use clintrials\admin\metadata\Table;
use Logger;
use PDO;
use PDOException;
use clintrials\admin\metadata\DbSchema;
//use phpDocumentor\Reflection\Types\String_;
use clintrials\admin\metadata\Trigger;
use clintrials\admin\validation\TableMetaFromDb;
use clintrials\admin\validation\TableValidation;
use clintrials\admin\validation\FieldMetaFromDb;

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
	
	function tableJournalExists($table_name) {
		$this->logger->trace("START");
		$result = $this->tableExists($table_name . "_jrnl");
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
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
	
	function getRowsCount($table_name) {
		$this->logger->trace("START");
		$result = 0;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			$query = "select count(1) from " . $table_name . "";
			$stmt = $this->conn->prepare ( $query );
			$stmt->execute ();
			//$row = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			//$this->logger->trace("\$stmt->rowCount ()=" . $stmt->rowCount ());
			$result = $stmt->rowCount ();
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function triggerExists(Trigger $trigger) {
		return $this->triggerExistsByName($trigger->getName());
	}
	
	function triggerExistsByName($trigger_name) {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			//show triggers where `Trigger` like '%ns%' and `table`='T1'
			$query = "show triggers where `Trigger` = '" . $trigger_name . "'";
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
	
	function createTrigger(Trigger $trigger) {
		$this->logger->trace("START");
		$result = $this->runSql($trigger->getDdl());
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
			$this->logger->error($e->getMessage(), $e);
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function createTable(Table $table) {
		return $this->createTableFromDdl($table->getDdl());
	}
	
	function createTableJrnl(Table $table) {
		return $this->createTableFromDdl($table->getTableJrnj()->getDdl());
	}
	
	function createTableFromDdl($ddl) {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			$this->logger->trace("table ddl: " . PHP_EOL . $ddl);
			$this->conn->exec ($ddl);
			$result = true;
		} catch ( PDOException $e ) {
			$this->logger->error("error: " . $e->getMessage(), $e);
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
	
	/**
	 * Checks if table's metadata from XML and in DB are same
	 */
	function tableMatched(Table $table){
		$this->logger->trace("START");
		$resultValidation = null;
		$tableMetaFromDb = new TableMetaFromDb();
		$tableMetaFromDb->columns = $this->getColumnsFromDb($this->db->getName(), $table->getName());
		try {
			$tableValidation = new TableValidation($table, $tableMetaFromDb);
			$resultValidation = $tableValidation->validate();
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
		}
		//$this->logger->trace("FINISH, return \$result" . var_export($resultValidation, true));
		return $resultValidation;
	}
	
	function getColumnsFromDb($dbName, $tableName){
		$this->logger->trace("START");
		$columns = array();
		$tableMetaFromDb = new TableMetaFromDb();
		try {
			$query = "SELECT t.COLUMN_NAME, t.DATA_TYPE, t.COLUMN_COMMENT " .
					" FROM INFORMATION_SCHEMA.columns t " .
					" WHERE t.table_schema=:table_schema " .
					" AND t.table_name=:table_name " .
					" ORDER BY t.ordinal_position";
			
			$parameters['table_schema'] = $dbName;
			$parameters['table_name'] = $tableName;
			$this->logger->trace("\$parameters=" . var_export($parameters, true));
			$stmt = $this->conn->prepare($query);
			$stmt->execute($parameters);
			//$result = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			while ($row = $stmt->fetch () ) {
				$columns [] = new FieldMetaFromDb ( $row ['COLUMN_NAME'], $row ['COLUMN_COMMENT'], $row ['DATA_TYPE'] );
			}
		} catch ( PDOException $e ) {
			$this->logger->error ( "error", $e );
		}
		$this->logger->trace ( "FINISH, return \$columns" . var_export ( $columns, true ) );
		return $columns;
	}
	function backupTable(Table $table) {
		$result = false;
		// if(1) return 1;
		$bc_table_name = $this->createBackupTable ( $table );
		if ($bc_table_name == null ) {
			throw new \Exception ( "backup table " . $bc_table_name . "is not created" );
		}
		if (! $this->copyDataToBackupTable($table, $bc_table_name)){
			throw new \Exception("data into backup table " . $bc_table_name . "is not copied");
		}
		$bc_table_jrnl_name = $this->createBackupTable($table->getTableJrnj());
		if($bc_table_jrnl_name == null){
			throw new \Exception("backup table " . $bc_table_jrnl_name . "is not created");
		}
		if (! $this->copyDataToBackupTable($table->getTableJrnj(), $bc_table_jrnl_name)){
			throw new \Exception("data into backup table " . $bc_table_jrnl_name . "is not copied");
		}
		$result = true;
		return $result;
	}
	/**
	 * 
	 * @param Table $table
	 * @return string - if success - name of created table,  null if tabel is not created 
	 */
	function createBackupTable (Table $table) {
		$this->logger->trace("START");
		$result = null;
		$bc_table_name = "bc_" . $table->getName() . "_" . date('ymd_His');
		$query = sprintf("create table %s like %s", $bc_table_name, $table->getName());
		if($this->runSql($query)){
			$result = $bc_table_name;
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function copyDataToBackupTable (Table $table, $bc_table_name) {
		$this->logger->trace("START");
		$result = false;
		$bc_table_name = "bc_" . $table->getName() . "_" . date('ymd_His');
		$query = sprintf("INSERT INTO %s SELECT * FROM %s", $bc_table_name, $table->getName());
		$result = $this->runSql($query);
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	

}

?>