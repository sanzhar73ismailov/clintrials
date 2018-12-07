QUnit.skip( "add test", function( assert ) {
  var result = add(21,3);
  assert.equal( result, "24", "add(21,3) should be 24." );
});

/*
[
{"id":"0","field":"sex_id","initial_type":"add","type":"add","after":"instr_mrt_descr"},
{"id":"1","field":"field11","initial_type":"remove","type":"remove","after":""},
{"id":"2","field":"erythrocytes_date","initial_type":"change","type":"change","after":""}
]
*/

QUnit.test( "Json actions validate", function( assert ) {
  var actionsArray = [];
  //var action = ;
  actionsArray.push({id:"0", field:"f1", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"0", field:"f2", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"0", field:"f3", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"0", field:"f4", initial_type:"add", type:"add", after:"instr_mrt_descr"});
  actionsArray.push({id:"0", field:"f2", initial_type:"add", type:"add", after:"instr_mrt_descr"});

  assert.ok(Array.isArray(actionsArray), "actionsArray is array.");
  assert.equal( 5, actionsArray.length, "actionsArray.length should be 5." );

  
  assert.ok("0" == isActioArrayValid([]).length, "isActioArrayValid([]) return empty array.");

  var expectedErrors = ["Field f2 used more than once"];
  var result = isActioArrayValid(actionsArray);

  assert.deepEqual( result, expectedErrors, "result == [\"Field f2 used more than once\"]");
  //assert.equal(expectedErrors[0], result[0], "result == [\"Field f2 used more than once\"]");


  
});
