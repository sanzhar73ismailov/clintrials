<?php
use PHPUnit\Framework\TestCase;
use clintrials\HelloWorld;

require_once "configs/app_prop_test.php";

class HelloWorldTest extends TestCase {
	private static $logger;
	
	public function __construct() {
		// The __CLASS__ constant holds the class name, in our case "Foo".
		// Therefore this creates a logger named "Foo" (which we configured in the config file)
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function testSayHello() {
		$this->logger->debug("START");
		$helloWorld = new HelloWorld ();
		$this->assertEquals ( 'Hello', $helloWorld->sayHello () );
		$this->logger->debug("FINISH");
	}
	
	public function testSum() {
		$this->logger->debug("START");
		$helloWorld = new HelloWorld ();
		$this->assertEquals ( 234, $helloWorld->sum(12, 222) );
		$this->logger->debug("FINISH");
	}
	
	public function testMultiply() {
		$this->logger->debug("START");
		$helloWorld = new HelloWorld ();
		$this->assertEquals ( 36, $helloWorld->multiply(12, 3) );
		$this->logger->debug("FINISH");
	}
	
	public function testExpectHelloActualHello()
	{
		$this->logger->debug("START");
		$helloWorld = new HelloWorld ();
		$this->expectOutputString('Hello');
		$helloWorld->printHello();
		$this->logger->debug("this->getActualOutput()=<<<" . $this->getActualOutput() . ">>>");
		$this->logger->debug("FINISH");
	}
	
}