<!DOCTYPE html>
<html>
<head>
	<title>Admin main page</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>
<body>
<div class="container">

   <h2>DB info</h2>
   <table class="table table-bordered">
    <tr><td>Name</td><td>{$reportDb->getDbSchema()->getName()}</td></tr>
    <tr><td>Number Xml Tables</td><td>{$reportDb->getNumberXmlTables()}</td></tr>
    <tr><td>Number Db Tables</td><td>{$reportDb->getNumberDbTables()}</td></tr>
    <tr><td>Validation</td><td>
    	{if $reportDb->getValidationResult()->passed}
    		<div class='text text-success'>OK</div>
    	{else}
	    	<div class='badge badge-secondary'>DB validation result errors:</div>
	    	{foreach from=$reportDb->getValidationResult()->errors item=error}
	    	<div class='alert alert-warning'>{$error}</div>
	    	{/foreach}

    	{/if}

    </td></tr>
  </table>
   <!--  public function getNumberXmlTables() : int {
	public function getNumberDbTables() : int 
    	}
getDbSchema
	{ -->

	<h2> Tables info </h2>

	<table class="table table-bordered">
		<tr>
			<th>Name</th>
			<th>Comment</th>
			<th>DDL</th>
			<th>Valid</th>
			<th>Valid Rusults</th>
		</tr>
	{function name=showOk label='' isOk='0'}
	    <div>{$label}={if $isOk}<span class='text text-success'>Yes</span>{else}<span class='text text-danger'>No</span>{/if}</div>
	{/function}	
	{foreach from=$reportDb->getReportTables() item=reportTable}
        <tr>
	    	<td>{$reportTable->getTable()->getName()} {if $reportTable->getTable()->isPatient()} <span class="text-primary">is Patient</span>{/if}</td>
	    	<td>{$reportTable->getTable()->getComment()}</td>
	    	<td><button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#{$reportTable->getTable()->getName()}_ddl">Show DDL</button>
               <div id="{$reportTable->getTable()->getName()}_ddl" class="collapse"><pre>{$reportTable->getTable()->getDdl()}</pre></div>
	    	</td>
	    	<td>{if $reportTable->getReportTableValid()}<div class="alert alert-success">Valid</div>
	    	    {else}<div class="alert alert-warning">Valid
                <a href="?editTable={$reportTable->getTable()->getName()}">edit</a>
                </div>
	    	{/if}</td>
	    	<td>
	    		{if $reportTable->getReportTableValid()==false}
	    		<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#{$reportTable->getTable()->getName()}_errors">Show error details</button>
	    		<div id="{$reportTable->getTable()->getName()}_errors" class="collapse">
		    		{showOk label='table exist' isOk=$reportTable->getTableExist()}
		    		{showOk label='table valid' isOk=$reportTable->getTableValid()}
		    		{showOk label='tableJrnl exist' isOk=$reportTable->getTableJrnlExist()}
		    		{showOk label='tableJrnl vaild' isOk=$reportTable->getTableJrnlVaild()}
		    		{showOk label='trigger insert exist' isOk=$reportTable->getTriggerInsertExist()}
		    		{showOk label='trigger insert valid' isOk=$reportTable->getTriggerInsertValid()}
		    		
		    		{showOk label='trigger update exist' isOk=$reportTable->getTriggerUpdateExist()}
		    		{showOk label='trigger update valid' isOk=$reportTable->getTriggerUpdateValid()}
		    		{if $reportTable->getTableValidationResult()->passed==false}
		    		   <div class='badge badge-secondary'>Table validation result errors:</div>
		    		   {foreach from=$reportTable->getTableValidationResult()->errors item=error}
		    		   <div class='alert alert-warning'>{$error}</div>
		    		   {/foreach}
		    		{/if}
		    		{if $reportTable->getTableJrnlValidationResult()->passed==false}
		    		   <div class='badge badge-secondary'>Journal table validation result errors:</div>
		    		   {foreach from=$reportTable->getTableJrnlValidationResult()->errors item=error}
		    		   <div class='alert alert-warning'>{$error}</div>
		    		   {/foreach}
		    		{/if}
	    		{/if}
	    	    </div>
	    	</td>
	    	<td></td>
	    </tr>
	{/foreach}
	</table>
</div>
<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="vendor/components/jquery/jquery.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/site/docs/4.1/assets/js/vendor/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
