<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class AdviserAction {
	public $type; //add, remove, change
	public $field;
	public $after = ""; //if add or change
	public $comment = ""; //description of action cause

	public function __construct(string $type='') {
			$this->type = $type;
	}

	public static function buildAdviserAction($type, $field, $after, $comment = '') : AdviserAction{
		$obj = new AdviserAction();
		$obj->type = $type;
		$obj->field = $field;
		$obj->after = $after;
		$obj->comment = $comment;
		return $obj;
	}

	/*
     'id' => '0',
     'field' => 'sex_id',
     'initial_type' => 'add',
     'type' => 'add',
     'after' => 'instr_mrt_descr',
	*/


	public static function convertJsonToAdviserActionArray($json){
		$adviserActionArray = [];
		$array = json_decode($json);
		foreach ($array as $item) {
			$adviserActionArray[] = self::buildAdviserAction($item->type, $item->field, $item->after, '');
		}
		return $adviserActionArray;
	}
}