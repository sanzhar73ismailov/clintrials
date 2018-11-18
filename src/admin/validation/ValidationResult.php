<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class ValidationResult {
	
	public $passed = true;
	public $errors = array (); // array of error messages
}