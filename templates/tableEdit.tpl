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
	<table class="">
		<tr><td>
			<table class="table table-bordered table-sm">
				<tr>
					<th>N</th>
					<th>Name</th>
					<th>Type</th>
					<th>Comment</th>
					<th>Is Null</th>
					<th>Is Seevice</th>
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
<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="vendor/components/jquery/jquery.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/site/docs/4.1/assets/js/vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>