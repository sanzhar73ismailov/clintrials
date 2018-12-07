function add (x1, x2){
	return x1 + x2;
}

function isActioArrayValid(actionArray) {
   $errors = [];
   if(actionArray.length == 0) {
   	return $errors;
   }
   let arrayFieldNames = actionArray.map(a => a.field);
   console.log(arrayFieldNames);

   //checking - every field should be only one time 
   alreadyChecked = [];
   for (var i = 0; i < arrayFieldNames.length; i++) {
   	  var fieldName = arrayFieldNames[i];
   	  if(alreadyChecked.indexOf(fieldName) == -1) {
   	     if(arrayFieldNames.indexOf(fieldName) !== arrayFieldNames.lastIndexOf(fieldName)){
   	  		$errors.push("Field " + fieldName + " used more than once");
   	  	    alreadyChecked.push(fieldName);
   	  	}
   	  }
   }

   //$errors.push("is not implemented");
   return $errors;
}