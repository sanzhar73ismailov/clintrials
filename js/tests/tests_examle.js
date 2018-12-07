QUnit.skip( "hello test", function( assert ) {
  assert.ok( 1 == "1", "Passed!" );
  //assert.ok( 1 === "1", "Passed!" );
  //assert.ok( 3 === "1", "3 equal to 1!" );
});

QUnit.skip( "My First Test", function( assert ) {
            var value = "1";
            assert.equal( value, "1", "Value should be 1" );
});

QUnit.skip( "TestSquare", function( assert ) {
            var result = square(2);
            assert.equal( result, "4", "square(2) should be 4." );
});


QUnit.skip( "TestSuite", function( assert ) {
            //test data
            var str1 = "abc";
            var str2 = "abc";
            var str3 = null;
            var val1 = 5;
            var val2 = 6;
            var expectedArray = ["one", "two", "three"];
            var resultArray =  ["one", "two", "three"];

            //Check that two objects are equal
            assert.equal(str1, str2, "Strings passed are equal.");
			
            //Check that two objects are not equal
            assert.notEqual(str1,str3, "Strings passed are not equal.");

            //Check that a condition is true
            assert.ok(val1 < val2, val1 + " is less than " + val2);
			
            //Check that a condition is false
            assert.notOk(val1 > val2, val2 + " is not less than " + val1);

            //Check whether two arrays are equal to each other.
            assert.deepEqual(expectedArray, resultArray ,"Arrays passed are equal.");
			
            //Check whether two arrays are equal to each other.
            assert.notDeepEqual(expectedArray, ["one", "two"],
               "Arrays passed are not equal.");			
         });
        

QUnit.module( "Module 000A", {
            beforeEach: function( assert ) {
               assert.ok( true, "before test case <<< " );
            }, afterEach: function( assert ) {
               assert.ok( true, " >>> after test case" );
            }
});
QUnit.skip( "test case 000A- 1", function( assert ) {
	assert.ok( true, "Module 000A: in test case 1" );
});

QUnit.skip( "test case 000A - 2", function( assert ) {
	assert.ok( true, "Module 000A: in test case 2" );
});      
QUnit.module( "Module A", {
            beforeEach: function( assert ) {
               assert.ok( true, "before test case" );
            }, afterEach: function( assert ) {
               assert.ok( true, "after test case" );
            }
});
         
QUnit.skip( "test case 1", function( assert ) {
	assert.ok( true, "Module A: in test case 1" );
});

QUnit.skip( "test case 2", function( assert ) {
	assert.ok( true, "Module A: in test case 2" );
});
		 		 
QUnit.module( "Module B" );		

QUnit.skip( "test case 1", function( assert ) {
	assert.ok( true, "Module B: in test case 1" );
});

QUnit.skip( "test case 2", function( assert ) {
	assert.ok( true, "Module B: in test case 2" );
});	

QUnit.skip( "multiple call test()", function( assert ) {
            var done = assert.async( 3 );
            
            setTimeout(function() {
               assert.ok( true, "first callback." );
               done();
            }, 500 );

            setTimeout(function() {
               assert.ok( true, "second callback." );
               done();
            }, 500 );

            setTimeout(function() {
               assert.ok( true, "third callback." );
               done();
            }, 500 );
         });	

QUnit.skip( "multiple call test()", function( assert ) {
            assert.expect( 3 );
            var done = assert.async( 3 );
            
            setTimeout(function() {
               assert.ok( true, "first callback." );
               done();
            }, 500 );

            setTimeout(function() {
               assert.ok( true, "second callback." );
               done();
            }, 500 );

            setTimeout(function() {
               assert.ok( true, "third callback." );
               done();
            }, 500 );
         });	