<?php
require_once "configs/app_prop_test.php";

use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;

use clintrials\admin\validation\FieldMetaFromDb;
use clintrials\admin\validation\ValidationResult;
use clintrials\admin\validation\TableValidation;
use clintrials\admin\validation\TableMetaFromDb;
use clintrials\admin\metadata\Trigger;
use clintrials\admin\validation\TableValidator;
use clintrials\admin\validation\Validator;
		

use PHPUnit\Framework\TestCase;
use clintrials\admin\DdlExecutor;

class TableValidatorTest extends TestCase {
	private $logger;
	
	public function setUp(){
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function getTableValidationEqual() {
		$table = new Table();
		$table->setName("t1");
		$field1 = new Field("f1", "comment1", "int");
		$field2 = new Field("f2", "comment2", "int");
		$field3 = new Field("f3", "comment3", "int");
		$table->addField($field1);
		$table->addField($field2);
		$table->addField($field3);
		
		$tableMetaFromDb = new TableMetaFromDb();
		$column1 = new FieldMetaFromDb("f1", "comment1", "int");
		$column2 = new FieldMetaFromDb("f2", "comment2", "int");
		$column3 = new FieldMetaFromDb("f3", "comment3", "int");
		$tableMetaFromDb->addColumn($column1);
		$tableMetaFromDb->addColumn($column2);
		$tableMetaFromDb->addColumn($column3);
		
		$tableValidation [] = array($table, $tableMetaFromDb);
		return $tableValidation;
	}

	public function getTableValidationColumnsMoreInDb() {
		$table = new Table();
		$table->setName("t1");
		$field1 = new Field("f1", "comment1", "int");
		$field2 = new Field("f2", "comment2", "int");
		$field3 = new Field("f3", "comment3", "int");
		$table->addField($field1);
		$table->addField($field2);
		$table->addField($field3);
		
		$tableMetaFromDb = new TableMetaFromDb();
		$column1 = new FieldMetaFromDb("f1", "comment1", "int");
		$column2 = new FieldMetaFromDb("f2", "comment2", "int");
		$column3 = new FieldMetaFromDb("f3", "comment3", "int");
		$column4 = new FieldMetaFromDb("f4", "comment4", "int");
		$tableMetaFromDb->addColumn($column1);
		$tableMetaFromDb->addColumn($column2);
		$tableMetaFromDb->addColumn($column3);
		$tableMetaFromDb->addColumn($column4);
		
		$tableValidation [] = array($table, $tableMetaFromDb);
		return $tableValidation;
	}
	
	public function getTableValidationColumnsMoreInXml() {
		$table = new Table();
		$table->setName("t1");
		$field1 = new Field("f1", "comment1", "int");
		$field2 = new Field("f2", "comment2", "int");
		$field3 = new Field("f3", "comment3", "int");
		$field4 = new Field("f4", "comment4", "int");
		$field5 = new Field("f5", "comment5", "int");
		$table->addField($field1);
		$table->addField($field2);
		$table->addField($field3);
		$table->addField($field4);
		$table->addField($field5);
		
		$tableMetaFromDb = new TableMetaFromDb();
		$column1 = new FieldMetaFromDb("f1", "comment1", "int");
		$column2 = new FieldMetaFromDb("f2", "comment2", "int");
		$column3 = new FieldMetaFromDb("f3", "comment3", "int");
		$column4 = new FieldMetaFromDb("f4", "comment4", "int");
		$tableMetaFromDb->addColumn($column1);
		$tableMetaFromDb->addColumn($column2);
		$tableMetaFromDb->addColumn($column3);
		$tableMetaFromDb->addColumn($column4);
		
		$tableValidation [] = array($table, $tableMetaFromDb);
		return $tableValidation;
	}
	
	public function getTableValidationColumnsHasDiffType() {
		$table = new Table();
		$table->setName("t1");
		$field1 = new Field("f1", "comment1", "int");
		$field2 = new Field("f2", "comment2", "int");
		$field3 = new Field("f3", "comment3", "varchar");
		$table->addField($field1);
		$table->addField($field2);
		$table->addField($field3);
		
		$tableMetaFromDb = new TableMetaFromDb();
		$column1 = new FieldMetaFromDb("f1", "comment1", "int");
		$column2 = new FieldMetaFromDb("f2", "comment2", "int");
		$column3 = new FieldMetaFromDb("f3", "comment3", "int");
		$tableMetaFromDb->addColumn($column1);
		$tableMetaFromDb->addColumn($column2);
		$tableMetaFromDb->addColumn($column3);
		
		$tableValidation [] = array($table, $tableMetaFromDb);
		return $tableValidation;
	}

	public function getTableValidationColumnsOrderIsDifferent() {
		$table = new Table();
		$table->setName("t1");
		$field1 = new Field("f1", "comment1", "int");
		$field2 = new Field("f2", "comment2", "int");
		$field3 = new Field("f3", "comment3", "int");

		$table->addField($field1);
		$table->addField($field2);
		$table->addField($field3);
		
		$tableMetaFromDb = new TableMetaFromDb();
		$column1 = new FieldMetaFromDb("f1", "comment1", "int");
		$column3 = new FieldMetaFromDb("f3", "comment3", "int");
		$column2 = new FieldMetaFromDb("f2", "comment2", "int");

		$tableMetaFromDb->addColumn($column1);
		$tableMetaFromDb->addColumn($column3);
		$tableMetaFromDb->addColumn($column2);
		
		$tableValidation [] = array($table, $tableMetaFromDb);
		return $tableValidation;
	}
	
	/**
     * @dataProvider getTableValidationEqual
     */
	public function testValidateTableEqual($table, $tableMetaFromDb) {
		$this->logger->debug("START");
		$stub = $this->createMock(DdlExecutor::class);
        $stub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
        $stub->method("tableExists")->willReturn(true); 

        $this->assertTrue($tableMetaFromDb == $stub->getTableMetaFromDb(new Table()));
        $this->assertEquals(true, $stub->tableExists($table->getName()));

        $validator = new Validator($stub);
        $validationResult = $validator->validate($table);

		$this->assertTrue($validationResult->passed);
		$this->assertTrue(count($validationResult->errors) == 0);
		$this->logger->debug("FINISH");
	}

	/**
     * @dataProvider getTableValidationColumnsMoreInDb
     */
	public function testTableValidationColumnsMoreInDb($table, $tableMetaFromDb) {
		$this->logger->debug("START");
		$stub = $this->createMock(DdlExecutor::class);
        $stub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
        $stub->method("tableExists")->willReturn(true); 

        $this->assertTrue($tableMetaFromDb == $stub->getTableMetaFromDb(new Table()));
        $this->assertEquals(true, $stub->tableExists($table->getName()));

        $validator = new Validator($stub);
        $validationResult = $validator->validate($table);

		$this->assertFalse($validationResult->passed);
		$this->assertTrue(count($validationResult->errors) > 0);
		$this->logger->debug("errors: " . var_export($validationResult->errors, true));
		
		$this->logger->debug("FINISH");
	}

	/**
     * @dataProvider getTableValidationColumnsMoreInXml
     */
	public function testTableValidationColumnsMoreInXml($table, $tableMetaFromDb) {
		$this->logger->debug("START");
		$stub = $this->createMock(DdlExecutor::class);
        $stub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
        $stub->method("tableExists")->willReturn(true); 

        $this->assertTrue($tableMetaFromDb == $stub->getTableMetaFromDb(new Table()));
        $this->assertEquals(true, $stub->tableExists($table->getName()));

        $validator = new Validator($stub);
        $validationResult = $validator->validate($table);

		$this->assertFalse($validationResult->passed);
		$this->assertTrue(count($validationResult->errors) > 0);
		$this->logger->debug("errors: " . var_export($validationResult->errors, true));
		$this->logger->debug("FINISH");
	}
    
    /**
     * @dataProvider getTableValidationColumnsHasDiffType
     */
	public function testTableValidationColumnsHasDiffType($table, $tableMetaFromDb) {
		$this->logger->debug("START");
		$stub = $this->createMock(DdlExecutor::class);
        $stub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
        $stub->method("tableExists")->willReturn(true); 

        $this->assertTrue($tableMetaFromDb == $stub->getTableMetaFromDb(new Table()));
        $this->assertEquals(true, $stub->tableExists($table->getName()));

        $validator = new Validator($stub);
        $validationResult = $validator->validate($table);

		$this->assertFalse($validationResult->passed);
		$this->assertTrue(count($validationResult->errors) > 0);
		$this->logger->debug("errors: " . var_export($validationResult->errors, true));
		$this->logger->debug("FINISH");
	}

    /**
     * @dataProvider getTableValidationColumnsOrderIsDifferent
     */
	public function testTableValidationColumnsOrderIsDifferent($table, $tableMetaFromDb) {
		$this->logger->debug("START");
		$stub = $this->createMock(DdlExecutor::class);
        $stub->method("getTableMetaFromDb")->willReturn($tableMetaFromDb);     
        $stub->method("tableExists")->willReturn(true); 

        $this->assertTrue($tableMetaFromDb == $stub->getTableMetaFromDb(new Table()));
        $this->assertEquals(true, $stub->tableExists($table->getName()));

        $validator = new Validator($stub);
        $validationResult = $validator->validate($table);

		$tableValidation = $this->getTableValidationColumnsOrderIsDifferent();
		$this->logger->trace("start check when getTableValidationColumnsOrderIsDifferent");
		$this->assertFalse($validationResult->passed);
		$this->logger->trace("finish check when getTableValidationColumnsOrderIsDifferent");
		$this->assertTrue(count($validationResult->errors) > 0);
		$this->logger->debug("errors: " . var_export($validationResult->errors, true));
		$this->logger->debug("FINISH");
	}
}

