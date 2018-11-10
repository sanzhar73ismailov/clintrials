<?php
declare ( strict_types = 1 );
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\ { 
	Table,
	TableJrnl,
	Trigger
};

class TriggerTest extends TestCase{
	private $logger;
	private $metadataCreator;
	private $db;
	private $ddlExecutor;

    public function setUp() {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->metadataCreator = new MetadataCreator ( ClinTrialsTestHelper::TEST_XML );
		$this->ddlExecutor = new DdlExecutor ( $this->metadataCreator->getDb () );
		$this->createDb ();
		$this->createTablesAndTriggers ();
		
	}

	public function createDb() : void {
		$this->logger->debug ( "START" );
		$this->db = $this->metadataCreator->getDb ();
		$ddlExecutor = $this->ddlExecutor;
		$this->logger->debug ( "\$this->metadataCreator->getDb()->getName()=" . $this->db->getName () );
		if ($ddlExecutor->dbExists ()) {
			$ddlExecutor->dropDb ();
		}
		$this->assertFalse ( $ddlExecutor->dbExists () );
		$this->assertTrue ( $ddlExecutor->createDb () );
		$this->logger->debug ( "FINISH" );
	}

	public function createTablesAndTriggers() : void {
		$this->logger->debug ( "START" );
		$ddlExecutor = $this->ddlExecutor;
		$db = $this->db;
		$tables = $db->getTables ();
		foreach ( $tables as $table ) {
			if (0) {
				$table = new Table ();
			}
			$this->assertTrue ( $ddlExecutor->createTable ( $table ) );
			$this->assertTrue ( $ddlExecutor->tableExists ( $table->getName () ) );
			
			$this->assertTrue ( $ddlExecutor->createTableJrnl ( $table ) );
			$this->assertTrue ( $ddlExecutor->tableExists ( $table->getTableJrnj ()->getName () ) );
			
			$this->assertFalse ( $ddlExecutor->triggerExists ( $table->getTriggerInsert () ) );
			$this->assertTrue ( $ddlExecutor->createTrigger ( $table->getTriggerInsert () ) );
			$this->assertTrue ( $ddlExecutor->triggerExists ( $table->getTriggerInsert () ) );
			
			$this->assertFalse ( $ddlExecutor->triggerExists ( $table->getTriggerUpdate () ) );
			$this->assertTrue ( $ddlExecutor->createTrigger ( $table->getTriggerUpdate () ) );
			$this->assertTrue ( $ddlExecutor->triggerExists ( $table->getTriggerUpdate () ) );
		}
		
		$this->logger->debug ( "FINISH" );
	}

	private function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

    private function buildInsertRow (Table $table) : string {
    	$query = "insert into " . $table->getName() . "(";
    	$fields = $table->getFields();
    	foreach ($fields as $index=>$field) {
    		if($field->getPk()) {
    			continue;
    		}
    		$query .= $field->getName();
    		//$this->logger->debug ( "index=$index" );
    		$query .=  ($index+1) < count($fields) ? ',' : ''; 
    	}
    	$query .=") VALUE (";

    	foreach ($fields as $index=>$field) {
    		if($field->getPk()) {
    			continue;
    		}
    		$val = "";
    		switch ($field->getType ()) {
				case "date" :
					$val .= sprintf("%s-%s-%s", rand(1900,2018),rand(1,12),rand(1,28));
					break;
				case "float" :
					$val .= sprintf("%s.%s", rand(1,200),rand(1,9));
					break;
				case "text" :
					$val .= $this->generateRandomString(100) ;
					break;
				case "varchar" :
					$val .= $this->generateRandomString(50) ;
					break;
				case "timestamp" :
					$val .= date('Y-m-d H:i:s', strtotime('now'));
					break;
				case "int" :
				case "integer" :
				case "list" :
				case "boolean" :
					$val .= rand(1,200);
					break;
				default :
					throw new Exception ( "type of field is unknown: " . $field->getType () );
			}
    		$query .= "'" . $val . "'";
    		$query .=  ($index+1) < count($fields) ? ',' : ''; 
    	}
    	$query .=")";
    	return $query;
    }	

    private function buildUpdateRow (Table $table, $id) : string {
    	$query = "update " . $table->getName() . " set ";
    	$fields = $table->getFields();
    	foreach ($fields as $index=>$field) {
    		if($field->getPk()) {
    			continue;
    		}
    		//$query .= $field->getName();
    		$val = "";
    		switch ($field->getType ()) {
				case "date" :
					$val .= sprintf("%s-%s-%s", rand(1900,2018),rand(1,12),rand(1,28));
					break;
				case "float" :
					$val .= sprintf("%s.%s", rand(1,200),rand(1,9));
					break;
				case "text" :
					$val .= $this->generateRandomString(100) ;
					break;
				case "varchar" :
					$val .= $this->generateRandomString(50) ;
					break;
				case "timestamp" :
					$val .= date('Y-m-d H:i:s', strtotime('now'));
					break;
				case "int" :
				case "integer" :
				case "list" :
				case "boolean" :
					$val .= rand(1,100000);
					break;
				default :
					throw new Exception ( "type of field is unknown: " . $field->getType () );
			}
    		$query .= $field->getName() . "='" . $val . "'";
    		$query .=  ($index+1) < count($fields) ? ',' : ''; 
    	}
    	$query .=" where id='$id'";
    	return $query;
    }	

    private function insertRowToTable(Table $table){
		
		$randPatId = rand(1,100);
		$randVisitId = rand(100,500);
		$query = $this->buildInsertRow($table);;
        return $this->ddlExecutor->insertSql($query);
	}

	public function t_estInsertTriggers() : void {
		$this->logger->debug ( "START" );
		$db = $this->db;
		$tables = $db->getTables ();
		foreach ( $tables as $table ) {
			for ($i=0; $i < 10; $i++) { 
				//$table = $tables[0];
				$insertQuery = $this->buildInsertRow($table);
				$this->assertStringMatchesFormat("insert into %s(%s", $insertQuery);
				$this->logger->debug ( '$insertQuery=' . $insertQuery );
				$insertId = $this->insertRowToTable($table);

				$this->assertTrue($insertId > 0);

				$row = $this->ddlExecutor->getRowById($table->getName(), 'id', $insertId);
				$this->assertNotNull($row);
				$this->assertTrue(is_array($row));
				$this->assertGreaterThan(0, $row);

		        $rowJrnl = $this->ddlExecutor->getLastRowFromJrnlById($table->getTableJrnj()->getName(), $insertId);
		        $this->assertNotNull($rowJrnl);
		        $this->assertEquals(1, $rowJrnl['insert_ind']);
				$this->assertTrue(is_array($rowJrnl));
				$this->assertGreaterThan(0, $rowJrnl);
				foreach ($row as $key => $value) {
					$this->assertEquals($value, $rowJrnl[$key]);
					//$this->logger->debug ( " $key => $value" );
				}
			}
		}
		$this->logger->debug ( "FINISH" );
	}

	public function t_estUpdateTriggers() : void {
		$this->logger->debug ( "START" );
		$db = $this->db;
		$tables = $db->getTables ();
		foreach ( $tables as $table ) {
			for ($i=0; $i < 10; $i++) { 
				
				$insertQuery = $this->buildInsertRow($table);
				$insertId = $this->insertRowToTable($table);
				$this->assertTrue($insertId > 0);

				$row = $this->ddlExecutor->getRowById($table->getName(), 'id', $insertId);
				$this->assertNotNull($row);
				$this->assertTrue(is_array($row));
				$this->assertGreaterThan(0, $row);
				
				
				$updateQuery = $this->buildUpdateRow($table, $insertId);
				$this->logger->debug ( '$updateQuery=' . $updateQuery );
				$this->assertStringMatchesFormat("update %s set %s where id='%i'", $updateQuery);
				
				$this->assertTrue($this->ddlExecutor->runSql($updateQuery));

				$row = $this->ddlExecutor->getRowById($table->getName(), 'id', $insertId);
				$this->assertNotNull($row);
				$this->assertTrue(is_array($row));
				$this->assertGreaterThan(0, $row);

				$rowJrnl = $this->ddlExecutor->getLastRowFromJrnlById($table->getTableJrnj()->getName(), $insertId);
		        $this->assertNotNull($rowJrnl);
		        $this->assertEquals(0, $rowJrnl['insert_ind']);
				$this->assertTrue(is_array($rowJrnl));
				$this->assertGreaterThan(0, $rowJrnl);
				foreach ($row as $key => $value) {
					$this->assertEquals($value, $rowJrnl[$key]);
					//$this->logger->debug ( " $key => $value" );
				}

				/*

                $updateResult = $this->ddlExecutor->updateValue($table->getName(), {''}, $insertId);
                updateValue(string $table_name, array $parameters, array $id_param) {


		        $rowJrnl = $this->ddlExecutor->getLastRowFromJrnlById($table->getTableJrnj()->getName(), $insertId);
		        $this->assertNotNull($rowJrnl);
		        $this->assertEquals(1, $rowJrnl['insert_ind']);
				$this->assertTrue(is_array($rowJrnl));
				$this->assertGreaterThan(0, $rowJrnl);
				foreach ($row as $key => $value) {
					$this->assertEquals($value, $rowJrnl[$key]);
					//$this->logger->debug ( " $key => $value" );
				}

				*/
			}
		}
		
		$this->assertTrue(1==1);
		$this->logger->debug ( "FINISH" );

	}

	public function testUpdateTriggers() : void {
		$this->logger->debug ( "START" );
		$this->assertTrue(true);
		$tables = $this->db->getTables ();
		foreach ( $tables as $table ) {
			$this->checkTrigger($table->getTriggerInsert());
			$this->checkTrigger($table->getTriggerUpdate());
		
		}
		$this->logger->debug ( "FINISH" );
	}

	private function checkTrigger($trigger) : void {
		    $triggerName = $trigger->getName();
			$this->logger->debug ( "triggerName = " . $triggerName );
			$this->assertNotNull($triggerName);
			$statement = $this->ddlExecutor->getTriggerStatementByName($triggerName);
			$this->logger->debug ( "Statement = " . $statement );
			$this->assertNotEmpty($statement);
			$this->assertNotNull($statement);
			$this->logger->debug ('ddl: ' .  $trigger->getDdl() );

			$this->logger->debug ( "Statement substr = " . $this->getBeginEndSubstr($statement) );
			$ddlTrigSubstr = $this->getBeginEndSubstr($trigger->getDdl());
			$this->logger->debug ( "Ddl substr = " . $ddlTrigSubstr );

			$this->assertEquals($ddlTrigSubstr, $statement);
	}

	private function getBeginEndSubstr($str){
		$pos = strpos($str, "BEGIN");
		return trim(substr($str, $pos));
	}

}