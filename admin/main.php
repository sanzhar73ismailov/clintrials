<?php
include_once 'parsexml.php';
include_once 'createdb.php';

$metadataCreator = new MetadataCreator ( "../clintrials.xml" );
//$str = $metadataCreator->buildDdl ();
echo "----<br>";
//print_r($metadataCreator);
// echo "<pre>$str</pre>";
$ddlExecutor = new DdlExecutor($metadataCreator->getDb());
//$ddlExecutor->createDbWhole();
print_r($ddlExecutor->dbExists());
echo "<br>----";