<?php
class Db{
	public $name;
	public $tables = array();
	public $ddl;
}

class Table{
	public $name;
	public $fields = array();
	public $comment;
	public $ddl;
}

class Field{
	public $name;
	public $type;
	public $comment;
}