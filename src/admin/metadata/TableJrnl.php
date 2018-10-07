<?php

namespace clintrials\admin\metadata;
use clintrials\admin\metadata\Table;

class TableJrnl extends Table {
	private $table;
	
	public function __construct(Table $table) {
		$this->table = $table;
		$this->init();
	}
	
	private function init(){
		if(0){
			$this->table = new Table();
		}
		foreach ($this->table->getFields() as $field){
			if(0){
				$field = new Field();
			}
			$this->fields[] = $field->cloneField();
		}
		$field = new Field($this->name, $this->comment, $this->ddl, $this->type);
	}
	
}