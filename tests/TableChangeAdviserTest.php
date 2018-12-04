<?php
require_once "configs/app_prop_test.php";

use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;

use clintrials\admin\validation\FieldMetaFromDb;
use clintrials\admin\validation\ValidationResult;
use clintrials\admin\validation\TableValidation;
use clintrials\admin\validation\TableChangeAdviser;
use clintrials\admin\metadata\Trigger;
use clintrials\admin\validation\TableMetaFromDb;

use PHPUnit\Framework\TestCase;
use clintrials\admin\DdlExecutor;

class TableChangeAdviserTest extends TestCase {
	private $logger;
	
	public function setUp(){
		$this->logger = Logger::getLogger(__CLASS__);
	}

	function provideTableAndFields() {
		$table = new Table();
		$table->setName("t1");
		$field1 = new Field("f1", "comment1", "int");
		$field2 = new Field("f2", "comment2", "int");
		$field3 = new Field("f3", "comment3", "int");
		$table->addField($field1);
		$table->addField($field2);
		$table->addField($field3);
		$data = array ( 
			array($table, "f1", $field1),
			array($table, "f2", $field2),
			array($table, "f3", $field3)
		);
		return $data;
	}

	function provideTableAndFieldsBefore() {
		$table = new Table();
		$table->setName("t1");
		$field1 = new Field("f1", "comment1", "int");
		$field2 = new Field("f2", "comment2", "int");
		$field3 = new Field("f3", "comment3", "varchar");
		$table->addField($field1);
		$table->addField($field2);
		$table->addField($field3);
		$data = array ( 
			array($table, "f1", new Field("","","")),
			array($table, "f2", $field1),
			array($table, "f3", $field2)
		);
		return $data;
	}

	function provideTableAndTableMetaFromDbForTestAdd() {
		$table = new Table();
		$table->setName("t1");

		$table->addField(new Field("f1", "comment1", "int"));
		$table->addField(new Field("f2", "comment2", "int"));
		$table->addField(new Field("f3", "comment3", "int"));
		$table->addField(new Field("f4", "comment4", "varchar"));
		$table->addField(new Field("afterf4", "comment_afterf44", "varchar"));
		
		$tableMetaFromDb = new TableMetaFromDb();
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f1", "comment1", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f3", "comment3", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f2", "comment2", "int");
		
		$data [] = array($table, $tableMetaFromDb);
		return $data;
	}

	function provideTableAndTableMetaFromDbForTestRemove() {
		$table = new Table();
		$table->setName("t1");

		$table->addField(new Field("f1", "comment1", "int"));
		$table->addField(new Field("f2", "comment2", "int"));
		$table->addField(new Field("f3", "comment3", "int"));
		
		$tableMetaFromDb = new TableMetaFromDb();
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f1", "comment1", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f2", "comment2", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f3", "comment3", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("fTobRemoved", "comment_fTobRemoved", "int");
		
		$data [] = array($table, $tableMetaFromDb);
		return $data;
	}

	function provideTableAndTableMetaFromDbForTestChangePosition() {
		$table = new Table();
		$table->setName("t1");

		$table->addField(new Field("f1", "comment1", "int"));
		$table->addField(new Field("f2", "comment2", "int"));
		$table->addField(new Field("f3", "comment3", "int"));
		
		$tableMetaFromDb = new TableMetaFromDb();
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f1", "comment1", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f3", "comment3", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f2", "comment2", "int");
		
		$data [] = array($table, $tableMetaFromDb);
		return $data;
	}

	function provideTableAndTableMetaFromDbForTestChangeType() {
		$table = new Table();
		$table->setName("t1");

		$table->addField(new Field("f1", "comment1", "int"));
		$table->addField(new Field("f2", "comment2", "varchar"));
		$table->addField(new Field("f3", "comment3", "int"));
		$table->addField(new Field("f4", "comment4Another", "int"));
		
		$tableMetaFromDb = new TableMetaFromDb();
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f1", "comment1", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f3", "comment3", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f2", "comment2", "int");
		$tableMetaFromDb->columns[] = new FieldMetaFromDb("f4", "comment4", "int");
		
		$data [] = array($table, $tableMetaFromDb);
		return $data;
	}

	/**
	 * @dataProvider provideTableAndFields
	 */
	public function testTableGetFieldByName($table, $fieldName, $fieldExpected) {
		$this->logger->debug("START");
		$this->assertEquals($fieldExpected, $table->getFieldByName($fieldName));
		$this->assertNotEquals("f111", $table->getFieldByName($fieldName));
		$this->logger->debug("FINISH");
	}

	/**
	 * @dataProvider provideTableAndFieldsBefore
	 */
	public function testTableGetFieldBefore($table, $fieldName, $fieldExpected) {
		$this->logger->debug("START");
		$this->assertEquals($fieldExpected, $table->getFieldBefore($fieldName));
		$this->logger->debug("FINISH");
	}

    /**
	 * @dataProvider provideTableAndTableMetaFromDbForTestAdd
	 */
    public function testAddActions($table, $tableMetaFromDb) {

    	$ddlStub = $this->createMock(DdlExecutor::class);
        $ddlStub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
    
        $tableChangeAdviser = new TableChangeAdviser($ddlStub, $table);
        $tableChangeAdviser->advise();
        $this->assertNotNull($tableChangeAdviser);
        
        $this->assertNotNull($tableChangeAdviser->getActionsAdd());
        $this->assertNotNull($tableChangeAdviser->getActionsRemove());
        $this->assertNotNull($tableChangeAdviser->getActionsChange());

        $this->logger->debug('$tableChangeAdviser->getActionsAdd()=' .
        	var_export($tableChangeAdviser->getActionsAdd(), true)
        );
        $this->logger->debug('count($tableChangeAdviser->getActionsAdd())='.count($tableChangeAdviser->getActionsAdd()));

        $this->logger->debug('$tableChangeAdviser->getActionsRemove()=' .
        	var_export($tableChangeAdviser->getActionsRemove(), true)
        );

        $this->assertCount(2, $tableChangeAdviser->getActionsAdd());
        $this->assertCount(0, $tableChangeAdviser->getActionsRemove());
        $this->assertCount(0, $tableChangeAdviser->getActionsChange());

        $this->assertEquals("add", $tableChangeAdviser->getActionsAdd()[0]->type);
        $this->assertEquals("f4", $tableChangeAdviser->getActionsAdd()[0]->field->getName());
        $this->assertEquals(new Field("f4", "comment4", "varchar"), $tableChangeAdviser->getActionsAdd()[0]->field);
        $this->assertEquals("f3", $tableChangeAdviser->getActionsAdd()[0]->after->getName());
        $this->assertEquals(new Field("f3", "comment3", "int"), $tableChangeAdviser->getActionsAdd()[0]->after);

        $this->assertEquals("add", $tableChangeAdviser->getActionsAdd()[0]->type);
        $this->assertEquals("afterf4", $tableChangeAdviser->getActionsAdd()[1]->field->getName());
        $this->assertEquals("f4", $tableChangeAdviser->getActionsAdd()[1]->after->getName());
    }

    /**
	* @dataProvider provideTableAndTableMetaFromDbForTestRemove
	*/
    public function testRemoveActions($table, $tableMetaFromDb) {

    	$ddlStub = $this->createMock(DdlExecutor::class);
        $ddlStub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
    
        $tableChangeAdviser = new TableChangeAdviser($ddlStub, $table);
        $tableChangeAdviser->advise();
        $this->assertNotNull($tableChangeAdviser);
        
        $this->assertNotNull($tableChangeAdviser->getActionsAdd());
        $this->assertNotNull($tableChangeAdviser->getActionsRemove());
        $this->assertNotNull($tableChangeAdviser->getActionsChange());

        $this->assertCount(0, $tableChangeAdviser->getActionsAdd());
        $this->assertCount(1, $tableChangeAdviser->getActionsRemove());
        $this->assertCount(0, $tableChangeAdviser->getActionsChange());

        $this->assertEquals("remove", $tableChangeAdviser->getActionsRemove()[0]->type);
        $this->assertEquals("fTobRemoved", $tableChangeAdviser->getActionsRemove()[0]->field->getName());
        $this->assertEquals(new Field("fTobRemoved", "", ""), $tableChangeAdviser->getActionsRemove()[0]->field);
        $this->assertEquals("", $tableChangeAdviser->getActionsAdd()[0]->after);
    }
    
    /**
	* @dataProvider provideTableAndTableMetaFromDbForTestChangeType
	*/
    public function testChangeActionsChangeType($table, $tableMetaFromDb) {

    	$ddlStub = $this->createMock(DdlExecutor::class);
        $ddlStub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
    
        $tableChangeAdviser = new TableChangeAdviser($ddlStub, $table);
        $tableChangeAdviser->advise();
        $this->assertNotNull($tableChangeAdviser);

        

        $this->assertNotNull($tableChangeAdviser->getActionsAdd());
        $this->assertNotNull($tableChangeAdviser->getActionsRemove());
        $this->assertNotNull($tableChangeAdviser->getActionsChange());

        $this->assertCount(0, $tableChangeAdviser->getActionsAdd());
        $this->assertCount(0, $tableChangeAdviser->getActionsRemove());
        $this->assertCount(2, $tableChangeAdviser->getActionsChange());

        $actonsChange = $tableChangeAdviser->getActionsChange();

        $this->assertEquals("change", $tableChangeAdviser->getActionsChange()[0]->type);
        $this->assertEquals("f2", $tableChangeAdviser->getActionsChange()[0]->field->getName());
        $this->assertEquals(new Field("f2", "comment2", "varchar"), $tableChangeAdviser->getActionsChange()[0]->field);

        $this->assertEquals("change", $tableChangeAdviser->getActionsChange()[1]->type);
        $this->assertEquals("f4", $tableChangeAdviser->getActionsChange()[1]->field->getName());
        $this->assertEquals(new Field("f4", "comment4Another", "int"), $tableChangeAdviser->getActionsChange()[1]->field);

        /*
        
        

        

       
        $this->assertEquals(new Field("fTobRemoved", "", ""), $tableChangeAdviser->getActionsRemove()[0]->field);
        $this->assertEquals("", $tableChangeAdviser->getActionsAdd()[0]->after);
        */
    }
}


