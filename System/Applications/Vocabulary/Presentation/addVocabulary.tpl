<h3><a href="{$domain}models">Data Manager</a> > <a href="{$domain}models/getItemSchemas">Model Templates</a> > Add Vocabulary</h3>

<table style="width:750px" border="0" cellspacing="2" cellpadding="1">
	{if $content.vocabulary.vocabulary_name }
	<tr>
		<td style="width:100px" valign="top">Parent:</td>
		<td><b>{$content.vocabulary.vocabulary_name}</b></td>
	</tr>
	{/if}
	
	<!--
	<form name="searchform" action="{$domain}{$section}/findVocabulary" method="post"  onsubmit="return liveSearchSubmit(); document.getElementById('vocabulary_name').value = document.getElementById('livesearch').value; ">
		<tr>
			<td style="width:100px" valign="top">Name:</td>
			<td><input type="text" id="livesearch" name="q" onkeypress="liveSearchStart();"/>
			<div id="LSResult" style="position:relative;display: none;">
				<div id="LSShadow" />
			</div>
	</form>
	-->
	
	<form name="addVocabularyToSchemaAction" action="{$domain}{$section}/addVocabularyToSchemaAction" method="post">
		<input type="hidden" name="vocabulary_id" value="{$content.vocabulary.vocabulary_id}" />
		<input type="hidden" name="schemadefinition_schema_id" value="{$content.vocabulary.schemadefinition_schema_id}" />
		<input type="hidden" name="schemadefinition_parent_id" value="{$content.vocabulary.schemadefinition_vocabulary_id}" />
		<input type="hidden" name="schemadefinition_level" value="{$content.vocabulary.schemadefinition_level}" />
		<input type="hidden" name="parent" value="{$content.vocabulary.schemadefinition_id}">	
		<input type="hidden" name="vocabulary_name" id="vocabulary_name" value="">
	</td></tr>


	<tr>
	<td style="width:100px" valign="top">Name:</td>
	<td>
	<select name="vocabulary_id">
	{foreach from=$content.vocabularies key=key item=vocabulary}
		<option value="{$vocabulary.vocabulary_id}">{$vocabulary.vocabulary_name}</option>
	{/foreach}
	</select>	
	<a href="{$domain}{$section}/getItemVocabulary">Manage Vocabulary</a>

	</td>
	</tr>	

	<!--
	<tr><td style="width:100px" valign="top">Default Value:</td>
	<td><input type="text" value="{$content.default_value}" name="default_value" /></td></tr>
	
	<tr><td style="width:100px" valign="top">Required:</td>
	<td><input type="checkbox" {if $content.itemproperty_required == "TRUE"} checked {/if}  name="itemproperty_required" value="TRUE" />Check if required</td></tr>
	<tr><td style="width:100px" valign="top">Repeats:</td>
	<td><input type="checkbox" {if $content.itemproperty_required == "TRUE"} checked {/if}  name="itemproperty_required" value="TRUE" />Is a value that repeats</td></tr>
	-->
	<tr>
	<td>
	</td>
	
	<td>
		<input type="submit" value="Add" />
		<input type="button" value="Cancel" onclick="window.location='{$domain}{$section}';" />
	</td>
	</tr>
</table>
</form>	



