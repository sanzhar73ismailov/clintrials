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
	private $actonsAdd = array();
	private $actonsRemove = array();
	private $actonsChange = array();




	public function __construct(DdlExecutor $ddlExecutor) {
			$this->ddlExecutor = $ddlExecutor;
			$this->logger = Logger::getLogger(__CLASS__);
	}

	public function advise(Table $table) : void {
		$this->logger->debug("START");

		$tableMetaFromDb = $this->ddlExecutor->getTableMetaFromDb($table);

		$columnsNamesXml = array ();
		$columnsNamesDb = array ();
		$columnsNamesXml = $table->getFieldsName();
		$columnsNamesDb = $tableMetaFromDb->getFieldsName();

		$this->fillActonsAdd($table, $columnsNamesXml, $columnsNamesDb);
		$this->fillActonsRemove($table, $columnsNamesXml, $columnsNamesDb);
		$this->fillActonsChange($table, $columnsNamesXml, $columnsNamesDb);

		$this->logger->debug("FINISH");

	}

	private function fillActonsAdd($table, $columnsNamesXml, $columnsNamesDb) {
		$this->logger->debug("START");
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
		
				$this->actonsAdd [] = $adviserAction;
			}
		}
		$this->logger->trace('$this->actonsAdd=' . var_export($this->actonsAdd, true));
		$this->logger->debug("FINISH");
	}

	private function fillActonsRemove($table, $columnsNamesXml, $columnsNamesDb) {
		$this->logger->debug("START");
		$resultDiff = array_diff($columnsNamesDb, $columnsNamesXml);
		$this->logger->trace('columnsNamesDb=' . var_export($columnsNamesDb, true));
		$this->logger->trace('columnsNamesXml=' . var_export($columnsNamesXml, true));
		$this->logger->trace('resultDiff=' . var_export($resultDiff, true));
		if(count($resultDiff)) {
			foreach ($resultDiff as $fieldNameToAdd) {
				$adviserAction = new AdviserAction("remove");
				$adviserAction->field = new Field($fieldNameToAdd, "", "");
				$this->actonsRemove [] = $adviserAction;
			}
		}
		$this->logger->debug("FINISH");
	}

	private function fillActonsChange($table, $tableMetaFromDb) {
		/*
		$columnsNamesXml = $table->getFieldsName();
		$columnsNamesDb = $tableMetaFromDb->getFieldsName();
		
		
		foreach ($columnsNamesXml as $columnNameXml) {
			if(in_array($columnNameXml, $columnsNamesDb){
				$columnXml = $table->getFieldByName($columnNameXml);
				$columnDb = $tableMetaFromDb->getFieldByName($columnNameXml);
				if( ((string) $columnXml) != ((string) $columnDb) ) {
					$adviserAction = new AdviserAction("change");
				    $adviserAction->field = $columnXml;
				    $fieldBefore = $table->getFieldBefore($columnXml);
				    if ($fieldBefore) {
				       $adviserAction->after = $table->getFieldBefore($columnXml);	
				    }
				$this->logger->trace('adviserAction=' . var_export($adviserAction, true));
				$this->actonsChange [] = $adviserAction;
				}
			}
		}
		*/

	}


	public function getActonsAdd() : array {
		return $this->actonsAdd;
	}

	public function getActonsRemove() : array {
		return $this->actonsRemove;
	}

	public function getActonsChange() : array {
		return $this->actonsChange;
	}



}