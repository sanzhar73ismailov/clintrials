<?php
include "metadata.php";
libxml_use_internal_errors ( true );
class MetadataCreator {
	private $xmlObj;
	private $db;
	function __construct($xml_file) {
		$this->xmlObj = simplexml_load_file ( $xml_file ) or die ( "Error: Cannot create object" );
		if ($this->xmlObj === false) {
			echo "Failed loading XML: ";
			foreach ( libxml_get_errors () as $error ) {
				echo "<br>", $error->message;
			}
			exit ();
		}
		$this->fillDb ();
		//print_r ( $this->db );
	}
	
	/**
	 * @return the $xmlObj
	 */
	public function getXmlObj() {
		return $this->xmlObj;
	}

	/**
	 * @return the $db
	 */
	public function getDb() {
		return $this->db;
	}

	/**
	 * @param SimpleXMLElement $xmlObj
	 */
	public function setXmlObj($xmlObj) {
		$this->xmlObj = $xmlObj;
	}

	/**
	 * @param Db $db
	 */
	public function setDb($db) {
		$this->db = $db;
	}

	function fillDb() {
		$this->db = new Db ();
		$this->db->name = $this->xmlObj->prefix->__toString ();
		$this->db->comment = $this->xmlObj->title->__toString ();
		$this->db->ddl = $this->buildCreateDb($this->db);
		foreach ( $this->xmlObj->data->investigations->children () as $investigation ) {
			$this->db->tables [] = $this->fillTable ( $investigation );
		}
	}
	function fillTable($investigation) {
		$table = new Table ();
		$table->name = $this->xmlObj->prefix . "_" . $investigation ['name'];
		$table->comment = $investigation->title->__toString ();
		foreach ( $investigation->fields->children () as $fieldArray ) {
			$table->fields [] = $this->fillField ( $fieldArray );
		}
		$table->ddl = $this->buildCreateTableDdl($table);
		return $table;
	}
	function fillField($fieldArray) {
		$field = new Field ();
		$field->name = $fieldArray ['name']->__toString ();
		$field->type = $fieldArray->type->__toString ();
		$field->comment = $fieldArray->title->__toString ();
		return $field;
	}
// 	public function buildDdl() {
// 		$ddl = "";
// 		foreach ( $this->xmlObj->data->investigations->children () as $investigation ) {
// 			$ddl .= $this->buildCreateTableDdl ( $investigation ) . "\n";
// 		}
// 		return $ddl;
// 	}
	function buildCreateDb($db) {
		$template = "CREATE DATABASE `%s` CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		return sprintf ( $template, $db->name );
	}
	
	private function buildCreateTableDdl($table) {
		if (0)
			$table = new Table ();
		$ddl = "";
		$ddl .= sprintf ( "CREATE TABLE %s (\nid INT AUTO_INCREMENT,\n", $table->name );
		foreach ( $table->fields as $field ) {
			$ddl .= $field->name . "";
			if ($field->type == "list")
				$ddl .= " INT";
			else
				$ddl .= " " . $field->type . "";
			
			if ($field->type == "float")
				$ddl .= "(11,2)";
			
			$ddl .= " COMMENT '" . $field->title . "',\n";
		}
		$ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
		$ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
		$ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
		$ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		$ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		
		$ddl .= "PRIMARY KEY (id)";
		foreach ( $table->fields as $field ) {
			if ($field->type == "list")
				$ddl .= ",\nKEY(" . $field->name . ")";
		}
		$ddl .= "\n) ENGINE=InnoDB\n";
		$ddl .= "AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		return $ddl;
	}
}

?>