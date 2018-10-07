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
	
	public function testCreateTrigger() {
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
		
		$this->logger->debug ( "result=" . $result );
		
		/*
		if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
				$this->logger->debug (  "id: " . $row["id"] . " - f1: " . $row["f1"] . "<br>" . PHP_EOL);
			}
		} else {
			echo "0 results";
		}
		*/
		$conn->close();
		$this->logger->debug ( "FINISH" );
	}
}