<!DOCTYPE html>
<html>
<head>
	<title>Table page - {$table->getName()}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>
<body>
<div class="container">
	<h2>Table {$table->getName()}</h2>
	<p/>
{if !$validationResult->passed}
   <div>
    <h3>Actions</h3>
   	<table class="table table-bordered table-sm">
   			<tr><th>Fileld name</th><th>Action type</th><th>After field</th><th>Comment</th><th>Skip</th></tr>
   		{foreach from=$tableChangeAdviser->getActionsAdd() item=action}
   			<tr class="alert alert-success">
   				<td>{$action->field->getName()}</td>
   				<td>{$action->type}</td>
   				<td>{$action->after->getName()}</td>
   				<td>&nbsp;</td>
   				<td><input type="checkbox" name="{$action->type}_{$action->field->getName()}"/></td>
   			</tr>
   		{/foreach}
   		{foreach from=$tableChangeAdviser->getActionsRemove() item=action}
   			<tr class="alert alert-danger">
   				<td>{$action->field->getName()}</td>
   				<td>
   					<select class="remove_select" id="remove_change_{$action->field->getName()}">
   					    <option value="remove" selected="">remove</option>
   					    <option value="change">change to</option>
   				    </select>
   				    <select id="select_remove_change_{$action->field->getName()}" style="display:none">
   				    	{foreach from=$tableChangeAdviser->getActionsAdd() item=action}
   					    <option value="{$action->field->getName()}">{$action->field->getName()}</option>
   					    {/foreach}
   				    </select>
   				</td>
   				<td>{$action->after}</td>
   				<td>&nbsp;</td>
   				<td><input type="checkbox" name="{$action->type}_{$action->field->getName()}"/></td>
   			</tr>
   		{/foreach}
   		{foreach from=$tableChangeAdviser->getActionsChange() item=action}
   			<tr class="alert alert-warning">
   				<td>{$action->field->getName()}</td>
   				<td>{$action->type}</td>
   				<td>{$action->after}</td>
   				<td>{$action->comment}</td>
   				<td><input type="checkbox" name="{$action->type}_{$action->field->getName()}"/></td>
   			</tr>
   		{/foreach}

   	</table>
   </div>
   {/if}

   <div>
	   <h3>Tables info</h3>
		<table class="">
			<tr><th>Table in XML</th><th>&nbsp;</th><th>Table in DB</th></tr>
			<tr><td>
				<table class="table table-bordered table-sm">
					<tr>
						<th>N</th>
						<th>Name</th>
						<th>Type</th>
						<th>Comment</th>
						<th>Is null</th>
						<th>Is service</th>
					</tr>
					{foreach from=$table->getFields() item=field name=smartyloop}
					{assign var="counter" value=$smarty.foreach.smartyloop.iteration}
					<tr>
						<td>{$counter}</td>
						<td>{if $field->getPk()}<span class='text text-success'>{$field->getName()} (PK)</span>
							{else}{$field->getName()}{/if}</td>
						<td>{$field->getType()}</td>
						<td>{$field->getComment()}</td>
						<td>{$field->getNull()}</td>
						<td>{$field->getService()}</td>
					</tr>
						
					{/foreach}
			    </table>
	    </td>
	    <td scope="col"><=></td>
	    <td>
	    	<table class="table table-bordered table-sm">
					<tr>
						<th>N</th>
						<th>Name</th>
						<th>Type</th>
						<th>Comment</th>
						<th>Is Null</th>
					</tr>
	    	{foreach from=$tableMetaFromDb->columns item=field name=smartyloop2}
	    	{assign var="counter" value=$smarty.foreach.smartyloop2.iteration}
	    	<tr>
						<td>{$counter}</td>
						<td>{$field->column_name}</td>
						<td>{$field->data_type}</td>
						<td>{$field->column_comment}</td>
						<td>{$field->is_nullable}</td>
			</tr>
	    	{/foreach}
	    	</table>
	    </td></tr>
	    <table>
    </div>
</div>
<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="vendor/components/jquery/jquery.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/site/docs/4.1/assets/js/vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <script>
	$(document).ready(function(){
	    //$("p").click(function(){
	    //    $(this).hide();
	   // });
	   
	});

	$(document).ready(function(){
	    $("button").click(function(){
	        $("#test").hide();
	    });
	    $( ".remove_select" ).change(function() {
           console.log( $(this).attr('id'));
       });
	});
	
</script>
</body>
</html>