<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;
class DdlValidateTest extends TestCase {
	private $logger;
	private $metadataCreator;
	private $db;
	
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
	
	public function testValidate() {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		$db = $this->db;
		$tables = $db->getTables ();
		$this->assertNotNull($this->db->getTable('clin_test_patient'));
		$this->assertNotNull($this->db->getTable('clin_test_lab'));
		$this->assertNotNull($this->db->getTable('clin_test_instrument'));
		$this->assertTrue ( count ( $tables ) > 0 );
		foreach ( $tables as $table ) {
			
			$this->assertTrue ( $ddlExecutor->tableExists ( $table->getName() ) );
			$this->logger->debug ( "test table " . $table->getName () );
			
			$res = $ddlExecutor->tableMatched ( $table );
			$this->logger->debug ( var_export ( $res, true ) );
			$this->assertTrue ( $res->passed );
			$this->assertTrue ( count ( $res->errors ) == 0 );
		}

		$this->logger->debug ( "FINISH" );
	}

	public function testValidateOrderOfColumnIsDifferent() {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		$db = $this->db;
		$tables = $db->getTables ();
		$table = $this->db->getTable('clin_test_lab');
		$this->assertNotNull($table);
		$this->assertTrue ( $ddlExecutor->tableExists ( $table->getName() ) );
		$this->logger->debug ( "test table " . $table->getName () );
		
		$res = $ddlExecutor->tableMatched ( $table );
		$this->logger->debug ( var_export ( $res, true ) );
		$this->assertTrue ( $res->passed );
		$this->assertTrue ( count ( $res->errors ) == 0 );
		$query = "ALTER TABLE `clin_test_lab` MODIFY COLUMN `visit_id` INTEGER(11) DEFAULT NULL COMMENT 'Визит' AFTER `id`";
		$this->assertTrue ($ddlExecutor->runSql($query));
		$res = $ddlExecutor->tableMatched ( $table );
		$this->assertFalse ( $res->passed );
		$this->assertTrue ( count ( $res->errors ) == 1 );
		$this->logger->debug ( var_export ( $res->errors, true ) );
		//$this->assertTrue ( count ( $res->errors ) == 1 );
		$this->logger->debug ( "FINISH" );
	}

}