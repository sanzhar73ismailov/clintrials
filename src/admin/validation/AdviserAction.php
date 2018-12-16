<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

use clintrials\admin\metadata\Field;
use Logger;

class AdviserAction {
	private $logger;
	public $type; //add, remove, change
	public $field;
	public $oldName; // for change actions
	public $comment = ""; //description of action cause

	public function __construct(string $type='') {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->type = $type;
	}

	public static function buildAdviserAction($type, $field, $comment = '', $oldName = '') : AdviserAction{
		$obj = new AdviserAction();
		$obj->type = $type;
		$obj->field = $field;
		$obj->comment = $comment;
		$obj->oldName = $oldName;
		return $obj;
	}

	/*
     'id' => '0',
     'field' => 'sex_id',
     'initial_type' => 'add',
     'type' => 'add',
     'after' => 'instr_mrt_descr',
     [{
     "id":"1",
     "field":"field11",
     "initial_type":"remove",
     "type":"change",
     "to":"sex_id",
     "after":""}]
	*/


	public static function convertJsonToAdviserActionArray($table, $json){
		$adviserActionArray = [];
		$array = json_decode($json);
		foreach ($array as $item) {
			$field = null;
			$oldName = "";
			$after = "";
			// var_dump($item);
			// echo "<br/>";
			if ($item->type == 'change') {
				$field = $table->getFieldByName($item->to);
				$oldName = $item->field;
			} elseif ($item->type == 'remove') {
				$field = new Field($item->field);
			} else {
				$field = $table->getFieldByName($item->field);
			}


			$field->setAfter($field->getPrev() ?: "");
			
			$adviserAction = self::buildAdviserAction($item->type, $field, $item->type . ' action', $oldName);

			$adviserActionArray[] = $adviserAction;
		}
		return $adviserActionArray;
	}
}