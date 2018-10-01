<?php
require_once 'configs/config_dev.php';
use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\DdlExecutor;

Logger::configure("configs/log4php_tests.xml");

class A{
	public $field1;
	
}

class DdlExecutorTest extends TestCase {
	private $logger;
	
	public function __construct() {
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function testMetadataCreator() {
		$this->logger->debug("START");
		$metadataCreator = new MetadataCreator ("tests/clintrials_test.xml");
		$this->assertNotNull($metadataCreator);
		$this->assertNotNull($metadataCreator->getXmlObj());
		$this->assertNotNull($metadataCreator->getDb());
		
		$ddlExecutor = new DdlExecutor($metadataCreator->getDb());
		//$this->assertTrue($ddlExecutor->createDb());
		

		
		//$this->assertInstanceOf('SimpleXMLElement', $metadataCreator->getXmlObj());
		//$this->assertInstanceOf(DbSchema::class, $metadataCreator->getDb());
		
		
		//$this->assertTrue($a->field1 == null);
		//$this->assertNull($a->field1);

		$this->logger->debug("FINISH");
	}
}