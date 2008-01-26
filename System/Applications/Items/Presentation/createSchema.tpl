<h3><a href="{$domain}{$section}">Data Manager</a> > Create Schema</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px"></div>

<form name="createSchemaForm" action="{$domain}{$section}/createSchemaAction" method="POST">

 <a href="{$domain}{$section}/getItemSchemas">Schema Manager</a>
<table>

	<tr>
		<td>
		Base on
		</td>
		<td>
		<select name="schema_parent_id">
			<option />
			{foreach from=$content.schemas item=item}
			<option value="{$item.schema_id}">{$item.schema_name}</option>
			{/foreach}
		</select> *optional*
		</tr>
	</tr>
	
	<tr>
		<td>
		Schema Name
		</td>
		<td>
		<input type="text" name="schema_name">
		</td>	
	</tr>
	
	
	<tr>
	<td>
	</td>
	<td>
	<input type="submit" value="Create" name="submit">
	</td>
	</tr>
</table>

</form>





