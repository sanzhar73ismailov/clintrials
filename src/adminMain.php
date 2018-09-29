<?php

require __DIR__ . '/vendor/autoload.php';

use clintrials\admin\DdlExecutor;
use clintrials\admin\MetadataCreator;


//include_once 'MetadataCreator.php';
//include_once 'DdlExecutor.php';

//$xmlObj = \simplexml_load_file ( "clintrials.xml" ) or die ( "Error: Cannot create object" );

$metadataCreator = new MetadataCreator ( "clintrials.xml" );
$str = $metadataCreator->getDb()->getDdl();
echo "----<br>";
// print_r($metadataCreator);
echo "<pre>$str</pre>";
if (false) {
	$ddlExecutor = new DdlExecutor ( $metadataCreator->getDb () );
	$ddlExecutor->createDbWhole ();
	print_r ( $ddlExecutor->dbExists () );
}
echo "<br>----";