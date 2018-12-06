$(document).ready(function() {

	$("#mybutton").click(function(){
		var jsonDiv = $( "#jsonToPost" );
		var arrActions = [];
		if (jsonDiv.text()) {
			arrActions = jQuery.parseJSON( jsonDiv.text() );
		}
		console.log(arrActions);
		var action = {}
		action ["type"] = "add";
		action ["field"] = "f1_" + new Date().getTime();
		action ["after"] = "a1";

		arrActions.push(action)
		jsonDiv.html( JSON.stringify(arrActions) );

	});

	$( ".remove_select" ).change(function() {
		console.log( $(this).attr('id'));
	});
	/*
 				<td id="field_{$k}">{$action->field->getName()}</td>
   				<td id="type_{$k}">{$action->type}</td>
   				<td id="after_{$k}">{if $action->after}{$action->after->getName()}{/if}</td>
	*/

	$( ".check_action" ).change(function() {
		//action_enable_
		var suffix = $(this).attr('id').replace("action_enable_", "");
		console.log( $(this).attr('id'));
		console.log( "suffix=" + suffix );
		console.log( $(this).prop("checked") );

		var toAdd = $(this).prop("checked");

		var jsonDiv = $( "#jsonToPost" );
		var arrActions = [];
		if (jsonDiv.text()) {
			arrActions = jQuery.parseJSON( jsonDiv.text() );
		}
		console.log(arrActions);
		if (toAdd) {
			var action = {}
			action ["id"] = suffix;
			action ["field"] = $( "#field_" + suffix).text();
			action ["initial_type"] = $( "#initial_type_" + suffix).text();
			
			if(action ["initial_type"] == 'remove') {
				console.log("selected text="+$( "#remove_change_" + suffix).find(":selected").text());
				console.log("selected val="+$( "#remove_change_" + suffix).find(":selected").val());
				action ["type"] = $( "#remove_change_" + suffix).find(":selected").val();
				action ["to"] = $( "#select_remove_change_" + suffix).find(":selected").val();
			} else {
				action ["type"] = $( "#type_" + suffix).text().trim();
			}
			
			action ["after"] = $( "#after_" + suffix).text();
			arrActions.push(action);
		} else {
			arrActions = $.grep(arrActions, function(e) { return e.id!=suffix });
		}
		jsonDiv.html( JSON.stringify(arrActions) );
	});



	try {

	} catch(err) {
	//document.getElementById("err_mess").innerHTML = err.message;
	$( "#err_mess" ).html( err.message );
	}
});