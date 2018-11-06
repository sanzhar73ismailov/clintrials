<?php
use PHPUnit\Framework\TestCase;
use clintrials\admin\MetadataCreator;
use clintrials\admin\metadata\DbSchema;

require_once "configs/app_prop_test.php";

class MetadataCreatorTest extends TestCase {
	private $logger;
	
	public function setUp() {
		$this->logger = Logger::getLogger ( __CLASS__ );
	}
	
	public function testCreateDb() {
		$this->logger->debug("START");
		$this->assertTrue(defined("HOST"));
		$this->assertTrue(defined("DB_NAME"));
		$this->assertTrue(defined("DB_USER"));
		$this->assertTrue(defined("DB_PASS"));
		
		$this->assertTrue(strpos(DB_NAME, "test") !== false);
		
		$metadataCreator = new MetadataCreator (ClinTrialsTestHelper::TEST_XML);
		$this->assertNotNull($metadataCreator);
		$this->assertNotNull($metadataCreator->getXmlObj());
		$this->assertNotNull($metadataCreator->getDb());
		$this->assertEquals(DB_NAME, $metadataCreator->getDb()->getName());
		$this->assertInstanceOf('SimpleXMLElement', $metadataCreator->getXmlObj());
		$this->assertInstanceOf(DbSchema::class, $metadataCreator->getDb());
		$this->logger->debug("FINISH");
	}
}