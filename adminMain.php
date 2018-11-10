<?php

require __DIR__ . '/vendor/autoload.php';
require_once "configs/app_prop.php";

use clintrials\admin\{
	DdlExecutor,
    MetadataCreator,
    ReportDb,
    ReportTables
};


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



//** un-comment the following line to show the debug console
//$smarty->debugging = true;



//echo "<h1>Hi</h1>";
//$log->debug("Hello World 2 - debug!");


$metadataCreator = new MetadataCreator ( "tests/clintrials_test.xml" );
$db = $metadataCreator->getDb();

$reportDb = ReportDb::createReport($metadataCreator, new DdlExecutor ( $db ));
//$reportTables = $reportDb->getReportTables();

//$smarty->assign('name','Ned');
$smarty->assign('reportDb',$reportDb);




// print_r($metadataCreator);
//echo "<pre>dbDdl=$metadataCreator->getDb()->getDdl()</pre>";

//echo "<pre>" . $metadataCreator->getDb()->getWholeDdl(). "</pre>";
//if (0) {
//	$ddlExecutor = new DdlExecutor ( $metadataCreator->getDb () );
	//$ddlExecutor->createDbWhole ();
//	$log->debug(( $ddlExecutor->dbExists () ));
//}
$smarty->display('index.tpl');
$log->trace("FINISH");