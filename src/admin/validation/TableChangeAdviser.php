<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;
use clintrials\admin\DdlExecutor;
use Logger;
use Exception;

class TableChangeAdviser {
	private $logger;
	private $ddlExecutor;
	private $table;
	private $actionsAdd = array();
	private $actionsRemove = array();
	private $actionsChange = array();




	public function __construct(DdlExecutor $ddlExecutor, Table $table) {
		    $this->logger = Logger::getLogger(__CLASS__);
			$this->ddlExecutor = $ddlExecutor;
			$this->table = $table;
	}

	public function advise() : void {
		$this->logger->debug("START");

		$tableMetaFromDb = $this->ddlExecutor->getTableMetaFromDb($this->table);

		$columnsNamesXml = array ();
		$columnsNamesDb = array ();
		$columnsNamesXml = $this->table->getFieldsName();
		$columnsNamesDb = $tableMetaFromDb->getFieldsName();

		$this->fillActonsAdd($columnsNamesXml, $columnsNamesDb);
		$this->fillActonsRemove($columnsNamesXml, $columnsNamesDb);
		$this->fillActonsChange($tableMetaFromDb);

		$this->logger->debug("FINISH");

	}

	private function fillActonsAdd($columnsNamesXml, $columnsNamesDb) {
		$this->logger->debug("START");
		$table = $this->table;
		$resultDiff = array_diff($columnsNamesXml, $columnsNamesDb);
		$this->logger->trace('resultDiff=' . var_export($resultDiff, true));
		if(count($resultDiff)) {
			$this->logger->trace("in 1 ********* ");
			foreach ($resultDiff as $fieldNameToAdd) {
				$this->logger->trace("in 2 &&&&&&&&&&&&&& =". $fieldNameToAdd);
				$this->logger->trace('$fieldNameToAdd=' . $fieldNameToAdd);
				$adviserAction = new AdviserAction("add");
				$adviserAction->field = $table->getFieldByName($fieldNameToAdd);
				$fieldBefore = $table->getFieldBefore($fieldNameToAdd);
				if ($fieldBefore) {
				  $adviserAction->after = $table->getFieldBefore($fieldNameToAdd);	
				}

				$this->logger->trace('adviserAction=' . var_export($adviserAction, true));
		
				$this->actionsAdd [] = $adviserAction;
			}
		}
		$this->logger->trace('$this->actionsAdd=' . var_export($this->actionsAdd, true));
		$this->logger->debug("FINISH");
	}

	private function fillActonsRemove($columnsNamesXml, $columnsNamesDb) {
		$this->logger->debug("START");
		$table = $this->table;
		$resultDiff = array_diff($columnsNamesDb, $columnsNamesXml);
		$this->logger->trace('columnsNamesDb=' . var_export($columnsNamesDb, true));
		$this->logger->trace('columnsNamesXml=' . var_export($columnsNamesXml, true));
		$this->logger->trace('resultDiff=' . var_export($resultDiff, true));
		if(count($resultDiff)) {
			foreach ($resultDiff as $fieldNameToAdd) {
				$adviserAction = new AdviserAction("remove");
				$adviserAction->field = new Field($fieldNameToAdd, "", "");
				$this->actionsRemove [] = $adviserAction;
			}
		}
		$this->logger->debug("FINISH");
	}

	private function getChangeReplaces($tableMetaFromDb) {
		$table = $this->table;
		$columnsNamesXml = $table->getFieldsName();
		$columnsNamesDb = $tableMetaFromDb->getFieldsName();

		/*
               else{
					if($columnXml->getPrev() != $columnDb->getPrev()){
						$adviserAction = new AdviserAction("change");
					    $adviserAction->field = $columnXml;
					    $adviserAction->comment = "Position is not the same";
					    //$adviserAction->after = $columnXmlBefore ?: "";
					    $this->logger->trace('adviserAction=' . var_export($adviserAction, true));
					    $this->actionsChange [] = $adviserAction;
					}
				}
		*/

	}

	private function fillActonsChange($tableMetaFromDb) {
		$this->logger->debug("START");
		$table = $this->table;
		$columnsNamesXml = $table->getFieldsName();
		$columnsNamesDb = $tableMetaFromDb->getFieldsName();
		$this->logger->trace('$columnsNamesXml=' . var_export($columnsNamesXml,true));
		$this->logger->trace('$columnsNamesDb=' . var_export($columnsNamesDb,true));

		$isAllColumnsExistsGood = true;
		foreach ($columnsNamesXml as $columnNameXml) {

			if(in_array($columnNameXml, $columnsNamesDb)) {
				$columnXml = $table->getFieldByName($columnNameXml);
				$columnDb = $tableMetaFromDb->getFieldByName($columnNameXml);

				if( ((string)$columnXml) != ((string)$columnDb) ) {
					$isAllColumnsExistsGood = false;
					$adviserAction = new AdviserAction("change");
				    $adviserAction->field = $columnXml;
				    $adviserAction->comment = $this->getWhyComment($columnXml, $columnDb);
				    //$adviserAction->after = $columnXmlBefore ?: "";
				    $this->logger->trace('adviserAction=' . var_export($adviserAction, true));
				    $this->actionsChange [] = $adviserAction;
				}
			}
		}
		if($isAllColumnsExistsGood && (count($columnsNamesXml) == count($columnsNamesDb))){
			$this->getChangeReplaces($tableMetaFromDb);
		}
		$this->logger->debug("FINISH");
	}

	private function getWhyComment($columnXml, $columnDb) :string {
		$description = "";
		$description .= $columnXml->getComment() != $columnDb->column_comment ? "comments not equal" : "";
		$description .= $columnXml->getType() != $columnDb->data_type ? ($description ? ", type is not equal" : "type is not equal") : "";
		return $description;
	}


	public function getActionsAdd() : array {
		return $this->actionsAdd;
	}

	public function getActionsRemove() : array {
		return $this->actionsRemove;
	}

	public function getActionsChange() : array {
		return $this->actionsChange;
	}
	//array_merge
	public function getAllActions() : array {
		return array_merge($this->actionsAdd, $this->actionsRemove, $this->actionsChange);
	}

	public function applyActions(array $adviserActions) : void {
		$ddlExecutor = $this->ddlExecutor;
		if(!$adviserActions) {
			return;
		}
		$ddlExecutor->backupTable($this->table);

		/*
		AdviserAction 
	 	$type; //add, remove, change
	 	$field;
	 	$after = ""; //if add or change
	 	$comment = ""; //description of action cause
	 	*/
	 	foreach ($adviserActions as $action) {
	 		$this->logger->trace("action:" . var_export($action, true));
	 		if($action->type == "add") {
	 			//$action->field->after = $action->after;
	 			$ddlExecutor->addColumn($this->table->getName(), $action->field);
	 			$ddlExecutor->addColumn($this->table->getTableJrnl()->getName(), $action->field);
	 		}
	 		if($action->type == "remove") {
	 			$ddlExecutor->dropColumn($this->table->getName(), $action->field->getName());
	 			$ddlExecutor->dropColumn($this->table->getTableJrnl()->getName(), $action->field->getName());
	 			
	 		}
	 		if($action->type == "change") {
	 			$ddlExecutor->changeColumn($this->table->getName(), $action->oldName, $action->field);
	 			$ddlExecutor->changeColumn($this->table->getTableJrnl()->getName(), $action->oldName, $action->field);
	 		}
	 	}
	 	$ddlExecutor->createAllTriggers($this->table);
	 	/*
	 	$trigCreate = $ddlExecutor->createTrigger($this->table->getTriggerInsert());
	 	if(!$trigCreate){
	 		throw new Exception("Trigger " . $this->table->getTriggerInsert()->getName() . " was not created");
	 	}
	 	$trigCreate = $ddlExecutor->createTrigger($this->table->getTriggerUpdate());
	 	if(!$trigCreate){
	 		throw new Exception("Trigger " . $this->table->getTriggerUpdate()->getName() . " was not created");
	 	}
	 	*/
	}



}