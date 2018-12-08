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
	<div id="err_mess" class="text-danger"></div>
	<p/>
	
{if !$validationResult->passed}
   <div>
    <h3>Actions</h3>

    <table class="table table-bordered table-sm">
   			<tr><th>Fileld name</th><th>Initial type</th><th>Action type</th><th>After field</th><th>Comment</th><th>Enable</th></tr>
   		{foreach from=$tableChangeAdviser->getAllActions() item=action key=k}
   			<tr class="">
   				<td id="field_{$k}">{$action->field->getName()}</td>
   				<td  id="initial_type_{$k}">{$action->type}</td>
   				<td  id="type_{$k}">
   				{if $action->type=="remove"}
   					<select class="remove_select" id="remove_change_{$k}">
   					    <option value="remove" selected="">remove</option>
   					    <option value="change">change to</option>
   				    </select>
   				    <select id="select_remove_change_{$k}" style="display:">
   				    	{foreach from=$tableChangeAdviser->getActionsAdd() item=action}
   					    <option value="{$action->field->getName()}">{$action->field->getName()}</option>
   					    {/foreach}
   				    </select>
   				{else}
   					{$action->type}
   				{/if}
   			    </td>
   				
   				<td id="after_{$k}">{if $action->after}{$action->after->getName()}{/if}</td>
   				<td>{$action->comment}</td>
   				<td><input type="checkbox" class="check_action"  id="action_enable_{$k}"  name="{$action->type}_{$action->field->getName()}"/></td>
   			</tr>
   		{/foreach}
   	</table>


   	<form method="post">
   	<textArea rows="10" cols="100" name="jsonActions" id="jsonToPost"></textArea>
   	<br/><button id="updateTableButton" class="btn btn-primary">Update Db table</button>
   </form>
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

    <script src="js/script.js"></script>
    <script src="js/functions.js"></script>
</body>
</html>