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
			array($table, "f3", $field3),
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
			array($table, "f1", false),
			array($table, "f2", $field1),
			array($table, "f3", $field2),
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
    
        $tableChangeAdviser = new TableChangeAdviser($ddlStub);
        $tableChangeAdviser->advise($table);
        $this->assertNotNull($tableChangeAdviser);
        
        $this->assertNotNull($tableChangeAdviser->getActonsAdd());
        $this->assertNotNull($tableChangeAdviser->getActonsRemove());
        $this->assertNotNull($tableChangeAdviser->getActonsChange());

        $this->logger->debug('$tableChangeAdviser->getActonsAdd()=' .
        	var_export($tableChangeAdviser->getActonsAdd(), true)
        );
        $this->logger->debug('count($tableChangeAdviser->getActonsAdd())='.count($tableChangeAdviser->getActonsAdd()));

        $this->logger->debug('$tableChangeAdviser->getActonsRemove()=' .
        	var_export($tableChangeAdviser->getActonsRemove(), true)
        );

        $this->assertCount(2, $tableChangeAdviser->getActonsAdd());
        $this->assertCount(0, $tableChangeAdviser->getActonsRemove());
        $this->assertCount(0, $tableChangeAdviser->getActonsChange());

        $this->assertEquals("add", $tableChangeAdviser->getActonsAdd()[0]->type);
        $this->assertEquals("f4", $tableChangeAdviser->getActonsAdd()[0]->field->getName());
        $this->assertEquals(new Field("f4", "comment4", "varchar"), $tableChangeAdviser->getActonsAdd()[0]->field);
        $this->assertEquals("f3", $tableChangeAdviser->getActonsAdd()[0]->after->getName());
        $this->assertEquals(new Field("f3", "comment3", "int"), $tableChangeAdviser->getActonsAdd()[0]->after);

        $this->assertEquals("add", $tableChangeAdviser->getActonsAdd()[0]->type);
        $this->assertEquals("afterf4", $tableChangeAdviser->getActonsAdd()[1]->field->getName());
        $this->assertEquals("f4", $tableChangeAdviser->getActonsAdd()[1]->after->getName());
    }

     /**
	 * @dataProvider provideTableAndTableMetaFromDbForTestRemove
	 */
    public function testRemoveActions($table, $tableMetaFromDb) {

    	$ddlStub = $this->createMock(DdlExecutor::class);
        $ddlStub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
    
        $tableChangeAdviser = new TableChangeAdviser($ddlStub);
        $tableChangeAdviser->advise($table);
        $this->assertNotNull($tableChangeAdviser);
        
        $this->assertNotNull($tableChangeAdviser->getActonsAdd());
        $this->assertNotNull($tableChangeAdviser->getActonsRemove());
        $this->assertNotNull($tableChangeAdviser->getActonsChange());

        $this->assertCount(0, $tableChangeAdviser->getActonsAdd());
        $this->assertCount(1, $tableChangeAdviser->getActonsRemove());
        $this->assertCount(0, $tableChangeAdviser->getActonsChange());

        $this->assertEquals("remove", $tableChangeAdviser->getActonsRemove()[0]->type);
        $this->assertEquals("fTobRemoved", $tableChangeAdviser->getActonsRemove()[0]->field->getName());
        $this->assertEquals(new Field("fTobRemoved", "", ""), $tableChangeAdviser->getActonsRemove()[0]->field);
        $this->assertEquals("", $tableChangeAdviser->getActonsAdd()[0]->after);
    }



}