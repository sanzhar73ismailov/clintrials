<?php

require __DIR__ . '/vendor/autoload.php';
require_once "configs/app_prop.php";

use clintrials\admin\{
	DdlExecutor,
    MetadataCreator,
    ReportDb,
    ReportTables
};
use clintrials\admin\validation\Validator;
use clintrials\admin\validation\ValidationResult;
use clintrials\admin\validation\TableChangeAdviser;
use clintrials\admin\validation\AdviserAction;


//$logger = Logger::getRootLogger();

$logger = Logger::getLogger('adminMain.php');
$logger->trace("START");


$logger->debug("hoster_var=" . $hoster_var);
$logger->debug("Hello World - debug!");

$smarty = new Smarty();

$smarty->setTemplateDir('templates/');
$smarty->setCompileDir('templates_c/');
$smarty->setConfigDir('configs/');
$smarty->setCacheDir('cache/');



//** un-comment the following line to show the debug console
//$smarty->debugging = true;



//echo "<h1>Hi</h1>";
//$logger->debug("Hello World 2 - debug!");


$metadataCreator = new MetadataCreator ( "tests/clintrials_test.xml" );
$db = $metadataCreator->getDb();
$ddlExecutor = new DdlExecutor ( $db );
$tpl = 'index.tpl';
// for testins use -- start clin_test_lab DDL - with errors for testing from tests/scr_for_tests.sql
if (isset($_REQUEST['editTable'])) {
	$tpl = 'tableEdit.tpl';
	$table = $db->getTable($_REQUEST['editTable']);
	$tableMetaFromDb = $ddlExecutor->getTableMetaFromDb($table);
	$validator = new Validator($ddlExecutor);
	$validationResult = $validator->validate($table);
	$tableChangeAdviser = new TableChangeAdviser($ddlExecutor, $table);
    $tableChangeAdviser->advise();
	$smarty->assign('table', $table);
	$smarty->assign('tableMetaFromDb', $tableMetaFromDb);
	$smarty->assign('validationResult', $validationResult);
	$smarty->assign('tableChangeAdviser', $tableChangeAdviser);
	$logger->trace('$ddlExecutor->getTableMetaFromDb($table)=' . var_export($tableMetaFromDb, true));
} else {
	
	if (isset($_REQUEST['updateTableButton'])) {
		$table = $db->getTable($_REQUEST['table']);
		$jsonDecode = json_decode($_REQUEST['jsonActions']);
		$logger->debug("jsonDecode=" . var_export($jsonDecode, true));
		$tableChangeAdviser = new TableChangeAdviser($ddlExecutor, $table);
		if(isset($_REQUEST['reorder'])) {
			$tableChangeAdviser->reorderTable();
		} else {
			$adviserActions = AdviserAction::convertJsonToAdviserActionArray($table,$_REQUEST['jsonActions']);
			$logger->debug("adviserActions=" . var_export($adviserActions, true));
	        $tableChangeAdviser->applyActions($adviserActions);
        }
	} elseif (isset($_REQUEST['recreateTriggers'])) {
		$ddlExecutor->createAllTriggers($db->getTable($_REQUEST['table']));
	} elseif (isset($_REQUEST['recreateTable'])) {
		$logger->debug("REQUEST['recreateTable'] is flow");
		$ddlExecutor->reCreateTable($db->getTable($_REQUEST['table']));
	} elseif (isset($_REQUEST['createTable'])) {
		$logger->debug("REQUEST['createTable'] is flow");
		$ddlExecutor->createPackTable($db->getTable($_REQUEST['table']));
	}

	//createAllTables
	if (isset($_REQUEST['createAllTables'])) {
		$ddlExecutor->createAllTables();
	}


	//$logger->trace("ddlExecutor=" . var_export($ddlExecutor, true));
	$reportDb = ReportDb::createReport($metadataCreator, $ddlExecutor);
	$smarty->assign('reportDb',$reportDb);
}

$smarty->display($tpl);
//$logger->debug(var_export($reportDb, true) );
//$reportTables = $reportDb->getReportTables();

//$smarty->assign('name','Ned');





// print_r($metadataCreator);
//echo "<pre>dbDdl=$metadataCreator->getDb()->getDdl()</pre>";

//echo "<pre>" . $metadataCreator->getDb()->getWholeDdl(). "</pre>";
//if (0) {
//	$ddlExecutor = new DdlExecutor ( $metadataCreator->getDb () );
	//$ddlExecutor->createDbWhole ();
//	$logger->debug(( $ddlExecutor->dbExists () ));
//}

$logger->trace("FINISH");
