<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;


class AlterTableTest extends TestCase {
	private $logger;
	private $metadataCreator;
	
	public function setUp(){
		$this->logger = Logger::getLogger(__CLASS__);
		$this->metadataCreator = new MetadataCreator ( ClinTrialsTestHelper::TEST_XML );
		$this->createDb ();
		//$this->createTablesAndTriggers ();
	}
	public function tearDown() {
		$this->metadataCreator = null;
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

	public function testAddColumn() : void {
		$this->logger->debug ( "START" );
		//$this->assertFalse ( $ddlExecutor->tableExists ("t1") );
		//$this->assertTrue (1==1);
		$ddlExecutor = new DdlExecutor ( $this->db );
		
		$sql = "create table t1 (id int, name varchar(20))";
		$this->assertTrue ($ddlExecutor->runSql($sql));
		
		$this->assertTrue ( $ddlExecutor->tableExists ("t1") );

        

        


        for ($i=0; $i < 20; $i++) { 
        	$this->assertContains(ClinTrialsTestHelper::getRandomType(), ['date', 'float', 'text', 'varchar', 'int', 'list', 'boolean', 'timestamp']);
        }
		

        $prev_col = "id";
        for ($i=0; $i < 10; $i++) { 
        	$col_name = "n1" . ClinTrialsTestHelper::generateRandomString(10);
	        $col_type = ClinTrialsTestHelper::getRandomType();
	        $col_comment = "comment_" . ClinTrialsTestHelper::generateRandomString(rand(10, 100));
	        $fieldNew = new Field($col_name, $col_comment, $col_type);

	        $fieldNew->setNull(ClinTrialsTestHelper::getTrueOrFalse());
			$this->assertTrue ( $ddlExecutor->addColumn ("t1", $fieldNew, $prev_col ) );


			$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
			$this->assertTrue (is_array($columnsFromDb));
			$this->assertTrue (count($columnsFromDb) > 0);
			$this->logger->debug ( var_export($columnsFromDb, true) );

			$newColumn = $ddlExecutor->getColumnFromDb($this->db->getName(), 't1', $fieldNew->getName());
			$this->assertNotNull ($newColumn);
	        $this->assertEquals ((string) $fieldNew, (string) $newColumn);
	        $prev_col = $fieldNew->getName();
			//$this->logger->debug ( var_export($newColumn, true) );
		 }
		
		$this->logger->debug ( "FINISH" );
	}
}