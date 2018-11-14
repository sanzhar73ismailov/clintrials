<?php
require_once "configs/app_prop_test.php";

class ClinTrialsTestHelper {
	
	const TEST_XML = "tests\clintrials_test.xml";


	public static function generateRandomString($length = 10) : string {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public static function getRandomType() : string {
		//$logger = Logger::getLogger(__CLASS__);
	    $types = explode('|', 'date|float|text|varchar|int|list|boolean|timestamp');
	    $type = $types[rand(0, count($types)-1)];
	    return $type;
	}

	public static function getTrueOrFalse() : bool {
	    return rand(0,1) ==1;
	}

}
