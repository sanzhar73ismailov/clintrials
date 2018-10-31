<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;

class CreateBackupTest extends TestCase {
	private $logger;
	private $metadataCreator;
	private $db;

	public function __construct() {
		$this->logger = Logger::getLogger ( __CLASS__ );
	}
	public function setUp() {
		$this->metadataCreator = new MetadataCreator ( "tests/clintrials_test.xml" );
		$this->createDb ();
		$this->createTablesAndTriggers ();
		
	}
	public function createDb() {
		$this->db = $this->metadataCreator->getDb ();
		$ddlExecutor = new DdlExecutor ( $this->db );
		$this->logger->debug ( "\$this->metadataCreator->getDb()->getName()=" . $this->db->getName () );
		if ($ddlExecutor->dbExists ()) {
			$ddlExecutor->dropDb ();
		}
		$this->assertFalse ( $ddlExecutor->dbExists () );
		$this->assertTrue ( $ddlExecutor->createDb () );
	}
	public function createTablesAndTriggers() {
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
	
	private function insertRowTo_clin_test_patient(){
		$ddlExecutor = new DdlExecutor ( $this->db );
		$randPatId = rand(1,1000);
		$randVisitId = rand(1,1000);
		$query = "INSERT INTO   clin_test_patient( id, patient_id, visit_id, code, sex_id, doctor, checked, row_stat, user_insert, user_update)" 
                 ." VALUE (null, $randPatId, $randVisitId, 'sdsds', 2, 'doctor fio', 0, 1, 'user1', 'user1')";
        return $ddlExecutor->runSql($query);
	}
	
	
	public function testCreateBackup(){
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		
		$db = $this->db;
		$tables = $db->getTables ();
		$tablePatient = $this->db->getTable('clin_test_patient');
		$this->assertNotNull($tablePatient);
		$this->assertTrue($ddlExecutor->tableExists($tablePatient->getName()));
		
		//$this->assertTrue($this->insertRowTo_clin_test_patient());
		//$this->assertTrue($this->insertRowTo_clin_test_patient());
		//$this->assertTrue($this->insertRowTo_clin_test_patient());
		//$this->assertTrue($this->insertRowTo_clin_test_patient());
		
		$ddlExecutor->backupTable($tablePatient);
		
		$this->logger->debug ( "FINISH" );
	}
	
}