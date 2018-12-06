<?php
require_once "configs/app_prop_test.php";

use PHPUnit\Framework\TestCase;


class PhpBaseTest extends TestCase {
	private $logger;
	private $metadataCreator;
	
	public function setUp(){
		$this->logger = Logger::getLogger(__CLASS__);
	}

	public function testPhpFnctions() {
		$this->logger->debug("START");
		
		$this->assertTrue(1 == 1);
		$this->assertTrue(1 > 0);
		$this->assertTrue(true);
		$this->assertTrue(!false);
		$this->assertTrue(! false);
		$this->assertTrue(TRUE);
		$this->assertTrue((bool) " ");
		$this->assertTrue((bool) "0.0");
		$this->assertTrue((bool) "some text");
		$this->assertTrue((bool) 1);
		$this->assertTrue((bool) 10);
		$this->assertTrue((bool) 200);
		$this->assertTrue((bool) 0.1);
		$this->assertTrue((bool) 1.10);
		$this->assertTrue((bool) array(1,2,3));
		$this->assertTrue((bool) [1]);
		$this->assertTrue((bool) array("ss"));
		$this->assertTrue((bool) new A());


		$this->assertTrue("" ===  (string)FALSE);
		$this->assertTrue("1" ===  (string)TRUE);
		$this->assertTrue(0 ===  (int)FALSE);
		$this->assertTrue(1 ===  (int)TRUE);
		$this->assertTrue("0" ==  (int)FALSE);
		$this->assertTrue("1" ==  (int)TRUE);

		foreach ([] as $key => $value) {
			assertTrue(1 == 0);
		}


		$this->assertFalse(1 != 1);
		$this->assertFalse(1 == 0);
		$this->assertFalse(1 < 0);
		$this->assertFalse(!true);
		$this->assertFalse(false);
		$this->assertFalse(!TRUE);
		$this->assertFalse((bool) "");
		$this->assertFalse((bool) trim("  "));
		$this->assertFalse((bool) "0");
		$this->assertFalse((bool) NULL);
		$this->assertFalse((bool) 0);
		$this->assertFalse((bool) (10-10));
		$this->assertFalse((bool) 0.0);
		$this->assertFalse(!(bool) 1.10);
		$this->assertFalse((bool) array());
		$this->assertFalse((bool) []);
		$this->assertFalse(is_null(new A()));

		//ternar operator
		$this->assertTrue((1 ? 10 : 0) == 10);
		$this->assertTrue((bool) 1 ?: 0);
		$this->assertFalse( "" ?: (bool)0);





		$this->logger->debug("FINISH");
	}
	public function testArraMerge() {
		$arr1 = [1,2,3];
		$arr2 = [10,20,33];
		$arr_merged = array_merge($arr1, $arr2);
		$this->assertCount(3, $arr1);
		$this->assertCount(3, $arr2);
		$this->assertCount(6, $arr_merged);
		$this->assertEquals([1,2,3,10,20,33], $arr_merged);



	}
}

class A {
   private $field1;
}