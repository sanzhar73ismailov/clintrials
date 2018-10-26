<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;


class DdlExecutorTest extends TestCase {
	private $logger;
	private $metadataCreator;
	
	public function __construct() {
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function setUp(){
		$this->metadataCreator = new MetadataCreator ( "tests/clintrials_test.xml" );
	}
	public function tearDown() {
		$this->metadataCreator = null;
	}
	public function testPropsOfMetadataCreator() {
		$this->logger->debug("START");
		$this->assertNotNull($this->metadataCreator);
		$this->assertNotNull($this->metadataCreator->getXmlObj());
		$this->assertNotNull($this->metadataCreator->getDb());
		$this->assertInstanceOf('SimpleXMLElement', $this->metadataCreator->getXmlObj());
		$this->assertInstanceOf(DbSchema::class, $this->metadataCreator->getDb());
		$this->logger->debug ( "FINISH" );
	}
	
	public function testCreateDrobDb() {
		$this->logger->debug ( "START" );
		$db = $this->metadataCreator->getDb ();
		if (0) {
			$db = new DbSchema ();
		}
		$ddlExecutor = new DdlExecutor ( $db );
		if ($ddlExecutor->dbExists ()) {
			$this->assertTrue ( $ddlExecutor->dropDb () );
		}
		$this->assertFalse ( $ddlExecutor->dbExists () );
		$this->assertTrue ( $ddlExecutor->createDb () );
		$this->assertTrue ( $ddlExecutor->dbExists () );
		$this->assertTrue ( $ddlExecutor->dropDb () );
		$this->assertFalse ( $ddlExecutor->dbExists () );
		$this->logger->debug ( "FINISH" );
	}
	
	public function testTableExists() {
		$this->logger->debug ( "START" );
		$db = $this->metadataCreator->getDb ();
		if (0) {
			$db = new DbSchema ();
		}
		$ddlExecutor = new DdlExecutor ( $db );
		if ($ddlExecutor->dbExists ()) {
			$this->assertTrue ( $ddlExecutor->dropDb () );
		}
		$this->assertFalse ( $ddlExecutor->dbExists () );
		$this->assertTrue ( $ddlExecutor->createDb () );
		$this->assertTrue ( $ddlExecutor->dbExists () );
		
		$this->assertFalse ( $ddlExecutor->tableExists ("t1") );
		$sql = "create table t1 (id int, name varchar(20))";
		$this->assertTrue ($ddlExecutor->runSql($sql));
		
		$this->assertTrue ( $ddlExecutor->tableExists ("t1") );
		
		$this->logger->debug ( "FINISH" );
	}
	
	public function testMetadataCreator() {
		$this->logger->debug("START");
		$db = $this->metadataCreator->getDb();
		if(0) {
			$db = new DbSchema();
		}
		$ddlExecutor = new DdlExecutor($db);
		$this->logger->debug("\$this->metadataCreator->getDb()->getName()=" . $db->getName());
		if($ddlExecutor->dbExists()){
			$ddlExecutor->dropDb();
		}
		$this->assertFalse($ddlExecutor->dbExists());
		$this->assertTrue($ddlExecutor->createDb());
		
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
	
}