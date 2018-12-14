$(document).ready(function() {

	$("#updateTableButton").click(function(event){
		try {
		  	var jsonDiv = $( "#jsonToPost" );
			var arrActions = [];
			if (jsonDiv.val()) {
				arrActions = jQuery.parseJSON( jsonDiv.val() );
			}

			var errors = isActioArrayValid(arrActions);
	        console.log("arrActions="+arrActions);
			console.log("errors="+errors);
			if(errors.length) {
				$( "#err_mess" ).html( errors );
				event.preventDefault();
			}
		} catch(err) {
		  //document.getElementById("err_mess").innerHTML = err.message;
		  event.preventDefault();
		  $( "#err_mess" ).html( err.message );
		}
	});

	$( ".remove_select" ).change(function() {
		var suffix = $(this).attr('id').replace("remove_select_", "");
		console.log( $(this).attr('id'));
	});

	$( ".check_action" ).change(function() {
		//action_enable_
		var suffix = $(this).attr('id').replace("action_enable_", "");
		console.log( $(this).attr('id'));
		console.log( "suffix=" + suffix );
		console.log( $(this).prop("checked") );

		var toAdd = $(this).prop("checked");

		var jsonDiv = $( "#jsonToPost" );
		var arrActions = [];
		if (jsonDiv.val()) {
			arrActions = jQuery.parseJSON( jsonDiv.val() );
		}
		console.log(arrActions);
		if (toAdd) {
			var action = {}
			action ["id"] = suffix;
			action ["field"] = $( "#field_" + suffix).text();
			action ["initial_type"] = $( "#initial_type_" + suffix).text();
			action ["to"] = action ["field"];
			
			if(action ["initial_type"] == 'remove') {
				console.log("selected text="+$( "#remove_change_" + suffix).find(":selected").text());
				console.log("selected val="+$( "#remove_change_" + suffix).find(":selected").val());
				var removeType = $( "#remove_change_" + suffix).find(":selected").val();
				action ["type"] = removeType;
				if(removeType == "change") {
					$( "#select_remove_change_" + suffix).show();
					action ["to"] = $( "#select_remove_change_" + suffix).find(":selected").val();
				} else {
					$( "#select_remove_change_" + suffix).hide();

				}
				
			} else {
				action ["type"] = $( "#type_" + suffix).text().trim();
			}
			
			arrActions.push(action);
		} else {
			arrActions = $.grep(arrActions, function(e) { return e.id!=suffix });
		}
		jsonDiv.val( JSON.stringify(arrActions) );
	});



	
});

