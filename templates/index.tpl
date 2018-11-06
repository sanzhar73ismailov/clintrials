{* Smarty *}

<h2>
Hello {$name}, welcome to Smarty!

<table border="1">
	<tr>
		<td>Name</td>
		<td>Comment</td>
		<td>DDL</td>
		<td>Is Patient</td>
	</tr>
{foreach from=$db->getTables() item=table}
    <tr>
    	<td>{$table->getName()}</td>
    	<td>{$table->getComment()}</td>
    	<td><pre>{$table->getDdl()}</pre></td>
    	<td>{$table->isPatient()}</td>
    </tr>
{/foreach}
</table>
</h2>