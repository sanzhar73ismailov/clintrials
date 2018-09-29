<?php
define("HOME_HOST", 1);
define("HOSTER_HOST", 2);

$file_exists = file_exists("../../test_site.txt");
//print_r('$file_exists='.$file_exists);
if($file_exists){
	$hoster_var = HOME_HOST; // в домашних условиях
}else{
	$hoster_var = HOSTER_HOST; // расскоментировать если на боевом
}

if($hoster_var == HOME_HOST){
	define("HOST", "localhost");
	define("DB_USER", "root");
	//define("DB_PASS", "elsieltc");
	define("DB_PASS", "");
	define("DB_NAME", "ovarian");
}else{
	define("HOST", "localhost");
	define("DB_USER", "p-508_root");
	define("DB_PASS", "Qwerty73San+");
	define("DB_NAME", "p-5080_ovarian");
}

/* Database and Session prefixes */
define('DB_PREFIX', 'ovarian_'); ## Do not edit !
?>