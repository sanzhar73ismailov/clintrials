<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class AdviserAction {
	public $type; //add, remove, change
	public $field;
	public $oldName; // for change actions
	public $after = ""; //if add or change
	public $comment = ""; //description of action cause

	public function __construct(string $type='') {
			$this->type = $type;
	}

	public static function buildAdviserAction($type, $field, $after, $comment = '', $oldName = '') : AdviserAction{
		$obj = new AdviserAction();
		$obj->type = $type;
		$obj->field = $field;
		$obj->after = $after;
		$obj->comment = $comment;
		$obj->$oldName = $oldName;
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
			if($item->type == 'change'){
				$field = $table->getFieldByName($item->to);
				$oldName = $item->field;
			}else{
				$field = $table->getFieldByName($item->field);
			}
			$field = $table->getFieldByName($item->field);

			$adviserActionArray[] = self::buildAdviserAction($item->type, $field, $item->after, '', $oldName);
		}
		return $adviserActionArray;
	}
}