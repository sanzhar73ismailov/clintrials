<?php
declare ( strict_types = 1 );
namespace clintrials\admin\metadata;
use clintrials\admin\metadata\Table;

class TableJrnl extends Table {
	private $table;
	
	public function __construct(Table $table) {
		$this->table = $table;
		$table->setTableJrnl($this);
	}
}