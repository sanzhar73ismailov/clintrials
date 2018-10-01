<?php
//require_once 'configs/config_dev.php';

try {
	$conn = new PDO ( "mysql:host=" . HOST, DB_USER, DB_PASS );
	// set the PDO error mode to exception
	$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch ( PDOException $e ) {
	echo "error Exception: " . $e->getMessage ();
}

?>