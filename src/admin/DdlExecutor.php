<?php
declare ( strict_types = 1 );
namespace clintrials\admin;

use Logger;
use PDO;
use PDOException;
use clintrials\admin\metadata\ {
	DbSchema,
	Table,
	Trigger,
	Field
};
use clintrials\admin\validation\ {
	TableMetaFromDb,
    TableValidation,
    FieldMetaFromDb,
    ValidationResult
};

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
	function createDb() : bool {
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
	
	function dropDb() : bool {
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
	
	function dbExists() : bool {
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

	//select count(*) from information_schema.TABLES where TABLE_SCHEMA = <database-name>
	function getTablesNumber() : int {
		$this->logger->trace("START");
		$result = 0;
		try {
			$query = "SELECT count(1) num FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->db->getName() . "'";
			$stmt = $this->conn->prepare ( $query );
			$stmt->execute ();
			$row = $stmt->fetch( PDO::FETCH_ASSOC );
			$result = (int) $row[num];
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
			throw $e;
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	function tableJournalExists(string $table_name) : bool {
		$this->logger->trace("START");
		$result = $this->tableExists($table_name . "_jrnl");
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function tableExists(string $table_name) : bool {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->logger->trace("***** 1");
			$this->conn->exec('USE ' . $this->db->getName());
			$this->logger->trace("***** 2");
			$query = "show tables like '" . $table_name . "'";
			$this->logger->trace("***** 3");
			$this->logger->trace("\query=" . $query);
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
	
	function getRowsCount(string $table_name) : int {
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
	
	function triggerExists(Trigger $trigger) : bool {
		return $this->triggerExistsByName($trigger->getName());
	}
	
	function triggerExistsByName($trigger_name) : bool {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			//show triggers where `Trigger` like '%ns%' and `table`='T1'
			$query = "show triggers where `Trigger` = '" . $trigger_name . "'";
			$this->logger->trace("query=" . $query);
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

	function getTriggerStatementByName($trigger_name) : string {
		$this->logger->trace("START");
		$result = "";
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			//show triggers where `Trigger` like '%ns%' and `table`='T1'
			$query = "show triggers where `Trigger` = '" . $trigger_name . "'";
			$this->logger->trace("query=" . $query);
			$stmt = $this->conn->prepare ( $query );
			$stmt->execute ();
			if ($row = $stmt->fetch ( PDO::FETCH_ASSOC )){
				$this->logger->trace("in if !!!!!!!!!!!!: ");
				$this->logger->trace("row=" . var_export($row, true));
				$result = $row['Statement'];
			}else{
				$this->logger->trace("not in if ------");
			}
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function createTrigger(Trigger $trigger) : bool {
		$this->logger->trace("START");
		$result = $this->runSql($trigger->getDdl());
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}
	
	function runSql($sql) : bool {
		$this->logger->trace("START");
		$result = false;
		try {
			$this->conn->exec('USE ' . $this->db->getName());
			$this->logger->trace("sql=" . $sql);
			$result = $this->conn->exec ( $sql ) !== false;
		} catch ( PDOException $e ) {
			$this->logger->error($e->getMessage(), $e);
		}
		$this->logger->trace("FINISH, return " . $result);
		return $result;
	}

	function insertSql($sql) : int {
		$this->logger->trace("START");
		$lastInsertId = 0;
		$this->conn->exec('USE ' . $this->db->getName());
		$stmt = $this->conn->prepare($sql); 
	    try { 
	        $this->conn->beginTransaction(); 
	        $stmt->execute( ); 
	        $result = (int) $this->conn->lastInsertId(); 
	        $this->conn->commit(); 
	        $lastInsertId = $result;
	    } catch(PDOExecption $e) { 
	        $this->conn->rollback(); 
	        $this->logger->error($e->getMessage(), $e);
	    } 
		$this->logger->trace("FINISH, return lastInsertId = " . $lastInsertId);
		return $lastInsertId;
	}
	
	function createTable(Table $table) : bool {
		return $this->createTableFromDdl($table->getDdl());
	}
	
	function createTableJrnl(Table $table) : bool {
		$this->logger->trace("START ::: " . $table->getTableJrnl()->getName());
		$this->logger->trace("Jrnl DDL ::: " . $table->getTableJrnl()->getDdl());
		return $this->createTableFromDdl($table->getTableJrnl()->getDdl());
	}
	
	function createTableFromDdl(string $ddl) : bool{
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
	
	function createDbWhole() : void {
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
	
	function getTableMetaFromDb(Table $table) : TableMetaFromDb {
		$this->logger->trace("START");
		$resultValidation = null;
		$tableMetaFromDb = new TableMetaFromDb();
		$tableMetaFromDb->columns = $this->getColumnsFromDb($this->db->getName(), $table->getName());
		return $tableMetaFromDb;
	}
	
	function getColumnsFromDb($dbName, $tableName){
		$this->logger->trace("START");
		$columns = array();
		$tableMetaFromDb = new TableMetaFromDb();
		try {
			$query = "SELECT t.COLUMN_NAME, t.DATA_TYPE, t.COLUMN_COMMENT, t.IS_NULLABLE " .
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
				$columns [] = new FieldMetaFromDb ( $row ['COLUMN_NAME'], $row ['COLUMN_COMMENT'], $row ['DATA_TYPE'], $row ['IS_NULLABLE'] == 'YES' );
			}
		} catch ( PDOException $e ) {
			$this->logger->error ( "error", $e );
		}
		$this->logger->trace ( "FINISH, return \$columns" . var_export ( $columns, true ) );
		return $columns;
	}


    function getColumnFromDb(string $dbName, string $tableName, string $columnName) {
       $columns = $this->getColumnsFromDb($dbName, $tableName);
       foreach ($columns as $column) {
       	if($column->column_name == $columnName){
       		return $column;
       	}
       }
       return null;
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
		$bc_table_jrnl_name = $this->createBackupTable($table->getTableJrnl());
		if($bc_table_jrnl_name == null){
			throw new \Exception("backup table " . $bc_table_jrnl_name . "is not created");
		}
		if (! $this->copyDataToBackupTable($table->getTableJrnl(), $bc_table_jrnl_name)){
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

	function getRowById(string $table_name, string $pk_columns_name, $id) {
		$query = "SELECT * FROM $table_name WHERE $pk_columns_name = :id ";
		$parameters['id'] = $id;
		$this->logger->trace("id=" . $id);
		return $this->getSingleRow($query, $parameters);
	}

	function getLastRowFromJrnlById(string $table_name, $main_table_id) {
		$query = "SELECT * FROM $table_name WHERE id = :id order by jrnl_id desc";
		$parameters['id'] = $main_table_id;
		$this->logger->trace("id=" . $id);
		return $this->getSingleRow($query, $parameters);
	}

	function getSingleRow(string $query, array $parameters) {
		$this->logger->trace("START");
		$this->logger->trace("query: $query");
		$this->logger->trace("parameters: " . var_export($parameters, true));
		$result = false;
		try {
			$stmt = $this->conn->prepare($query);
			$stmt->execute($parameters);
			$result = $stmt->fetch (PDO::FETCH_ASSOC ) ;
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
			throw $e;
		}
		$this->logger->trace("FINISH, return " . var_export($result, true));
		return $result;
	}

	function updateValue(string $table_name, array $parameters, array $id_param) {
		$this->logger->trace("START");
		$result = false;
		$query = "UPDATE $table_name SET ";
		$i = 0;
		foreach ($parameters as $key => $value) {
			$query .= sprintf("%s=':%s'", $key, $key);
			if ($i < (count($parameters) - 1)) {
				$query .= ',';
			}
			$i++;
		}
		$this->logger->trace("query: $query");
		$this->logger->trace("parameters: " . var_export($parameters, true));
		try {
			$stmt = $this->conn->prepare($query);
			$result = $stmt->execute($parameters);
		} catch ( PDOException $e ) {
			$this->logger->error("error", $e);
			throw $e;
		}
		$this->logger->trace("FINISH, return " . $result );
		return $result;
	}

	public function addColumn(string $table_name, Field $field, string $after_column) : bool {
		$this->logger->trace("START");
		$result = false;
		$query = sprintf("ALTER TABLE %s ADD %s AFTER %s", $table_name, $field->getDdl(), $after_column);
		$this->logger->trace("query=" . $query );
		$result = $this->runSql($query);
		$this->logger->trace("FINISH, return " . $result );
		return $result;
	}

	public function changeColumn(string $table_name, string $column, Field $field) : bool {
		$this->logger->trace("START");
		$result = false;
		$this->logger->trace("FINISH, return " . $result );
		return $result;
	}

	public function dropColumn(string $table_name, string $column) : bool {
		$this->logger->trace("START");
		$result = false;
		$this->logger->trace("FINISH, return " . $result );
		return $result;
	}


}
?>