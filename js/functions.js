function add (x1, x2){
	return x1 + x2;
}

function isActioArrayValid(actionArray) {
   $errors = [];
   let arrayFieldNames = actionArray.map(a => a.field);
   console.log(arrayFieldNames);

   for (var i = 0; i < actionArray.length; i++) {
   	
   }

   $errors.push("is not implemented");
   return $errors;
}