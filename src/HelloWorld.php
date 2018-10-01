<?php
namespace clintrials;

class HelloWorld{
	
	public function sayHello(){
		return "Hello";
	}
	
	public function sum($x1, $x2){
		return $x1 + $x2;
	}
	
	public function multiply($x1, $x2){
		return $x1 * $x2;
	}
	
	public function printHello(){
		echo "Hello";
	}
	
}