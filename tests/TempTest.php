<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\DdlExecutor;
use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Trigger;


class TempTest extends TestCase {
	private $logger;
	private $metadataCreator;
	
	public function setUp(){
		$this->logger = Logger::getLogger(__CLASS__);
		$this->metadataCreator = new MetadataCreator ( ClinTrialsTestHelper::TEST_XML );
	}
	public function tearDown() {
		$this->metadataCreator = null;
	}

	
	
	
	public function TableExists() {
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
	
	public function CreateTrigger() {
		$this->logger->debug ( "START" );
		$servername = HOST;
		$username = DB_USER;
		$password = DB_PASS;
		$dbname = DB_NAME;
		
		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		//$sql = "SELECT * FROM t1";
		//$sql = "CREATE TRIGGER `trig_insert_name` AFTER INSERT ON `t1` FOR EACH ROW insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, new.f2)";
		$sql = "CREATE TRIGGER `t1_after_upd_trig` AFTER UPDATE ON `t1` FOR EACH ROW BEGIN\n"
				  ." set @valf2 = concat(new.id, '-' , new.f2);\n" 
                  . " insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, @valf2);\n"
                . "END";
		$result = $conn->query($sql);
		if ($result === TRUE) {
			$this->logger->debug ( "Trigger created successfully");
		} else {
			$this->logger->debug ( "Error creating trigger: " . $conn->error);
		}
		
		$this->assertTrue ( $result);
		//$conn->error
		
		$trigger->setName("t1_after_upd_trig");
		$this->assertTrue ( $ddlExecutor->triggerExists($trigger) );
		
		$this->logger->debug ( "result=" . $result );
		$conn->close();
		$this->logger->debug ( "FINISH" );
	}
	
	public function testTriggerExists() {
		$this->logger->debug ( "START" );
		$db = $this->metadataCreator->getDb ();
		if (0) {
			$db = new DbSchema ();
		}
		$ddlExecutor = new DdlExecutor ( $db );
		$trigger = new Trigger();
		$trigger->setName("t1_after_ins_trig");
		$this->assertFalse ( $ddlExecutor->triggerExists($trigger) );
		$this->logger->debug ( "FINISH" );
	}

	public function testShowCreateTable() {
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
		$sql = "
CREATE TABLE `t1` (
  `id` int(11) NOT NULL,
  `field2` int(11) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `field4` int(11) DEFAULT '3' COMMENT 'rrrr',
  `field3` int(11) NOT NULL DEFAULT '3',
  `field5` int(11) NOT NULL,
  `field6` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
		";
		$this->assertTrue ($ddlExecutor->runSql($sql));
		
		$this->assertTrue ( $ddlExecutor->tableExists ("t1") );
		$createTableDdl = $ddlExecutor->showCreateTable("t1");
		$this->assertTrue($createTableDdl != '');
		$this->assertNotNull($createTableDdl);
		$this->assertStringStartsWith('CREATE TABLE', $createTableDdl);

		$this->logger->debug ( "createTableDdl={$createTableDdl}" );

        /*
        $str = "asdasdasdqweqvc(HiMyFriend)bcsfsderwerwefvb";
		$this->assertEquals("HiMyFriend", $ddlExecutor->getTextBetweenParentess($str));
		$str = "asdasdasdqweqvcHiMyFriend)bcsfsderwerwefvb";
		$this->assertEquals("", $ddlExecutor->getTextBetweenParentess($str));
		$str = "asdasdasdqweqvc(HiMyFriendbcsfsderwerwefvb";
		$this->assertEquals("", $ddlExecutor->getTextBetweenParentess($str));
		$str = "asdasdasdqweqv)c(HiMyFriendbcsfsderwerwefvb";
		$this->assertEquals("", $ddlExecutor->getTextBetweenParentess($str));
         */

		$this->assertCount(7, $ddlExecutor->getColumDefinitionsFromDb("t1"));
		$this->assertEquals("`field2` int(11) DEFAULT NULL", $ddlExecutor->getColumDefinitionFromDb("t1", "field2"));
	
		$this->logger->debug ( "FINISH" );
	}
}