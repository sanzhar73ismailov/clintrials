<?php

namespace clintrials\admin;

use clintrials\admin\metadata\DbSchema;
use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;

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
		// print_r ( $this->db );
	}
	
	/**
	 *
	 * @return the $xmlObj
	 */
	public function getXmlObj() {
		return $this->xmlObj;
	}
	
	/**
	 *
	 * @return the $db
	 */
	public function getDb() {
		return $this->db;
	}
	
	/**
	 *
	 * @param SimpleXMLElement $xmlObj
	 */
	public function setXmlObj($xmlObj) {
		$this->xmlObj = $xmlObj;
	}
	
	/**
	 *
	 * @param Db $db
	 */
	public function setDb($db) {
		$this->db = $db;
	}
	private function fillDb() {
		$this->db = new DbSchema ();
		//$this->db->setName ( $this->xmlObj->prefix->__toString () );
		$this->db->setName ( DB_NAME); //db name from configs/config_env.php 
		$this->db->setComment ( $this->xmlObj->title->__toString () );
		$this->db->setDdl ( $this->buildCreateDb ( $this->db ) );
		foreach ( $this->xmlObj->data->investigations->children () as $investigation ) {
			$this->db->addTable ( $this->fillTable ( $investigation ) );
		}
	}
	private function fillTable($investigation) {
		$table = new Table ();
		$table->setName ( $this->getXmlObj()->prefix . "_" . $investigation ['name'] );
		$table->setComment ( $investigation->title->__toString () );
		foreach ( $investigation->fields->children () as $fieldArray ) {
			$table->addField ( $this->fillField ( $fieldArray ) );
		}
		$table->setDdl ( $this->buildCreateTableDdl ( $table ) );
		return $table;
	}
	private function fillField($fieldArray) {
		$field = new Field ();
		$field->setName ( $fieldArray ['name']->__toString () );
		$field->setType ( $fieldArray->type->__toString () );
		$field->setComment ( $fieldArray->title->__toString () );
		return $field;
	}
	// public function buildDdl() {
	// $ddl = "";
	// foreach ( $this->xmlObj->data->investigations->children () as $investigation ) {
	// $ddl .= $this->buildCreateTableDdl ( $investigation ) . "\n";
	// }
	// return $ddl;
	// }
	private function buildCreateDb($db) {
		$template = "CREATE DATABASE `%s` CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		return sprintf ( $template, $db->getName () );
	}
	
	private function buildCreateTableDdl($table) {
		return $this->buildCreateTableHelp($table);
	}
	
	private function buildCreateJournalTableDdl($table) {
		return $this->buildCreateTableHelp($table, true);
	}
	private function buildCreateTableHelp($table, $is_journal=false) {
		if (0)
			$table = new Table ();
			$ddl = "";
			$id_column = "id";
			$table_name = $table->getName ();
			if($is_journal){
				$table_name = $table->getName () . "_jrnl";
			    $id_column =  "jrnl_id";
			}
			
			$ddl .= sprintf ( "CREATE TABLE %s (\n %s INTEGER(11) AUTO_INCREMENT,\n", $table_name ,$id_column  );
			if($is_journal){
				$ddl .= "id INTEGER(11) NOT NULL, ";
			}
			$ddl .= "patient_id INTEGER(11) NOT NULL COMMENT 'Пациент',\n";
			$ddl .= "visit_id INTEGER(11) NOT NULL COMMENT 'Визит',\n";
			foreach ( $table->getFields () as $field ) {
				if(0)
					$field = new Field();
					$ddl .= $field->getName () . "";
					switch ($field->getType()){
						case "date":
							$ddl .= " DATE";
							break;
						case "float":
							$ddl .= " float(11,2)";
							break;
						case "text":
							$ddl .= " text";
							break;
						case "varchar":
							$ddl .= " varchar(50)";
							break;
						default:
							$ddl .= " INTEGER(11)";
							break;
					}
					
					$ddl .= " COMMENT '" . $field->getComment(). "',\n";
			}
			$ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
			$ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
			$ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
			$ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
			$ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
			
			$ddl .= "PRIMARY KEY ($id_column)";
			foreach ( $table->getFields () as $field ) {
				if ($field->getType() == "list")
					$ddl .= ",\nKEY(" . $field->getName() . ")";
			}
			
			//UNIQUE KEY `patient_visit_uniq` (`patient_id`, `visit_id`),
			//KEY `patient_id` (`patient_id`),
			//KEY `visit_id` (`patient_id`),
			
			$ddl .= "\n) ENGINE=InnoDB\n";
			$ddl .= "AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
			return $ddl;
	}
	
}

?>