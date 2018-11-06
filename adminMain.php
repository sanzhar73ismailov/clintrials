<?php

require __DIR__ . '/vendor/autoload.php';
require_once "configs/app_prop.php";

use clintrials\admin\DdlExecutor;
use clintrials\admin\MetadataCreator;


//$log = Logger::getRootLogger();

$log = Logger::getLogger('adminMain.php');
$log->trace("START");


$log->debug("hoster_var=" . $hoster_var);
$log->debug("Hello World - debug!");

$smarty = new Smarty();

$smarty->setTemplateDir('templates/');
$smarty->setCompileDir('templates_c/');
$smarty->setConfigDir('configs/');
$smarty->setCacheDir('cache/');

$smarty->assign('name','Ned');

//** un-comment the following line to show the debug console
//$smarty->debugging = true;

$smarty->display('index.tpl');

//echo "<h1>Hi</h1>";
//$log->debug("Hello World 2 - debug!");


//include_once 'MetadataCreator.php';
//include_once 'DdlExecutor.php';
//$xmlObj = \simplexml_load_file ( "clintrials.xml" ) or die ( "Error: Cannot create object" );

//$log->debug("defined(\"HOST\")=".defined("HOST"));
//$metadataCreator = new MetadataCreator ( "clintrials.xml" );
//$str = $metadataCreator->getDb()->getDdl();
// print_r($metadataCreator);
//echo "<pre>dbDdl=$metadataCreator->getDb()->getDdl()</pre>";

//echo "<pre>" . $metadataCreator->getDb()->getWholeDdl(). "</pre>";
//if (0) {
//	$ddlExecutor = new DdlExecutor ( $metadataCreator->getDb () );
	//$ddlExecutor->createDbWhole ();
//	$log->debug(( $ddlExecutor->dbExists () ));
//}
//$log->trace("FINISH");