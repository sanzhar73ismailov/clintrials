<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;


class DdlValidateTest extends TestCase {
	private $logger;
	private $metadataCreator;
	private $db;
	
	public function __construct() {
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function setUp(){
		$this->metadataCreator = new MetadataCreator ( "tests/clintrials_test.xml" );
		$this->createDb();
		$this->createTablesAndTriggers();
		
	}
	
	public function createDb() {
		$this->db = $this->metadataCreator->getDb();
		$ddlExecutor = new DdlExecutor($this->db);
		$this->logger->debug("\$this->metadataCreator->getDb()->getName()=" . $this->db->getName());
		if($ddlExecutor->dbExists()){
			$ddlExecutor->dropDb();
		}
		$this->assertFalse($ddlExecutor->dbExists());
		$this->assertTrue($ddlExecutor->createDb());
	}
	
	public function tearDown() {
		$this->metadataCreator = null;
	}

	public function createTablesAndTriggers() {
		$this->logger->debug("START");
		$ddlExecutor = new DdlExecutor($this->db);
		$db = $this->db;
		$tables = $db->getTables();
		foreach ($tables as $table) {
			if (0) {
				$table = new Table();
			}
			$this->assertTrue($ddlExecutor->createTable($table));
			$this->assertTrue ( $ddlExecutor->tableExists ($table->getName()) );
			
			$this->assertTrue($ddlExecutor->createTableJrnl($table));
			$this->assertTrue ( $ddlExecutor->tableExists ($table->getTableJrnj()->getName()) );
			
			$this->assertFalse($ddlExecutor->triggerExists($table->getTriggerInsert()));
			$this->assertTrue($ddlExecutor->createTrigger($table->getTriggerInsert()));
			$this->assertTrue($ddlExecutor->triggerExists($table->getTriggerInsert()));
			
			$this->assertFalse($ddlExecutor->triggerExists($table->getTriggerUpdate()));
			$this->assertTrue($ddlExecutor->createTrigger($table->getTriggerUpdate()));
			$this->assertTrue($ddlExecutor->triggerExists($table->getTriggerUpdate()));
		}
		
		$this->logger->debug("FINISH");
	}
	

	public function testSomething() {
		$this->logger->debug("START");
		$this->assertTrue(1 == 1);
		$this->assertFalse(1 == 0);
		$ddlExecutor = new DdlExecutor($this->db);
		$db = $this->db;
		$tables = $db->getTables();
		$this->assertTrue(count($tables) > 0);
		$this->assertTrue($ddlExecutor->tableExists("clin_test_instrument"));
		$res = $ddlExecutor->tableMatched($tables[0]);
		$this->logger->debug('count=' . count($res));
		$this->logger->debug(var_export($res, true));
		$this->logger->debug("FINISH");
	}

	
}