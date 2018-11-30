<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class AdviserAction {
	public $type; //add, remove, change
	public $field;
	public $after = ""; //if add or change

	public function __construct(string $type) {
			$this->type = $type;
	}
}