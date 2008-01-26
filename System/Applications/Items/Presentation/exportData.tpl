<h3><a href="{$domain}{$section}">Data Manager</a> >  <a href="{$domain}{$section}/getItemClassMembers?class_id={$itemClass.itemclass_id}">{$itemClass.itemclass_plural_name}</a> &gt; Export Data

<form action="{$domain}{$section}/exportDataXml" method="post" id="pageViewForm" >
<input type="hidden" name="class_id" value="{$itemClass.itemclass_id}">
<table  border="0" cellspacing="2" cellpadding="1">
	<tr>
      <td style="width:150px" valign="top">Model Template</td>
      <td>
      <select name="schema" id="schema" onchange="window.location='{$domain}{$section}/exportData?class_id={$itemClass.itemclass_id}&schema_id='+document.getElementById('schema').value"  >
        {foreach from=$content.schemas key=key item=item }
          <option value="{$item.schema_id}" {if $item.schema_id == $content.schema_id} selected{/if}>{$item.schema_name}</option>  
        {/foreach}
      </select>
      </td>		
	</tr>
	{if $content.schema_id}
	{foreach from=$itemClassProperties item=property}
	<tr>
	<td>{$property.itemproperty_name}</td>
	<td>
	<select name="{$property.itemproperty_id}">
	{foreach from=$schemsDefinition key=key item=definition}
	<option value="{$definition.vocabulary_id}">{$definition.vocabulary_name}</option>
	{/foreach}
	</select></td>
	</tr>
	{/foreach}

	{/if}
	<tr>
	<td colspan="2" align="right"><input type="submit" value="Ok"></td>
	<td>
	</tr>
</table>
</form>

