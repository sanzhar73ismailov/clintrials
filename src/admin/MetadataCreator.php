<?php
declare ( strict_types = 1 );
namespace clintrials\admin;

use Exception;
use Logger;
use SimpleXMLElement;
use clintrials\admin\metadata\{
	DbSchema,
	Table,
	Field,
	TableJrnl,
	Trigger
};

libxml_use_internal_errors ( true );

class MetadataCreator {
	private $logger;
	private $xmlObj;
	private $db;
	function __construct(string $xml_file) {
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
		$this->logger->debug ( "FINISH" );
	}
	
	/**
	 *
	 * @return the $xmlObj
	 */
	public function getXmlObj() : SimpleXMLElement {
		return $this->xmlObj;
	}
	
	/**
	 *
	 * @return the $db
	 */
	public function getDb() : DbSchema{
		return $this->db;
	}
	
	/**
	 *
	 * @param SimpleXMLElement $xmlObj
	 */
	public function setXmlObj(SimpleXMLElement $xmlObj) {
		$this->xmlObj = $xmlObj;
	}
	
	/**
	 *
	 * @param Db $db
	 */
	public function setDb(DbSchema $db) : void {
		$this->db = $db;
	}
	private function fillDb() : void {
		$this->db = new DbSchema ();
		// $this->db->setName ( $this->xmlObj->prefix->__toString () );
		$this->db->setName ( DB_NAME ); // db name from configs/config_env.php
		$this->db->setComment ( $this->xmlObj->title->__toString () );
		$this->db->setDdl ( $this->buildCreateDb ( $this->db ) );
		foreach ( $this->xmlObj->data->investigations->children () as $investigation ) {
			$table = $this->fillTable ( $investigation );
			$tableJrnl = $this->fillJrnlTable ( $table );
			
			$table->setDdl ( $this->buildCreateTableDdl ( $table ) );
			$tableJrnl->setDdl ( $this->buildCreateTableDdl ( $tableJrnl ) );
			$this->buildTriggers($table);
			$this->db->addTable ( $table );
		}
	}
	
	private function fillTable($investigation) : Table {
		$this->logger->debug ( "START" );
		$table = new Table ();
		$table->setName ( $this->getXmlObj ()->prefix . "_" . $investigation ['name'] );
		$table->setComment ( $investigation->title->__toString () );
		$table->addField ( $this->buildPkField ( 'id' ) );
		if($investigation ['name'] == 'patient') {
			$table->setPatient(true);
		}
		if(!$table->isPatient()) {
			$table->addField ( $this->fillField ( 'patient_id', 'Пациент', 'int' ) );
			$table->addField ( $this->fillField ( 'visit_id', 'Визит', 'int' ) );
		}
		
		foreach ( $investigation->fields->children () as $fieldArray ) {
			$table->addField ( $this->fillFieldFromArray ( $fieldArray ) );
		}
		$table->addFields ( $this->buildServiceFields () );
		$this->logger->debug ( "start print fields<<<" );
		foreach ( $table->getFields () as $field ) {
			$this->logger->debug ( "\$field=" . var_export ( $field, true ) );
		}
		$this->logger->debug ( ">>>finish print fields" );
		$this->logger->debug ( "FINISH" );
		return $table;
	}
	private function fillJrnlTable(Table $table) : TableJrnl {
		$tableJrnl = new TableJrnl ( $table );
		$tableJrnl->setPatient($table->isPatient());
		$tableJrnl->setName($table->getName() . '_jrnl');
		$tableJrnl->addField ( $this->buildPkField ( "jrnl_id" ) );
		$this->logger->trace ( "start clone fields from " . $table->getName() . " to " .  $tableJrnl->getName());
		foreach ( $table->getFields () as $field ) {
			$jrnl_field = "";
			$this->logger->trace ("clone " . $field->getName());
			if ($field->getPk ()) {
				$this->logger->trace ('is PK');
				$jrnl_field = $this->convertPkFieldToSimple ( $field ) ;
			} else {
				$jrnl_field = $field->cloneField ();
			}
			$this->logger->trace ( var_export($jrnl_field, true) );
			$tableJrnl->addField ( $jrnl_field);
		}
		$this->logger->trace ( "finish clone fields" );
		$tableJrnl->addField ( $this->buildInsertField () );
		return $tableJrnl;
	}
	private function fillFieldFromArray($fieldArray) : Field {
		$name = $fieldArray ['name']->__toString ();
		$title = $fieldArray->title->__toString ();
		$type = $fieldArray->type->__toString ();
		return $this->fillField ( $name, $title, $type );
	}
	private function fillField($name, $title, $type) : Field{
		$field = new Field ( $name, $title, $type );
		return $field;
	}
	private function buildCreateDb(DbSchema $db) : string {
		$template = "CREATE DATABASE `%s` CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		return sprintf ( $template, $db->getName () );
	}
	private function buildCreateTableDdl(Table $table) : string {
		// return $this->buildCreateTableHelp ( $table );
		$this->logger->debug ( "START" );
		if (0)
			$table = new Table ();
		$ddl = "";
				
		// $ddl .= sprintf ( "CREATE TABLE %s (\n %s INTEGER(11) AUTO_INCREMENT,\n", $table_name, $id_column );
		$ddl .= sprintf ( "CREATE TABLE %s (\n", $table->getName() );
		
		if ($is_journal) {
			$ddl .= "id INTEGER(11) NOT NULL, ";
		}
		// $ddl .= "patient_id INTEGER(11) NOT NULL COMMENT 'Пациент',\n";
		// $ddl .= "visit_id INTEGER(11) NOT NULL COMMENT 'Визит',\n";
		$id_column = '';
		foreach ( $table->getFields () as $field ) {
			if (0) {
				$field = new Field ( '', '', '' );
			}
			if ($field->getPk ()) {
				$id_column = $field->getName ();
				$ddl .= sprintf ( "%s INTEGER(11) AUTO_INCREMENT COMMENT '%s',\n", $field->getName (), $field->getComment() );
				break;
			}
		}
		foreach ( $table->getFields () as $field ) {
			if (0) {
				$field = new Field ( '', '', '' );
			}
			if ($field->getPk ()) {
				continue;
			}
			$this->logger->debug ( "field=" . var_export ( $field, true ) );
			/*
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
					throw new Exception ( "type of field is unknown: " . $field->getType () );
			}
			
			if (! $field->getNull ()) {
				$ddl .= " NOT NULL ";
			}
			if ($field->getDefault () != null) {
				if ($field->getType () == 'timestamp') {
					$ddl .= " DEFAULT " . $field->getDefault () . " ";
				} else {
					$ddl .= " DEFAULT '" . $field->getDefault () . "' ";
				}
			}
			$ddl .= " COMMENT '" . $field->getComment () . "',\n";
			*/
			$ddl .= $field->getDdl() . ",\n";
			}
		
		/*
		 * $ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
		 * $ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
		 * $ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
		 * $ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 * $ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 */
		$ddl .= "PRIMARY KEY ($id_column)";
		foreach ( $table->getFields () as $field ) {
			if ($field->getType () == "list")
				$ddl .= ",\nKEY(" . $field->getName () . ")";
		}
		
		// UNIQUE KEY `patient_visit_uniq` (`patient_id`, `visit_id`),
		if (!$table instanceof TableJrnl){
			if (!$table->isPatient()){
				$ddl .= ",\n UNIQUE KEY `patient_visit_uniq` (patient_id, visit_id)";
			}
		}
		if(!$table->isPatient()) {
			$ddl .=  ",\n KEY `patient_id` (patient_id)";
			$ddl .=  ",\n KEY `visit_id` (visit_id)";
		}
		
		$ddl .= "\n) ENGINE=InnoDB\n";
		$ddl .= "AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		$this->logger->debug ( "FINISH, returned: " . $ddl );
		return $ddl;
	}

	public function buildPkField(string $name) : Field {
		$pkField = new Field ( $name, 'PK', "int" );
		$pkField->setPk ( true );
		$pkField->setNull ( false );
		return $pkField;
	}

	private function convertPkFieldToSimple(Field $pkField) : Field {
		if (0) {
			$pkField = new Field ( null, null, null );
		}
		$field = $pkField->cloneField ();
		$field->setComment( 'PK of src table' );
		$field->setPk ( false );
		$field->setNull ( false );
		//$this->logger->trace ( var_export($field, true) );
		return $field;
	}
	public function buildServiceFields() : array {
		/*
		 * $ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
		 * $ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
		 * $ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
		 * $ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 * $ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		 */
		$serviceFields = array ();
		$serviceFields [] = $this->buildServiceField ( "checked", 'Проверено монитором', 'boolean', '0' );
		$serviceFields [] = $this->buildServiceField ( "row_stat", 'Статус строки (акт 1, удал 0)', 'int', '1' );
		$serviceFields [] = $this->buildServiceField ( "user_insert", 'Пользователь, создавший', 'varchar', 'no_user' );
		$serviceFields [] = $this->buildServiceField ( "user_update", 'Пользователь, обновивший', 'varchar', 'no_user' );
		$serviceFields [] = $this->buildServiceField ( "insert_date", 'Дата добавления записи', 'timestamp', 'CURRENT_TIMESTAMP' );
		$serviceFields [] = $this->buildServiceField ( "update_date", 'Дата обновления записи', 'timestamp', 'CURRENT_TIMESTAMP' );
		return $serviceFields;
	}
	private function buildServiceField(string $name, string $comment, string $type, string $default = null) : Field {
		$field = new Field ( $name, $comment, $type );
		$field->setService ( true );
		$field->setNull ( false );
		$field->setDefault ( $default );
		return $field;
	}
	public function buildInsertField() : Field {
		/*
		 * $ddl .= "insert_ind INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Индикатор вставки',\n ";
		 */
		$field = new Field ( "insert_ind", 'Индикатор вставки', 'boolean' );
		$field->setService ( true );
		$field->setNull ( false );
		$field->setDefault ( '0' );
		return $field;
	}
	private function buildTriggers(Table $table) : void {
		$this->logger->debug ( "START" );
		$this->buildInsertTrigger($table);
		$this->buildUpdateTrigger($table);
		$this->logger->debug ( "FINISH" );
	}
	
	private function buildInsertTrigger(Table $table) : void {
		$this->logger->debug ( "START" );
		$trigger = $this->buildTrigger($table, "insert");
		$table->setTriggerInsert($trigger);
		$this->logger->debug ( "FINISH" );
	}
	
	private function buildUpdateTrigger(Table $table) : void {
		$this->logger->debug ( "START" );
		$trigger = $this->buildTrigger($table, "update");
		$table->setTriggerUpdate($trigger);
		$this->logger->debug ( "FINISH" );
	}
	
	private function buildTrigger(Table $table, $typeTrigger='insert') : Trigger {
		$this->logger->debug ( "START" );
		$trigger = new Trigger();
		$trigger_name = "after_insert";
		$insert_ind_val = 1;
		$operation = "INSERT";
		if($typeTrigger == "update"){
			$trigger_name = "after_update";
			$insert_ind_val = 0;
			$operation = "UPDATE";
		}
		$trigger->setName($table->getName() . $trigger_name);
		
		$ddl = sprintf("CREATE TRIGGER %s AFTER " . $operation . " ON %s", $trigger->getName(), $table->getName());
		$ddl .= sprintf("\n FOR EACH ROW BEGIN\n insert into %s (", $table->getTableJrnl()->getName());
		
		foreach ($table->getFields() as $field){
			if (0) $field = new Field(null, null, null);
			$ddl .= " " . $field->getName() . ",";
		}
		
		$ddl .= " insert_ind) VALUES (" ;
		
		foreach ($table->getFields() as $field){
			if (0) $field = new Field(null, null, null);
			$ddl .= " new." . $field->getName() . ",";
		}
		$ddl .= " " . $insert_ind_val . ");" ;
		$ddl .= "\nEND\n";
		$trigger->setDdl($ddl);
		$this->logger->debug ( "trigger ddl:\n" . $ddl );
		$this->logger->debug ( "FINISH return " . $trigger->getName() );
		return $trigger;
	}
	
}

?>