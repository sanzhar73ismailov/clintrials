<?php

namespace clintrials\admin;

use Exception;
use Logger;
use clintrials\admin\metadata\DbSchema;
use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Field;
use clintrials\admin\metadata\TableJrnl;

libxml_use_internal_errors ( true );
class MetadataCreator {
	private $logger;
	private $xmlObj;
	private $db;
	function __construct($xml_file) {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->logger->debug ( "START" );
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
		$this->logger->debug ( "FINISH" );
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
		// $this->db->setName ( $this->xmlObj->prefix->__toString () );
		$this->db->setName ( DB_NAME ); // db name from configs/config_env.php
		$this->db->setComment ( $this->xmlObj->title->__toString () );
		$this->db->setDdl ( $this->buildCreateDb ( $this->db ) );
		foreach ( $this->xmlObj->data->investigations->children () as $investigation ) {
			$table = $this->fillTable ( $investigation );
			$tableJrnl = $table->createTableJrnl ();
			$this->db->addTable ( $table );
		}
	}
	private function fillTable($investigation) {
		$this->logger->debug ( "START" );
		$table = new Table ();
		$table->setName ( $this->getXmlObj ()->prefix . "_" . $investigation ['name'] );
		$table->setComment ( $investigation->title->__toString () );
		$table->addField ( $this->buildPkField ( 'id' ) );
		// $ddl .= "patient_id INTEGER(11) NOT NULL COMMENT 'Пациент',\n";
		// $ddl .= "visit_id INTEGER(11) NOT NULL COMMENT 'Визит',\n";
		// $this->fillField
		$table->addField ( $this->fillField ( 'patient_id', 'Пациент', 'int' ) );
		$table->addField ( $this->fillField ( 'visit_id', 'Визит', 'int' ) );
		foreach ( $investigation->fields->children () as $fieldArray ) {
			$table->addField ( $this->fillFieldFromArray( $fieldArray ) );
		}
		$table->addFields ( $this->buildServiceFields () );
		$this->logger->debug ( "start print fields" );
		foreach ( $table->getFields () as $field ) {
			$this->logger->debug ( "\$field=" . var_export ( $field, true ) );
		}
		$this->logger->debug ( "finish print fields" );
		$table->setDdl ( $this->buildCreateTableDdl ( $table ) );
		$table->setDdlJrnl ( $this->buildCreateJournalTableDdl ( $table ) );
		$this->logger->debug ( "FINISH" );
		return $table;
	}
	private function fillJrnlTable($table) {
	}
	private function fillFieldFromArray($fieldArray) {
		$name = $fieldArray ['name']->__toString ();
		$title = $fieldArray->title->__toString ();
		$type = $fieldArray->type->__toString ();
		return $this->fillField ( $name, $title, $type );
	}
	private function fillField($name, $title, $type) {
		$field = new Field ( $name, $title, $type );
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
		return $this->buildCreateTableHelp ( $table );
	}
	private function buildCreateJournalTableDdl($table) {
		return $this->buildCreateTableHelp ( $table, true );
	}
	private function buildCreateTableHelp($table, $is_journal = false) {
		$this->logger->debug ( "START" );
		if (0)
			$table = new Table ();
		$ddl = "";
		$id_column = "id";
		$table_name = $table->getName ();
		if ($is_journal) {
			$table_name = $table->getName () . "_jrnl";
			$id_column = "jrnl_id";
		}
		
		//$ddl .= sprintf ( "CREATE TABLE %s (\n %s INTEGER(11) AUTO_INCREMENT,\n", $table_name, $id_column );
		$ddl .= sprintf ( "CREATE TABLE %s (\n", $table_name);
		
		if ($is_journal) {
			$ddl .= "id INTEGER(11) NOT NULL, ";
		}
		// $ddl .= "patient_id INTEGER(11) NOT NULL COMMENT 'Пациент',\n";
		// $ddl .= "visit_id INTEGER(11) NOT NULL COMMENT 'Визит',\n";
		foreach ( $table->getFields () as $field ) {
			if (0) {
				$field = new Field ( '', '', '' );
			}
			if($field->getPk()){
				$ddl .= sprintf ( "%s INTEGER(11) AUTO_INCREMENT,\n", $field->getName() );
				break;
			}
		}
		foreach ( $table->getFields () as $field ) {
			if (0) {
				$field = new Field ( '', '', '' );
			}
			if ($field->getPk()) {
				continue;
			}
			$this->logger->debug ( "field=" . var_export ( $field, true ) );
			$ddl .= $field->getName () . "";
			switch ($field->getType ()) {
				case "date" :
					$ddl .= " DATE";
					break;
				case "float" :
					$ddl .= " float(11,2)";
					break;
				case "text" :
					$ddl .= " text";
					break;
				case "varchar" :
					$ddl .= " varchar(50)";
					break;
				case "timestamp" :
					$ddl .= " timestamp";
					break;
				case "int" :
				case "integer" :
				case "list" :
				case "boolean" :
					$ddl .= " INTEGER(11)";
					break;
				default :
					throw new Exception("type of field is unknown: " .$field->getType ());
			}
			
			if (!$field->getNull ()) {
				$ddl .= " NOT NULL ";
				
			} 
			if ($field->getDefault () != null) {
				if($field->getType () == 'timestamp'){
					$ddl .= " DEFAULT " . $field->getDefault() . " ";
				}else{
					$ddl .= " DEFAULT '" . $field->getDefault() . "' ";
				}
			} 
			$ddl .= " COMMENT '" . $field->getComment () . "',\n";
		}
		
		/*
		 * $ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
		 * $ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
		 * $ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
		 * $ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 * $ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 */
		if ($is_journal) {
			$ddl .= "insert_ind INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Индикатор вставки',\n ";
		}
		$ddl .= "PRIMARY KEY ($id_column)";
		foreach ( $table->getFields () as $field ) {
			if ($field->getType () == "list")
				$ddl .= ",\nKEY(" . $field->getName () . ")";
		}
		
		// UNIQUE KEY `patient_visit_uniq` (`patient_id`, `visit_id`),
		// KEY `patient_id` (`patient_id`),
		// KEY `visit_id` (`patient_id`),
		
		$ddl .= "\n) ENGINE=InnoDB\n";
		$ddl .= "AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		$this->logger->debug ( "FINISH, returned: " . $ddl );
		return $ddl;
	}
	public function buildPkField($name) {
		$pkField = new Field ( $name, 'PK', "int" );
		$pkField->setPk ( true );
		return $pkField;
	}
	public function buildServiceFields() {
		/*
		 * $ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
		 * $ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
		 * $ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
		 * $ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 * $ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 */
		$serviceFields = array ();
		
		$field = new Field ( "checked", 'Проверено монитором', 'boolean' );
		$field->setService ( true );
		$field->setNull ( false );
		$field->setDefault ( '0' );
		$serviceFields [] = $field;
		
		$field = new Field ( "user_insert", 'Пользователь, создавший', 'varchar' );
		$field->setService ( true );
		$field->setNull ( false );
		$field->setDefault ( 'no_user' );
		$serviceFields [] = $field;
		
		$field = new Field ( "user_update", 'Пользователь, обновивший', 'varchar' );
		$field->setService ( true );
		$field->setNull ( false );
		$field->setDefault ( 'no_user' );
		$serviceFields [] = $field;
		
		$field = new Field ( "insert_date", '', 'timestamp' );
		$field->setService ( true );
		$field->setNull ( false );
		$field->setDefault ( 'CURRENT_TIMESTAMP' );
		$serviceFields [] = $field;
		
		$field = new Field ( "update_date", '', 'timestamp' );
		$field->setService ( true );
		$field->setNull ( false );
		$field->setDefault ( 'CURRENT_TIMESTAMP' );
		$serviceFields [] = $field;
		
		return $serviceFields;
	}
}

?>