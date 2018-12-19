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

class TableTest extends TestCase {
	private $logger;
	
	public function setUp(){
		$this->logger = Logger::getLogger(__CLASS__);
	}

	/**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionAddFieldWithEmptyName() {
    	$table = new Table("t1");
    	$table->addField(new Field(""));
    }

     /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionAddFieldWithNotValidName01() {
    	$table = new Table("t1");
    	$table->addField(new Field("1colname"));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionAddFieldWithNotValidName02() {
    	$table = new Table("t1");
    	$table->addField(new Field("1colname"));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionAddFieldWithNotValidName03() {
    	$table = new Table("t1");
    	$table->addField(new Field("c#olname"));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionAddFieldWithNotValidName04() {
    	$table = new Table("t1");
    	$table->addField(new Field("co%lname"));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionAddFieldWithNotValidName05() {
    	$table = new Table("t1");
    	$table->addField(new Field("co^lname"));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionAddFieldWithNotValidName06() {
    	$table = new Table("t1");
    	$table->addField(new Field('co$lname'));
    }
   

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIfTwoFieldsWithOneName() {
	    $table = new Table("t1");
	    $table->addField(new Field("f1"));
	    $table->addField(new Field("f1"));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIfTwoFieldsWithOneName02() {
	    $table = new Table("t1");
	    $table->addField(new Field("f1"));
	    $table->addField(new Field("F1"));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIfTwoFieldsWithOneName03() {
	    $table = new Table("t1");
	    $table->addField(new Field("f1"));
	    $table->addField(new Field(" f1 ")); // spaces, check trim
    }

     /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIfTwoFieldsWithOneName04() {
	    $table = new Table("t1");
	    $table->addField(new Field("f1"));
	    $table->addField(new Field(" F1 ")); // spaces, check trim and strlower
    }

	public function testAddField() {
		$this->logger->debug("START");

	    $table = new Table("t1");
	    $this->assertCount(0, $table->getFields());
	    $table->addField(new Field("f1"));
	    $this->assertCount(1, $table->getFields());
	    $table->addField(new Field("   F2"));
	    $this->assertCount(2, $table->getFields());
	    $this->assertEquals('f2', $table->getFields()[1]->getName());


	    $table = new Table("t1");
	    $table->addField(new Field("f1"));
	    $table->addField(new Field("f2"));
	    $table->addField(new Field("f3"));
	    $table->addField(new Field("f4"));
	    $table->addField(new Field("f5"));
	    $this->assertCount(5, $table->getFields());
        
        $field = 'f1';
	    $this->assertEquals(NULL, $table->getFieldByName($field)->getPrev());
	    $this->assertEquals('f2', $table->getFieldByName($field)->getNext());
	     $field = 'f2';
	    $this->assertEquals('f1', $table->getFieldByName($field)->getPrev());
	    $this->assertEquals('f3', $table->getFieldByName($field)->getNext());
	     $field = 'f3';
	    $this->assertEquals('f2', $table->getFieldByName($field)->getPrev());
	    $this->assertEquals('f4', $table->getFieldByName($field)->getNext());
	     $field = 'f4';
	    $this->assertEquals('f3', $table->getFieldByName($field)->getPrev());
	    $this->assertEquals('f5', $table->getFieldByName($field)->getNext());
	     $field = 'f5';
	    $this->assertEquals('f4', $table->getFieldByName($field)->getPrev());
	    $this->assertEquals(NULL, $table->getFieldByName($field)->getNext());

	    $this->assertEquals('f1', $table->getFirstField($field)->getName());
	    $this->assertEquals('f5', $table->getLastField($field)->getName());


		$this->logger->debug("FINISH");
	}

	public function testGetFieldIndex() {
		$this->logger->debug("START");
	    $this->assertTrue(1==1);

	    $table = new Table("t1");
	    $table->addField(new Field("f1"));
	    $table->addField(new Field("f2"));
	    $table->addField(new Field("f3"));
	    $table->addField(new Field("f4"));
	    $table->addField(new Field("f5"));
	    $this->assertCount(5, $table->getFields());
        
        $field = 'f1';
	    $this->assertEquals(0, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(0, $table->getFieldIndexByName($field));
	    $field = 'f2';
	    $this->assertEquals(1, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(1, $table->getFieldIndexByName($field));
	    $field = 'f3';
	    $this->assertEquals(2, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(2, $table->getFieldIndexByName($field));
	    $field = 'f4';
	    $this->assertEquals(3, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(3, $table->getFieldIndexByName($field));
	    $field = 'f5';
	    $this->assertEquals(4, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(4, $table->getFieldIndexByName($field));

	    $field = 'f_not_exists_field';
	    $this->assertEquals(-1, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(-1, $table->getFieldIndexByName($field));

	    $this->assertEquals(0, $table->getFieldIndex($table->getFirstField()));
	    $this->assertEquals(4, $table->getFieldIndex($table->getLastField()));


		$this->logger->debug("FINISH");
	}
	//reorderField



	public function testReorderField() {
		$this->logger->debug("START");
	    $this->assertTrue(1==1);

	    $table = new Table("t1");
	    $table->addField(new Field("f1"));
	    $table->addField(new Field("f3"));
	    $table->addField(new Field("f4"));
	    $table->addField(new Field("f2"));
	    $table->addField(new Field("f5"));
	    $this->assertCount(5, $table->getFields());

	    $this->logger->debug("before order: " . $table->filedsToString());

	    $table->reorderField(new Field("f2"), "f1");

	    $this->logger->debug("after order: " . $table->filedsToString());

        
        
        $field = 'f1';
	    $this->assertEquals(0, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(0, $table->getFieldIndexByName($field));
	    $field = 'f2';
	    $this->assertEquals(1, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(1, $table->getFieldIndexByName($field));
	    $field = 'f3';
	    $this->assertEquals(2, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(2, $table->getFieldIndexByName($field));
	    $field = 'f4';
	    $this->assertEquals(3, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(3, $table->getFieldIndexByName($field));
	    $field = 'f5';
	    $this->assertEquals(4, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(4, $table->getFieldIndexByName($field));

	    $field = 'f_not_exists_field';
	    $this->assertEquals(-1, $table->getFieldIndex(new Field($field)));
	    $this->assertEquals(-1, $table->getFieldIndexByName($field));

	    $this->assertEquals(0, $table->getFieldIndex($table->getFirstField()));
	    $this->assertEquals(4, $table->getFieldIndex($table->getLastField()));
         

		$this->logger->debug("FINISH");
	}

}