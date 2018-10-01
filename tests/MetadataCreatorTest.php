<?php
use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;

Logger::configure("configs/log4php_tests.xml");

class A{
	public $field1;
	
}

class MetadataCreatorTest extends TestCase {
	private $logger;
	
	public function __construct() {
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function testCreateDb() {
		$this->logger->debug("START");
		$metadataCreator = new MetadataCreator ("tests/clintrials_test.xml");
		$this->assertNotNull($metadataCreator);
		$this->assertNotNull($metadataCreator->getXmlObj());
		$this->assertNotNull($metadataCreator->getDb());
		
		//$temp = new DbSchema();
	
		$this->assertEquals("clin_test", $metadataCreator->getDb()->getName());
		$this->assertInstanceOf('SimpleXMLElement', $metadataCreator->getXmlObj());
		$this->assertInstanceOf(DbSchema::class, $metadataCreator->getDb());
		
		
		//$this->assertTrue($a->field1 == null);
		//$this->assertNull($a->field1);

		$this->logger->debug("FINISH");
	}
}