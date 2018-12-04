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
			$this->ddlExecutor = $ddlExecutor;
			$this->table = $table;
			$this->logger = Logger::getLogger(__CLASS__);
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

	private function fillActonsChange($tableMetaFromDb) {
		$this->logger->debug("START");
		$table = $this->table;
		$columnsNamesXml = $table->getFieldsName();
		$columnsNamesDb = $tableMetaFromDb->getFieldsName();
		$this->logger->trace('$columnsNamesXml=' . var_export($columnsNamesXml,true));
		$this->logger->trace('$columnsNamesDb=' . var_export($columnsNamesDb,true));

		foreach ($columnsNamesXml as $columnNameXml) {

			if(in_array($columnNameXml, $columnsNamesDb)) {
				$columnXml = $table->getFieldByName($columnNameXml);
				$columnDb = $tableMetaFromDb->getFieldByName($columnNameXml);

				/*
				$columnXmlBefore = $table->getFieldBefore($columnNameXml);
				$columnDbBefore = $tableMetaFromDb->getFieldBefore($columnNameXml);

				$columnXmlAfter = ((string) $columnXml) . ($columnXmlBefore ? $columnXmlBefore->getName() : "") ;
				$columnDbAfter = ((string) $columnDb) . ($columnDbBefore ? $columnDbBefore->column_name : "") ;
				*/


				if( ((string)$columnXml) != ((string)$columnDb) ) {
					$adviserAction = new AdviserAction("change");
				    $adviserAction->field = $columnXml;
				    $adviserAction->comment = $this->getWhyComment($columnXml, $columnDb);
				    //$adviserAction->after = $columnXmlBefore ?: "";
				    $this->logger->trace('adviserAction=' . var_export($adviserAction, true));
				    $this->actionsChange [] = $adviserAction;
				    
				}
			}
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



}