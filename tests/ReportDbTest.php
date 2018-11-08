<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\DdlExecutor;
use clintrials\admin\ReportDb;
use clintrials\admin\ReportTable;
use clintrials\admin\metadata\Table;

class ReportDbTest extends TestCase {
	private $logger;
	private $metadataCreator;
	private $db;
	
	public function setUp() {
		$this->logger = Logger::getLogger ( __CLASS__ );
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

	public function createTablesAndTriggers() : void {
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

	public function testCreateReportIfDbEmpty() : void {
		$this->logger->debug ( "START" );
		$reportDb = new ReportDb();
		$reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$reportTables = $reportDb->getReportTables();
		foreach ($reportTables as $reportTable) {
			$this->assertNotNull($reportTable->getTable());
			$this->assertInstanceOf(ReportTable::class, $reportTable);
			$this->assertFalse($reportTable->getTableExist());
			$this->assertFalse($reportTable->getTableValid());
			$this->assertFalse($reportTable->getTableJrnlExist());
			$this->assertFalse($reportTable->getTableJrnlVaild());
			$this->assertFalse($reportTable->getTriggerInsertExist());
			$this->assertFalse($reportTable->getTriggerUpdateExist());
			$this->assertNull($reportTable->getTableValidationResult());
			$this->assertNull($reportTable->getTableJrnlValidationResult());
			$this->assertFalse($reportTable->getReportTableValid());
		}
		$this->logger->debug ( "FINISH" );
	}

	public function testCreateReportIfDbWithTables() : void {
		$this->logger->debug ( "START" );
		$this->createTablesAndTriggers ();
		$reportDb = new ReportDb();
		$this->logger->debug ( "start ReportDb::createReport" );
		$reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$this->logger->debug ( "finish ReportDb::createReport" );
		$reportTables = $reportDb->getReportTables();
		foreach ($reportTables as $reportTable) {
			//if($reportTable->getTable()->getName() != 'clin_test_lab') {
			//	continue;
			//}
			$this->logger->debug ("reportTable for table: " . $reportTable->getTable()->getName() );
			$this->assertNotNull($reportTable->getTable());
			$this->assertInstanceOf(ReportTable::class, $reportTable);
			$this->assertTrue($reportTable->getTableExist());
			$this->assertTrue($reportTable->getTableValid());
			$this->assertTrue($reportTable->getTableJrnlExist());
			$this->assertTrue($reportTable->getTableJrnlVaild());
			$this->assertTrue($reportTable->getTriggerInsertExist());
			$this->assertTrue($reportTable->getTriggerUpdateExist());
			$this->assertNotNull($reportTable->getTableValidationResult());
			$this->assertNotNull($reportTable->getTableJrnlValidationResult());
			$this->assertTrue($reportTable->getReportTableValid());
			
		}
		$this->logger->debug ( "FINISH" );
	}

}