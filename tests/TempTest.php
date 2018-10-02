<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;


class TempTest extends TestCase {
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
	
}