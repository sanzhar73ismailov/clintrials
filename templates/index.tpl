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
	<h2> Hello {$name}, welcome to Smarty! </h2>

	<table class="table table-bordered">
		<tr>
			<th>Name</th>
			<th>Comment</th>
			<th>DDL</th>
			<th>Valid</th>
			<th>Valid Rusults</th>
		</tr>
	{foreach from=$reportDb->getReportTables() item=reportTable}
        <tr>
	    	<td>{$reportTable->getTable()->getName()} {if $reportTable->getTable()->isPatient()} <span class="text-primary">is Patient</span>{/if}</td>
	    	<td>{$reportTable->getTable()->getComment()}</td>
	    	<td><button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#{$reportTable->getTable()->getName()}_ddl">Show DDL</button>
               <div id="{$reportTable->getTable()->getName()}_ddl" class="collapse"><pre>{$reportTable->getTable()->getDdl()}</pre></div>
	    	</td>
	    	<td>{if $reportTable->getReportTableValid()}<div class="alert alert-success">Valid</div>
	    	    {else}<div class="alert alert-warning">Valid</div>{/if}</td>
	    	<td><pre>
				$tableExist = {$reportTable->gettableExist()};
				$tableValid={$reportTable->getTableValid()};
				$tableJrnlExist= {$reportTable->getTableJrnlExist()};
				$tableJrnlVaild= {$reportTable->getTableJrnlVaild()};
				$triggerInsertExist= {$reportTable->getTriggerInsertExist()};
				$triggerUpdateExist= {$reportTable->getTriggerUpdateExist()};
				$tableValidationResult= {$reportTable->getTableValidationResult()->errors};
				$tableJrnlValidationResult= {$reportTable->getTableJrnlValidationResult()->errors};
				$reportTableValid = {$reportTable->getReportTableValid()};
			    </pre>
	    	</td>    
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
