<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}/"> Templates</a>  &gt; Create Schema</h3>

<a name="top"></a>
<div class="text" style="margin-bottom:10px"></div>

<form name="createSchemaForm" action="{$domain}{$section}/createSchemaAction" method="POST"> 
<table>

	<tr>
		<td>
		Base on
		</td><td>
		
		<select name="schema_parent_id">
			<option value="0">select</option>
			{foreach from=$content.schemas item=item}
			<option value="{$item.schema_id}">{$item.schema_name}</option>
			{/foreach}
		</select> *optional*
		</td>
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
		Schema Name Space
		</td>
		<td>
		<input type="text" name="schema_name_space">
		</td>	
	</tr>
		<tr>
		<td>
		Schema Description
		</td>
		<td>
		<textarea name="schema_des"></textarea>
		</td>	
	</tr>
		
		<tr>
		<td>
		Schema Encoding
		</td>
		<td>
		<input type="text" name="schema_encode">
		</td>	
	</tr>
		<tr>
		<td>
		Schema Root Tag
		</td>
		<td><input type="text" name="schema_root_tag">	
		</td>	
	</tr>
		<tr>
		<td>
		Schema Default Tag
		</td>
		<td><input type="text" name="schema_default_tag">	
		</td>	
	</tr>
<tr>
		<td>
		Schema Lang
		</td>
		<td><input type="text" name="schema_lang">
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

</div>