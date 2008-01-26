<div id="work-area">
<h3><a href="{$domain}datamanager">Data Manager</a> > <a href="{$domain}modeltemplates/">Templates</a>  >  <a href="{$domain}modeltemplates/schemaDefinition?schema_id={$content.schema_id}">{$content.schema_name}</a> > Add A Node</h3>
<div class="instruction">Please enter the details.</div>

<form action="{$domain}{$section}/insertSchemaVocabularyNode" method="post" id="pageViewForm">
<input type="hidden" name="parent_vocabulary_id" value="{$content.vocabulary_id}" />
<input type="hidden" name="schema_id" value="{$content.schema_id}" />
<input type="hidden" name="schema_definision_id" value="{$content.schemadefinition_id}" />
<table style="width:750px;margin-left:10px;" border="0" cellspacing="2" cellpadding="1">

<tr><td style="width:100px" valign="top">Element Name:</td>
<td><input type="text"  name="vocabulary_name"  /></td></tr>

<tr><td style="width:100px" valign="top">Type:</td>
<td>NODE</td></tr>

<tr><td style="width:100px" valign="top">Prefix:</td>
<td><input type="text"  name="prefix"  /></input></tr>

<tr>
<tr><td style="width:100px" valign="top">Default Value:</td>
<td>
	
	<input type="text" style="width:240px" name="default_value"  />
        </td></tr>
<tr><td style="width:100px" valign="top"></td>
<td><input type="checkbox"  name="schemadefinition_required" value="TRUE"  />Required</td></tr>

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
</div>