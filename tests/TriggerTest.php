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

    public function setUp() {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->metadataCreator = new MetadataCreator ( ClinTrialsTestHelper::TEST_XML );
		$this->createDb ();
		$this->createTablesAndTriggers ();
	}

	public function createDb() : void {
		$this->logger->debug ( "START" );
		$this->db = $this->metadataCreator->getDb ();
		$ddlExecutor = new DdlExecutor ( $this->db );
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
		$ddlExecutor = new DdlExecutor ( $this->db );
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
    		$this->logger->debug ( "index=$index" );
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

    private function insertRowToTable(Table $table){
		$ddlExecutor = new DdlExecutor ( $this->db );
		$randPatId = rand(1,100);
		$randVisitId = rand(100,500);
		$query = $this->buildInsertRow($table);;
        return $ddlExecutor->runSql($query);
	}

	public function testTest() : void {
		$this->logger->debug ( "START" );
		$db = $this->db;
		$tables = $db->getTables ();
		for ($i=0; $i < 10; $i++) { 
			$insertQuery = $this->buildInsertRow($tables[0]);
			$this->assertStringMatchesFormat("insert into %s(%s", $insertQuery);
			$this->logger->debug ( '$insertQuery=' . $insertQuery );
			$resInsert = $this->insertRowToTable($tables[0]);
			$this->assertTrue($resInsert);
		}
		
		
		foreach ( $tables as $table ) {

		}
		
		$this->assertTrue(1==1);
		$this->logger->debug ( "FINISH" );

	}
}