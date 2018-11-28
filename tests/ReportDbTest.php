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
			$this->assertTrue ( $ddlExecutor->tableExists ( $table->getTableJrnl ()->getName () ) );
			
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
			$this->assertFalse($reportTable->getTriggerInsertValid());
			$this->assertFalse($reportTable->getTriggerUpdateValid());
			$this->assertNotNull($reportTable->getTableValidationResult());
			$this->assertNotNull($reportTable->getTableJrnlValidationResult());
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
			

			if(!$reportTable->getTableJrnlVaild()){
				$this->logger->debug ("start show errors for: " . $reportTable->getTable()->getTableJrnl()->getName());
				$this->logger->debug (var_export($reportTable->getTableJrnlValidationResult()->errors,true));
				$this->logger->debug ("finish show errors for: " . $reportTable->getTable()->getTableJrnl()->getName());
			}
			$this->assertTrue($reportTable->getTableJrnlVaild());

			$this->assertTrue($reportTable->getTriggerInsertExist());
			$this->assertTrue($reportTable->getTriggerUpdateExist());

            $this->assertTrue($reportTable->getTriggerInsertValid());
			$this->assertTrue($reportTable->getTriggerUpdateValid());

			$this->assertNotNull($reportTable->getTableValidationResult());
			$this->assertNotNull($reportTable->getTableJrnlValidationResult());
			$this->assertTrue($reportTable->getReportTableValid());
			
		}
		$this->logger->debug ( "FINISH" );
	}

	public function testCreateReportIfDbIfColumnsInJournalTablesWereChanged() : void {
		$this->logger->debug ( "START" );
		$this->createTablesAndTriggers ();
		$ddlExecutor = new DdlExecutor ( $this->db );
		$reportDb = new ReportDb();
		$this->logger->debug ( "start ReportDb::createReport" );
		$reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$this->logger->debug ( "finish ReportDb::createReport" );
		$reportTables = $reportDb->getReportTables();
		$reportTable = $reportDb->getReportTableByTableName("clin_test_lab");
		$this->assertNotNull($reportTable);
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

		$updateQuery = "ALTER TABLE clin_test_lab_jrnl MODIFY COLUMN erythrocytes int(11) NOT NULL COMMENT 'Эритроциты'";
		$this->logger->debug ('$updateQuery=' . $updateQuery);
		$this->assertTrue($ddlExecutor->runSql($updateQuery));

		$this->logger->debug ( "start ReportDb::createReport after column change in table: " . $reportTable->getTable()->getName()  );
	    $reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$this->logger->debug ( "finish ReportDb::createReport after column change" );
		$reportTables = $reportDb->getReportTables();
		$reportTable = $reportDb->getReportTableByTableName("clin_test_lab");
		$this->assertNotNull($reportTable);
		$this->assertNotNull($reportTable->getTable());
		$this->assertInstanceOf(ReportTable::class, $reportTable);
		$this->assertTrue($reportTable->getTableExist());
		$this->assertTrue($reportTable->getTableValid());
		$this->assertTrue($reportTable->getTableJrnlExist());
		$this->assertFalse($reportTable->getTableJrnlVaild());
		$this->assertTrue($reportTable->getTriggerInsertExist());
		$this->assertTrue($reportTable->getTriggerUpdateExist());
		$this->assertNotNull($reportTable->getTableValidationResult());
		$this->assertTrue($reportTable->getTableValidationResult()->passed);

		$this->assertNotNull($reportTable->getTableJrnlValidationResult());
		$this->assertFalse($reportTable->getTableJrnlValidationResult()->passed);
		$this->assertTrue(count($reportTable->getTableJrnlValidationResult()->errors)>0);
		
        $reportTableValid  = ($reportTable->getTableValid() and $reportTable->getTableJrnlVaild() and $reportTable->getTriggerInsertValid() and $reportTable->getTriggerUpdateValid());
	    $this->logger->debug ( "** table: " . $reportTable->getTable()->getName());
	    $this->logger->debug ( "*** reportTableValid in test =". var_export($reportTableValid, true) );
	    $this->logger->debug ( "*** getTableValid in test =". var_export($reportTable->getTableValid(), true)  );
	    $this->logger->debug ( "*** getTableJrnlVaild in test =". var_export($reportTable->getTableJrnlVaild(), true)  );
	    $this->logger->debug ( "*** getTriggerInsertValid in test =". var_export($reportTable->getTriggerInsertValid(), true)  );
	    $this->logger->debug ( "*** getTriggerUpdateValid in test =". var_export($reportTable->getTriggerUpdateValid(), true)  );
	    $trfalse = true and false and true and true; // будет true - так как '= имеет больший приоритет, чем 'and'
	    $trfalse = (true and false and true and true); // будет false
	     $trfalse = true && false && true && true; // будет false - так как '&&' имеет больший приоритет, чем '='
	    $this->logger->debug ( "*** true and true and false and true =". var_export($trfalse, true)  );
	    $this->assertFalse($reportTableValid);              

		$this->assertFalse($reportTable->getReportTableValid());
		$this->logger->debug ( "FINISH" );
	}

	public function testCreateReportIfTriggersWereDropped() : void {
		$this->logger->debug ( "START" );
		$this->createTablesAndTriggers ();
		$ddlExecutor = new DdlExecutor ( $this->db );
		$reportDb = new ReportDb();
		$this->logger->debug ( "start ReportDb::createReport" );
		$reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$this->logger->debug ( "finish ReportDb::createReport" );
		$reportTables = $reportDb->getReportTables();
		$reportTable = $reportDb->getReportTableByTableName("clin_test_lab");
		$this->assertNotNull($reportTable);
		$this->assertNotNull($reportTable->getTable());
		$this->assertInstanceOf(ReportTable::class, $reportTable);
		$this->assertTrue($reportTable->getTableExist());
		$this->assertTrue($reportTable->getTableValid());
		$this->assertTrue($reportTable->getTableJrnlExist());
		$this->assertTrue($reportTable->getTableJrnlVaild());
		
		$this->assertTrue($reportTable->getTriggerInsertExist());
		$this->assertTrue($reportTable->getTriggerUpdateExist());
		$this->assertTrue($reportTable->getTriggerInsertValid());
		$this->assertTrue($reportTable->getTriggerUpdateValid());

		$this->assertNotNull($reportTable->getTableValidationResult());
		$this->assertNotNull($reportTable->getTableJrnlValidationResult());
		$this->assertTrue($reportTable->getReportTableValid());


        $insertTrigName = $reportTable->getTable()->getTriggerInsert()->getName();
        $updateTrigName = $reportTable->getTable()->getTriggerUpdate()->getName();
        $tableName = $reportTable->getTable()->getName();
        $tableJrnlName = $reportTable->getTable()->getTableJrnl()->getName();

        $reportDb = null;
		$reportTable = null;

		$this->assertTrue($ddlExecutor->runSql("drop trigger if exists " . $insertTrigName));
		$this->assertTrue($ddlExecutor->runSql("drop trigger if exists " . $updateTrigName));
		
		$reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$reportTable = $reportDb->getReportTableByTableName("clin_test_lab");

        $this->assertNotNull($reportTable);
		$this->assertNotNull($reportTable->getTable());
		$this->assertInstanceOf(ReportTable::class, $reportTable);
		$this->assertTrue($reportTable->getTableExist());
		$this->assertTrue($reportTable->getTableValid());
		$this->assertTrue($reportTable->getTableJrnlExist());
		$this->assertTrue($reportTable->getTableJrnlVaild());

		$this->assertFalse($reportTable->getTriggerInsertExist());
		$this->assertFalse($reportTable->getTriggerInsertValid());

		$this->assertFalse($reportTable->getTriggerUpdateValid());
		$this->assertFalse($reportTable->getTriggerUpdateExist());


		$this->assertFalse($reportTable->getReportTableValid());
		$this->logger->debug ( "FINISH" );
	}

	public function testCreateReportIfTriggersWereChanged() : void {
		$this->logger->debug ( "START" );
		$this->createTablesAndTriggers ();
		$ddlExecutor = new DdlExecutor ( $this->db );
		$reportDb = new ReportDb();
		$this->logger->debug ( "start ReportDb::createReport" );
		$reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$this->logger->debug ( "finish ReportDb::createReport" );
		$reportTables = $reportDb->getReportTables();
		$reportTable = $reportDb->getReportTableByTableName("clin_test_lab");
		$this->assertNotNull($reportTable);
		$this->assertNotNull($reportTable->getTable());
		$this->assertInstanceOf(ReportTable::class, $reportTable);
		$this->assertTrue($reportTable->getTableExist());
		$this->assertTrue($reportTable->getTableValid());
		$this->assertTrue($reportTable->getTableJrnlExist());
		$this->assertTrue($reportTable->getTableJrnlVaild());
		
		$this->assertTrue($reportTable->getTriggerInsertExist());
		$this->assertTrue($reportTable->getTriggerUpdateExist());
		$this->assertTrue($reportTable->getTriggerInsertValid());
		$this->assertTrue($reportTable->getTriggerUpdateValid());

		$this->assertNotNull($reportTable->getTableValidationResult());
		$this->assertNotNull($reportTable->getTableJrnlValidationResult());
		$this->assertTrue($reportTable->getReportTableValid());


        $insertTrigName = $reportTable->getTable()->getTriggerInsert()->getName();
        $updateTrigName = $reportTable->getTable()->getTriggerUpdate()->getName();
        $tableName = $reportTable->getTable()->getName();
        $tableJrnlName = $reportTable->getTable()->getTableJrnl()->getName();

        $reportDb = null;
		$reportTable = null;

		$this->assertTrue($ddlExecutor->runSql("drop trigger if exists " . $insertTrigName));
		$this->assertTrue($ddlExecutor->runSql("drop trigger if exists " . $updateTrigName));
	    $triggerCreateQuery = sprintf("create trigger %s after insert on %s " .
                                     " for each row insert into %s (id, patient_id, visit_id) " .
                                     " values (new.id, new.patient_id, new.visit_id)", 
                                     $insertTrigName, $tableName, $tableJrnlName);
	    $this->assertTrue($ddlExecutor->runSql($triggerCreateQuery));
	    $triggerCreateQuery = sprintf("create trigger %s after update on %s " .
                                     " for each row insert into %s (id, patient_id, visit_id) " .
                                     " values (new.id, new.patient_id, new.visit_id)", 
                                     $updateTrigName, $tableName, $tableJrnlName);
	    $this->assertTrue($ddlExecutor->runSql($triggerCreateQuery));

		$this->logger->debug ( "start ReportDb::createReport after triggers changed in table: " . $tableName);
	    $reportDb = ReportDb::createReport($this->metadataCreator, new DdlExecutor ( $this->db ));
		$this->logger->debug ( "finish ReportDb::createReport after triggers change" );
		$reportTable = $reportDb->getReportTableByTableName("clin_test_lab");

		$this->assertNotNull($reportTable);
		$this->assertNotNull($reportTable->getTable());
		$this->assertInstanceOf(ReportTable::class, $reportTable);
		$this->assertTrue($reportTable->getTableExist());
		$this->assertTrue($reportTable->getTableValid());
		$this->assertTrue($reportTable->getTableJrnlExist());
		$this->assertTrue($reportTable->getTableJrnlVaild());
		$this->assertTrue($reportTable->getTriggerInsertExist());
		$this->assertTrue($reportTable->getTriggerUpdateExist());
		
		$this->assertFalse($reportTable->getTriggerInsertValid());
		$this->assertFalse($reportTable->getTriggerUpdateValid());
		
		$this->assertNotNull($reportTable->getTableValidationResult());
		$this->assertTrue($reportTable->getTableValidationResult()->passed);

		$this->assertNotNull($reportTable->getTableJrnlValidationResult());
		$this->assertTrue($reportTable->getTableJrnlValidationResult()->passed);
		$this->assertFalse($reportTable->getReportTableValid());
		$this->logger->debug ( "FINISH" );
	}

}