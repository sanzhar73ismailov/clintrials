<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;
use clintrials\admin\validation\Validator;

class ValidatorTest extends TestCase {
	private $logger;
	private $metadataCreator;
	private $db;
	//private $validator;
	
	public function setUp() {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->metadataCreator = new MetadataCreator ( ClinTrialsTestHelper::TEST_XML );
		$this->createDb ();
		$this->createTablesAndTriggers ();

	}

	public function tearDown() {
		$this->metadataCreator = null;
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

	public function testValidateTableNotExists() {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		
        $validator = new Validator($ddlExecutor);
		$this->assertNotNull($validator);
		
		$notExistsTable = new Table();
		$notExistsTable->setName('some_table123');
		$validRes = $validator->validate($notExistsTable);
		$this->assertNotNull($validRes);
		$this->assertFalse($validRes->objectExists);
		$this->assertFalse($validRes->passed);
		$this->logger->debug ( "validRes->errors=" . var_export($validRes->errors, true) );
		$this->assertCount(1, $validRes->errors);
		$this->logger->debug ( "FINISH" );
	}
	
	public function testValidateGoodTables() {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		
		
		$tables = $this->db->getTables ();

		$this->assertTrue ( count ( $tables ) > 0 );
        
        $validator = new Validator($ddlExecutor);
		$this->assertNotNull($validator);
		$patientTable = $this->db->getTable('clin_test_patient');
		$this->assertNotNull($patientTable);
		
		$validRes = $validator->validate($patientTable);
		$this->assertNotNull($validRes);
		$this->assertTrue($validRes->objectExists);
		$this->assertTrue($validRes->passed);
		$this->logger->debug ( "validRes->errors=" . var_export($validRes->errors, true) );
		$this->assertCount(0, $validRes->errors);

		foreach ( $tables as $table ) {
			$validRes = $validator->validate($table);
			$this->assertNotNull($validRes);
			$this->assertTrue($validRes->objectExists);
			$this->assertTrue($validRes->passed);
			$this->logger->debug ( "validRes->errors=" . var_export($validRes->errors, true) );
			$this->assertCount(0, $validRes->errors);
		}
		$this->logger->debug ( "FINISH" );
	}

	public function testValidateColumnNameIsDifferent() {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		$table = $this->db->getTable('clin_test_lab');
		$validator = new Validator($ddlExecutor);

		$validRes = $validator->validate($table);
		$this->assertNotNull($table);
		$this->assertTrue($validRes->objectExists);
		$this->assertTrue($validRes->passed);
		$this->logger->debug ( "validRes->errors=" . var_export($validRes->errors, true) );
		$this->assertCount(0, $validRes->errors);



		$query = "ALTER TABLE `clin_test_lab` MODIFY COLUMN `visit_id` INTEGER(11) DEFAULT NULL COMMENT 'Визит' AFTER `id`";
		$this->assertTrue($ddlExecutor->runSql($query));
		$validRes = $validator->validate($table);
		$this->assertTrue($validRes->objectExists);
		$this->assertFalse($validRes->passed);
		$this->logger->debug ( "validRes->errors=" . var_export($validRes->errors, true) );
		$this->assertTrue(count($validRes->errors) > 0);
		$this->logger->debug ( "FINISH" );
	}

	
	public function testValidateOrderOfColumnIsDifferent() {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		$table = $this->db->getTable('clin_test_lab');
		$validator = new Validator($ddlExecutor);

		$validRes = $validator->validate($table);
		$this->assertNotNull($table);
		$this->assertTrue($validRes->objectExists);
		$this->assertTrue($validRes->passed);
		$this->logger->debug ( "validRes->errors=" . var_export($validRes->errors, true) );
		$this->assertCount(0, $validRes->errors);



		$query = "ALTER TABLE `clin_test_lab` MODIFY COLUMN `visit_id` INTEGER(11) DEFAULT NULL COMMENT 'Визит' AFTER `id`";
		$this->assertTrue($ddlExecutor->runSql($query));
		$validRes = $validator->validate($table);
		$this->assertTrue($validRes->objectExists);
		$this->assertFalse($validRes->passed);
		$this->logger->debug ( "validRes->errors=" . var_export($validRes->errors, true) );
		$this->assertTrue(count($validRes->errors) > 0);
		$this->logger->debug ( "FINISH" );
	}
	

}