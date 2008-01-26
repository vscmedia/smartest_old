<script language="javascript" type="text/javascript">
{literal}

function check(){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(editForm.paring_name.value==''){
		alert ('please enter the pairing name');
		editForm.paring_name.focus();
		return false;
	}else{
		return true;
	}
}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">Sets</a> &gt;{$set.set_name}  &gt; Export Data</h3>

<form action="{$domain}sets/updateExportData" method="post" id="pageViewForm" onsubmit="return check();">
<input type="hidden" name="pairing_id" value="{$content.pairing_id}">
<input type="hidden" name="class_id" value="{$set.set_itemclass_id}">
<input type="hidden" name="set_id" value="{$set.set_id}">
<input type="hidden" name="schema" value="{$content.schema_id}">
<table  border="0" cellspacing="2" cellpadding="1">
{if $content.msg==1}
<tr>
      <td  colspan="2"><font color="Red">
Sorry..This Pairing name already exists for this Set..Please try again!! </font></td>
</tr>
{/if}
	<tr>
      <td style="width:150px" valign="top">Pairing Name</td>
      <td>
      <input type="text" name="paring_name" id="p_name" value="{$content.name}"  >
      </td>		
	</tr>
{if $content.schema_id}
		{foreach from=$Properties item=property}
			{if $property.itemproperty_setting != 1}
			<tr>
			<td>{$property.itemproperty_name}</td>
			<td>
			<select name="{$property.itemproperty_id}">
			<OPTION value="">Select One</OPTION>
			{foreach from=$schemsDefinition key=key item=definition}
			{if $definition.schemadefinition_setting != 1}
			<option value="{$definition.vocabulary_id}" {if $definition.vocabulary_id == $property.pairingvocabulary} selected {/if} >{$definition.vocabulary_name}</option>
			{/if}
			{/foreach}
			</select></td>
			</tr>
			{/if}
		{/foreach}
	<tr>
	<td colspan="2"><b>Settings</b></td>
	<td>
	</tr>
	{if $content.Settings}
		{foreach from=$Settings item=setting}
			<tr>
			<td>{$setting.vocabulary_name}</td>
			<td><input type="text" name="{$setting.vocabulary_id}" value="{$setting.pairingvocabulary}">
			</td>
			</tr>
		{/foreach}
	{else}
		<tr>
	<td colspan="2">NO Settings found!!</td>
	<td>
	</tr>
	{/if}
	
	<tr>
	<td colspan="2" align="right"><input type="submit" value="Update" ></td>
	<td>
	</tr>
	{/if}
</table>
</form>

</div>