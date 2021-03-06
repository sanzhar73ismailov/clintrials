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
	        $fieldNew = new Field($col_name, $col_comment, $col_type, $prev_col);

	        $fieldNew->setNull(ClinTrialsTestHelper::getTrueOrFalse());
			$this->assertTrue ( $ddlExecutor->addColumn ("t1", $fieldNew ) );

			$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
			$this->assertTrue (is_array($columnsFromDb));
			$this->assertTrue (count($columnsFromDb) > 0);
			$this->logger->debug ( var_export($columnsFromDb, true) );

			$newColumn = $ddlExecutor->getColumnFromDb($this->db->getName(), 't1', $fieldNew->getName());
			$this->assertNotNull ($newColumn);
	        $this->assertEquals ((string) $fieldNew, (string) $newColumn);
			$prevColumnFromDb = $ddlExecutor->getPreviousColumnFromDb($this->db->getName(), 't1', $fieldNew->getName());
			$this->assertEquals($prev_col, $prevColumnFromDb->column_name);
	        $prev_col = $fieldNew->getName();
			//$this->logger->debug ( var_export($newColumn, true) );
		 }
		
		$this->logger->debug ( "FINISH" );
	}

	public function testDropColumn() : void {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		$sql = "create table t1 (id int, name varchar(20), f1 int, f2 float, f3 date )";
		$this->assertTrue ($ddlExecutor->runSql($sql));
		$this->assertTrue ( $ddlExecutor->tableExists ("t1") );
		$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
		$this->assertCount(5, $columnsFromDb);
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1', "id"));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','name'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f2'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f3'));

		$this->assertTrue ( $ddlExecutor->dropColumn ("t1", "f1") );
		$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
		$this->assertCount(4, $columnsFromDb);
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1', "id"));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','name'));
		$this->assertNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f2'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f3'));

		$this->assertTrue ( $ddlExecutor->dropColumn ("t1", "name") );
		$this->assertTrue ( $ddlExecutor->dropColumn ("t1", "f2") );
		$this->assertTrue ( $ddlExecutor->dropColumn ("t1", "f3") );
		$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
		$this->assertCount(1, $columnsFromDb);
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1', "id"));
		$this->assertNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','name'));
		$this->assertNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1'));
		$this->assertNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f2'));
		$this->assertNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f3'));

		$this->assertFalse ( $ddlExecutor->dropColumn ("t1", "column_not_exist") );
		//$this->assertFalse ( $ddlExecutor->dropColumn ("t1", "id") ); // You can't delete all columns 

		$this->logger->debug ( "FINISH" );
	}

	public function testChangeColumn() : void {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		$sql = "create table t1 (id int, name varchar(20), f1 int, f2 float, f3 date )";
		$this->assertTrue ($ddlExecutor->runSql($sql));
		$this->assertTrue ( $ddlExecutor->tableExists ("t1") );
		$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
		$this->assertCount(5, $columnsFromDb);
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1', "id"));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','name'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f2'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f3'));


		$fieldChanged = new Field("f1_changed", "col_comment", "varchar", "f2");
		$this->logger->debug ( "fieldChanged ddl =" . $fieldChanged->getDdl() );

		$this->assertTrue ($ddlExecutor->changeColumn("t1", "f1", $fieldChanged));
		$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
		$this->assertCount(5, $columnsFromDb);
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1', "id"));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','name'));

		$this->assertNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1_changed'));

		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f2'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f3'));

		$prevColumnFromDb = $ddlExecutor->getPreviousColumnFromDb($this->db->getName(), 't1', $fieldChanged->getName());
		$this->assertEquals("f2", $prevColumnFromDb->column_name);

		$colChanged = $ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1_changed');
		$this->assertEquals ((string) $fieldChanged, (string) $colChanged);
	

        // $prev_col = "id";
        // for ($i=0; $i < 10; $i++) { 
        // 	$col_name = "n1" . ClinTrialsTestHelper::generateRandomString(10);
	       //  $col_type = ClinTrialsTestHelper::getRandomType();
	       //  $col_comment = "comment_" . ClinTrialsTestHelper::generateRandomString(rand(10, 100));
	       //  $fieldNew = new Field($col_name, $col_comment, $col_type);
		$this->logger->debug ( "FINISH" );
	}

	public function testChangeColumnOrder() : void {
		$this->logger->debug ( "START" );
		$ddlExecutor = new DdlExecutor ( $this->db );
		$sql = "create table t1 (id int, name varchar(20), f4 int, f1 int, f2 float, f3 date )";
		$this->assertTrue ($ddlExecutor->runSql($sql));
		$this->assertTrue ( $ddlExecutor->tableExists ("t1") );
		$columnsFromDb = $ddlExecutor->getColumnsFromDb($this->db->getName(), 't1');
		$this->assertCount(6, $columnsFromDb);
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1', "id"));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','name'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f1'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f2'));
		$this->assertNotNull($ddlExecutor->getColumnFromDb($this->db->getName(), 't1','f3'));

		$showCreateTable = $ddlExecutor->showCreateTable("t1");
		$this->assertTrue (strpos($showCreateTable, "`f4`") < strpos($showCreateTable, "`f3`"));

        $ddlExecutor->changeColumnOrder("t1", "f4", "f3");
        $this->assertCount(6, $columnsFromDb);

        $showCreateTable = $ddlExecutor->showCreateTable("t1");
		$this->assertTrue (strpos($showCreateTable, "`f1`") < strpos($showCreateTable, "`f4`"));
		$this->assertTrue (strpos($showCreateTable, "`f2`") < strpos($showCreateTable, "`f4`"));
		$this->assertTrue (strpos($showCreateTable, "`f3`") < strpos($showCreateTable, "`f4`"));

		$ddlExecutor->changeColumnOrder("t1", "name", "f4");
        $this->assertCount(6, $columnsFromDb);

        $showCreateTable = $ddlExecutor->showCreateTable("t1");
		$this->assertTrue (strpos($showCreateTable, "`f1`") < strpos($showCreateTable, "`name`"));
		$this->assertTrue (strpos($showCreateTable, "`f2`") < strpos($showCreateTable, "`name`"));
		$this->assertTrue (strpos($showCreateTable, "`f3`") < strpos($showCreateTable, "`name`"));
		$this->assertTrue (strpos($showCreateTable, "`f4`") < strpos($showCreateTable, "`name`"));
		
		$this->logger->debug ( "FINISH" );
	}


}