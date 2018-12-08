/*
[
{"id":"0","field":"sex_id","initial_type":"add","type":"add","after":"instr_mrt_descr"},
{"id":"1","field":"field11","initial_type":"remove","type":"remove","after":""},
{"id":"2","field":"erythrocytes_date","initial_type":"change","type":"change","after":""}
]
 if(Array.isArray(actionArray)){
       $errors.push("actionArray is not array");
       return $errors;
   }
   if(!actionArray.length) {
      $errors.push("actionArray is empty");
      return $errors;
   }
*/

QUnit.test( "Json actions validate when field occurs more than once", function( assert ) {
  var expectedErrors = ["actionArray is not array"];
  var result = isActioArrayValid("text");
  assert.deepEqual(result, expectedErrors, "Parameter shoud be array");

  var actionsArray = [];
  expectedErrors = ["actionArray is empty"];
  result = isActioArrayValid([]);
  assert.deepEqual(result, expectedErrors, "Parameter shoud not be empty array");
  //var action = ;
  actionsArray.push({id:"0", field:"f0", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"1", field:"f1", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"2", field:"f2", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"3", field:"f3", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"4", field:"f4", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"5", field:"f2", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"0", field:"f0", initial_type:"add", type:"add", after:"instr_mrt_descr"});

  assert.ok(Array.isArray(actionsArray), "actionsArray is array.");
  assert.equal( 7, actionsArray.length, "actionsArray.length should be 5." );

  


  expectedErrors = ["Field f0 used more than once", "Field f2 used more than once"];
  result = isActioArrayValid(actionsArray);

  assert.deepEqual( result, expectedErrors, "result == [\"Field f0 used more than once\", \"Field f2 used more than once\"]");
});

QUnit.test( "Json actions validate when field occurs more than once", function( assert ) {
  var actionsArray = [];
  //var action = ;
  actionsArray.push({id:"0", field:"f0", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"1", field:"f1", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"2", field:"f2", initial_type:"remove", type:"change", to: "f0", after:"instr_mrt_descr"});
  actionsArray.push({id:"3", field:"f3", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"4", field:"f4", initial_type:"add", type:"add", after:"instr_mrt_descr"});

  var expectedErrors = ["Field f0 used as change <to> and field name"];
  var result = isActioArrayValid(actionsArray);

  assert.deepEqual( result, expectedErrors, "result == [\"Field f0 used as change <to> and field name\"]");
});
