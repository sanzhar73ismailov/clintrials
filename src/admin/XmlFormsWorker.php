<?php
declare ( strict_types = 1 );
namespace clintrials\admin;

use Exception;
use Logger;
use SimpleXMLElement;
use clintrials\admin\metadata\{
	DbSchema,
	Table,
	Field,
	TableJrnl,
	Trigger
};
class XmlFormsWorker {
	private $logger;
	private $xmlObj;
	//private $db;
	function __construct($xmlObj) {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->logger->debug ( "START" );
		$this->$xmlObj = $xmlObj;
		$this->logger->debug ( "FINISH" );
	}

	function getXmlObj(){
		return $this->xmlObj;
	}
	function write
}
?>