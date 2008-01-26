<h3><a href="{$domain}{$section}">Data Manager</a> >  <a href="{$domain}{$section}/getItemClassMembers?class_id={$itemClass.itemclass_id}">{$itemClass.itemclass_plural_name}</a> &gt; Import Data<br>
Please Assign the Fields to the Item Properties
<form action="{$domain}{$section}/insertImportData" method="post"   >
<input type="hidden" name="file_name" value="{$content.file}">
<input type="hidden" name="class_id" value="{$itemClass.itemclass_id}">
<input type="hidden" name="check_on_off" value="{$content.check}">
<table  border="0" cellspacing="2" cellpadding="1">
<tr>
	<td><b>Item Name</b><select name="item_name">
		{foreach from=$properties_csv key=key item=property_csv}
		<option value="{$key}">{if $content.check}{$property_csv}{else}{$key}{/if}</option>
		{/foreach}	 
	    </select>
	</td>
</tr>
<tr>
	<td >
	{if is_array($content.properties)}
	<table border="1">
	<tr>
		<td><b>ItemPropertyName</b></td>
		<td><b>DataType</b></td>
		<td><b>Value</b></td>
	</tr>
	{foreach from=$properties  item=property_name}
	<tr>
		<td>{$property_name.itemproperty_name}</td>
		<td>{$property_name.itemproperty_datatype_name}</td>
		<td><select name="{$property_name.itemproperty_name}">
		<option  value="blank">leave blank</option>
		{foreach from=$properties_csv key=key item=property_csv}
		<option value="{$key}">{if $content.check}{$property_csv}{else}{$key}{/if}</option>
		{/foreach}
		</select>
		</td>
	</tr>
	{/foreach}
	</table>
	{/if}
	</td>
</tr>
<tr>
	<td align="right"><input type="submit" value="Submit" />&nbsp;&nbsp;<input type="button" onclick="#" value="Cancel" /></td>
</tr>
</table>
</form>

