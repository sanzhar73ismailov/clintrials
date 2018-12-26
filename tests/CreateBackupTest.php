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
	private $patientVisitPairs = array(); // uniq set of patient and visit numbers

	public function setUp() {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->metadataCreator = new MetadataCreator ( ClinTrialsTestHelper::TEST_XML );
		$this->createDb ();
		$this->createTablesAndTriggers ();
		$this->fillPatientVisitPairs();
		
	}

	public function fillPatientVisitPairs(){
		for ($i=0; $i < 100; $i++) { 
			for ($j=0; $j < 100; $j++) {
				$this->patientVisitPairs[] = array($i, $j);
			}
		}
	}

    public function popPatientVisitPairs(){
		$toRet = $this->patientVisitPairs[0];
		array_splice($this->patientVisitPairs, 0, 1);
		return $toRet;
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
			$this->assertTrue ( $ddlExecutor->tableExists ( $table->getTableJrnl ()->getName () ) );
			
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
		$patientVisitNumbers = $this->popPatientVisitPairs();
		//$randPatId = rand(1,100);
		//$randVisitId = rand(100,500);
		$randPatId = $patientVisitNumbers[0];
		$randVisitId = $patientVisitNumbers[1];
		$query = "INSERT INTO   clin_test_patient( id, code, sex_id, doctor, checked, row_stat, user_insert, user_update)" 
                 ." VALUE (null, $randPatId, 2, 'doctor fio', 0, 1, 'user1', 'user1')";
        return $ddlExecutor->runSql($query);
	}
	
	public function testCreateBackup() : void{
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		
		$db = $this->db;
		$tables = $db->getTables ();
		$tablePatient = $this->db->getTable('clin_test_patient');
		$this->assertNotNull($tablePatient);
		$this->assertSame('clin_test_patient', $tablePatient->getName());
		$this->assertTrue($ddlExecutor->tableExists($tablePatient->getName()));
		
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		
		// copy table of patient
		$table_src = $tablePatient;
		$bc_table_name = $ddlExecutor->createBackupTable ( $table_src );
		$this->assertNotNull($bc_table_name);
		$this->assertTrue($ddlExecutor->tableExists($bc_table_name));
		
		$columns = $ddlExecutor->getColumnsFromDb($this->db->getName(), $bc_table_name);
		$this->assertTrue(count($columns) > 0);
		$this->assertTrue(count($columns) == count($table_src->getFields()));
		
		$this->assertTrue($ddlExecutor->copyDataToBackupTable($table_src, $bc_table_name));
		$this->assertTrue($ddlExecutor->getRowsCount($table_src->getName()) > 0);
		
		$this->assertEquals($ddlExecutor->getRowsCount($table_src->getName()), $ddlExecutor->getRowsCount($bc_table_name));
		
		// copy table of patient_jrnl
		$table_src = $tablePatient->getTableJrnl();
		$bc_table_name = $ddlExecutor->createBackupTable ( $table_src );
		$this->assertNotNull($bc_table_name);
		$this->assertTrue($ddlExecutor->tableExists($bc_table_name));
		
		$columns = $ddlExecutor->getColumnsFromDb($this->db->getName(), $bc_table_name);
		$this->assertTrue(count($columns) > 0);
		$this->assertTrue(count($columns) == count($table_src->getFields()));
		
		$this->assertTrue($ddlExecutor->copyDataToBackupTable($table_src, $bc_table_name));
		$this->assertTrue($ddlExecutor->getRowsCount($table_src->getName()) > 0);
		
		$this->assertEquals($ddlExecutor->getRowsCount($table_src->getName()), $ddlExecutor->getRowsCount($bc_table_name));
		
		
		$this->logger->debug ( "FINISH" );
	}
	
	public function testCreateBackupIfSomethingWrong(){
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		
		$db = $this->db;
		$tables = $db->getTables ();
		$tablePatient = $this->db->getTable('clin_test_patient');
		$this->assertNotNull($tablePatient);
		$this->assertTrue($ddlExecutor->tableExists($tablePatient->getName()));
		
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		$this->assertTrue($this->insertRowTo_clin_test_patient());
		
		// copy table of t123 that not exists
		$table_src = new Table();
		$table_src->setName("t123");
		$this->expectException(PDOException::class);
		$bc_table_name = $ddlExecutor->createBackupTable ( $table_src );
        

		//$this->assertNull($bc_table_name);
		//$this->assertFalse($ddlExecutor->tableExists($bc_table_name));
		//$this->assertFalse($ddlExecutor->copyDataToBackupTable($table_src, "t1_table_not_exists"));
	
		
		
		$this->logger->debug ( "FINISH" );
	}
	
}