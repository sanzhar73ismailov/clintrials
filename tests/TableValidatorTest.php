<?php

require_once "configs/app_prop_test.php";

use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;

use clintrials\admin\FieldMetaFromDb;
use clintrials\admin\ValidationResult;
use clintrials\admin\TableValidation;
use clintrials\admin\TableMetaFromDb;

use PHPUnit\Framework\TestCase;

class TableValidatorTest extends TestCase {
	private $logger;
	
	public function __construct() {
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function setUp(){
		
	}
	
	public function getTableValidationEqual() {
		$table = new Table();
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
		$tableMetaFromDb->columns[] = $column1;
		$tableMetaFromDb->columns[] = $column2;
		$tableMetaFromDb->columns[] = $column3;
		
		$tableValidation = new TableValidation($table, $tableMetaFromDb);
		return $tableValidation;
	}

	public function getTableValidationColumnsMoreInDb() {
		$table = new Table();
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
		$tableMetaFromDb->columns[] = $column1;
		$tableMetaFromDb->columns[] = $column2;
		$tableMetaFromDb->columns[] = $column3;
		$tableMetaFromDb->columns[] = $column4;
		
		$tableValidation = new TableValidation($table, $tableMetaFromDb);
		return $tableValidation;
	}
	
	public function testSomething() {
		$this->logger->debug("START");
		$this->assertTrue(1 == 1);
		$this->assertFalse(1 == 0);
	
		$tableValidation = $this->getTableValidationEqual();
		if(0){
		  $tableValidation = new TableValidation(null, null);
		}
		$validationResult = $tableValidation->validate();
		if(0){
			$validationResult = new ValidationResult();
		}
		$this->assertTrue($validationResult->passed);
		$this->assertTrue(count($validationResult->errors) == 0);
		
		$tableValidation = $this->getTableValidationColumnsMoreInDb();
		$validationResult = $tableValidation->validate();
		
		$this->assertFalse($validationResult->passed);
		$this->assertTrue(count($validationResult->errors) > 0);
		$this->logger->debug("errors: " . var_dump($validationResult->errors, true));
		
		$this->logger->debug("FINISH");
	}
}