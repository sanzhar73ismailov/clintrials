<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

class TableMetaFromDb {
	private $columns = array ();



	public function addColumn($column){
		if (count($this->columns)) {
			$lastColumn = $this->columns[count($this->columns) - 1];
			$lastColumn->next = $column->column_name;
			$column->prev = $lastColumn->column_name;
			//$field->setAfter($this->fields[count($this->fields) - 1]->getName());
		}
		$this->columns[] = $column;
	}

	public function addColumns($columns) {
	    foreach ($columns as $column) {
	    	$this->addColumn($column);
	    }
    }

	public function getColumns() {
		return $this->columns;
	}

	public function setColumns($columns) {
		$this->columns = array();
		$this->addColumns($columns);
	}



	public function getFieldByName(string $name) {
		foreach ( $this->columns as $field ) {
			if ($field->column_name == $name) {
				return $field;
			}
		}
		return false;
	}

	public function getFieldsName() : array {
		$columnNames = array();
		foreach ( $this->columns as $field ) {
			$columnNames [] = $field->column_name;
		}
		return $columnNames;
	}
	
	public function getFieldNameTypeComments() : array {
		$columnNames = array();
		foreach ( $this->columns as $field ) {
			$columnNames [] = (string) $field;
		}
		return $columnNames;
	}

	public function getFieldBefore(string $name) {
		$filedBefore = new FieldMetaFromDb("", "", "");
		foreach ( $this->columns as $field ) {
			if($field->column_name == $name) {
				break;
			}
			$filedBefore = $field;
		}
		return $filedBefore;
	}
}