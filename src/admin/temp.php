<?php
//echo date("d/m/Y") . "<br>";
//namespace clintrials\admin;


//echo ('<br/>extension_loaded (\'fileinfo\')=' . extension_loaded ('fileinfo'));

//echo ('<br/>extension_loaded (\'gd\')=' . extension_loaded ('gd'));

class A {
	public $field1 = "";
}

$a = new A();
var_dump($a);
var_dump($a->field1);

/*
$array = array('qqq', 'www', 'eee');
$comma_separated = implode(",", $array);

echo $comma_separated; // имя,почта,телефон

// Пустая строка при использовании пустого массива:
//var_dump(implode('hello', array())); // string(0) ""

$array1 = array("a" => "green", "yellow", "red");
$array2 = array("b" => "green", "yellow", "red");
$result = array_diff($array1, $array2);

print_r($result);






/*
$os = array("Mac", "NT", "Irix", "Linux");
if (in_array("Irix", $os)) {
	echo "find Irix";
}
if (in_array("Mac", $os)) {
	echo "find Mac";
}
*/

/*
$d = time();
for ($i; $i < 2000; $i++){
	$strDate = date("d/m/Y", $d);
	echo "&nbsp;D map.SetAt(\"" . md5($strDate) . "\",\"" . $strDate. "\")<br>";
	$d = $d + (1 * 24 * 60 * 60);
}
*/
