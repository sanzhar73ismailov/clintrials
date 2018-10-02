<?php
//echo date("d/m/Y") . "<br>";
$d = time();
for ($i; $i < 2000; $i++){
	$strDate = date("d/m/Y", $d);
	echo "&nbsp;D map.SetAt(\"" . md5($strDate) . "\",\"" . $strDate. "\")<br>";
	$d = $d + (1 * 24 * 60 * 60);
}
