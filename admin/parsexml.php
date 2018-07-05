<?php
libxml_use_internal_errors ( true );
class MetadataCreator {
	private $xmlObj;
	function __construct($xml_file) {
		$this->xmlObj = simplexml_load_file ( $xml_file ) or die ( "Error: Cannot create object" );
		if ($this->xmlObj === false) {
			echo "Failed loading XML: ";
			foreach ( libxml_get_errors () as $error ) {
				echo "<br>", $error->message;
			}
			exit ();
		}
	}
	public function buildDdl() {
		$ddl = "";
		foreach ( $this->xmlObj->data->investigations->children () as $investigation ) {
			// print_r ( $investigation );
			// echo "-------<br>";
			$ddl .= $this->buildCreateTableDdl ( $investigation ) . "\n";
			break;
		}
		return $ddl;
	}
	
	/*
	 * CREATE TABLE t1 (
	 * id INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'comment',
	 * f2 TINYINT(1) DEFAULT NULL COMMENT 'comment',
	 * PRIMARY KEY (id)
	 * )ENGINE=InnoDB
	 * AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';
	 */
	private function buildCreateTableDdl($investigation) {
		$ddl = "";
		$ddl .= sprintf ( "CREATE TABLE %s_%s (\nid INT AUTO_INCREMENT,\n", $this->xmlObj->prefix, $investigation ['name'] );
		foreach ( $investigation->fields->children () as $field ) {
			$ddl .= $field ['name'] . "";
			if ($field->type == "list")
				$ddl .= " INT";
				else
					$ddl .= " " . $field->type . "";
					
					if ($field->type == "float")
						$ddl .= "(11,2)";
						
						$ddl .= " COMMENT '" . $field->title . "',\n";
						// $ddl .= " is not null " . "\n";
		}
		/*
		 *   checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',
		 user VARCHAR(25) COLLATE utf8_general_ci DEFAULT NULL,
		 insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		 * */
		$ddl .= "checked INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',\n";
		$ddl .= "user_insert VARCHAR(25) COMMENT 'Пользователь, создавший',\n";
		$ddl .= "user_update VARCHAR(25) COMMENT 'Пользователь, обновивший',\n";
		$ddl .= "insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		$ddl .= "update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
		
		$ddl .= "PRIMARY KEY (id)";
		foreach ( $investigation->fields->children () as $field ) {
			if ($field->type == "list")
				$ddl .= ",\nKEY(" . $field ['name'] . ")";
		}
		$ddl .= "\n) ENGINE=InnoDB\n";
		$ddl .= "AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
		return $ddl;
	}
}
$metadataCreator = new MetadataCreator ( "../clintrials.xml" );
$str = $metadataCreator->buildDdl ();
echo "----<br>";
echo "<pre>$str</pre>";
echo "<br>----";

?>