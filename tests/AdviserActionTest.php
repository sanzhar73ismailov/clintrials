<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;


class AdviserActionTest extends TestCase {
	private $logger;
	//private $metadataCreator;
	private $ddlExecutor;
	private $tName = "t1adviser";
	
	public function setUp(){
		$this->logger = Logger::getLogger(__CLASS__);
		$metadataCreator = new MetadataCreator ( ClinTrialsTestHelper::TEST_XML );
		$this->ddlExecutor = new DdlExecutor ( $metadataCreator->getDb () );
		$ddlExecutor = $this->ddlExecutor;

		if ($ddlExecutor->dbExists ()) {
			$this->assertTrue ( $ddlExecutor->dropDb () );
		}
		$this->assertFalse ( $ddlExecutor->dbExists () );
		$this->assertTrue ( $ddlExecutor->createDb () );
		$this->assertTrue ( $ddlExecutor->dbExists () );
		
		$this->assertFalse ( $ddlExecutor->tableExists ("t1") );
		$sql = "create table " . $this->tName . " (
		          id int(11) NOT NULL  PRIMARY KEY AUTO_INCREMENT COMMENT 'PK',
		          last_name varchar(20) COMMENT 'Surname', 
		          firs_name varchar(20) COMMENT 'First name',
		          age int(11) COMMENT 'Age of person',
		          salary float(11,2) COMMENT 'Salary per month'
		        )";
		$this->assertTrue ($ddlExecutor->runSql($sql));
	}
	public function tearDown() {
		$this->ddlExecutor = null;
	}

	 public function testAddColumn() {
	 	$this->assertTrue(1==1);
	 	$fieldNew = new Field ("f_new", "This is a new column", "int");

	 	$this->assertTrue ($this->ddlExecutor->addColumn( $this->tName, $fieldNew, "age"));
	 }

}