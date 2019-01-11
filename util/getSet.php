<?php

?>
<h1>Getters-Setters Generator</h1>
<h3>Please enter list of variables and press Generate</h3>
<textarea rows="10" cols="45"  id="text"></textarea>
<button onclick="generate();">Generate</button>
<div id="output"></div>

<script>
	"use strict";
	function generate() {
		var textArea = document.getElementById("text");
		var vals = textArea.value.split('\n');
		vals = vals.map(function(value) {
		  return value.trim();
		});

		var output = "";

		vals.forEach(function(item, i, arr) {
		  output +="public function get" + item.substr(0, 1).toUpperCase() + item.substr(1, item.length);
		  output += "() {\n}";
		  output += "    return $this->" + item + "\n}";


		  //alert( i + ": " + item + " (массив:" + arr + ")" );
		});

		 console.log(output);



	}

</script>