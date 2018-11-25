<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class ValidationResult {
	
	public $objectExists = false;
	public $passed = false;
	public $errors = array (); // array of error messages
}