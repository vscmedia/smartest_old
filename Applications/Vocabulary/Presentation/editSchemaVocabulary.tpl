<h3><a href="{$domain}models">Data Manager</a> > <a href="{$domain}modeltemplates/getItemSchemas">Templates</a>  >  <a href="{$domain}modeltemplates/getItemSchemas">RSS</a> > {$vocabulary.vocabulary_name}</h3>

<form action="{$domain}{$section}/updateItemClassProperty" method="post">
<input type="hidden" name="vocabulary_id" value="{$vocabulary.vocabulary_name}" />
<table style="width:750px" border="0" cellspacing="2" cellpadding="1">
<!--
<tr><td style="width:100px" valign="top">Schema:</td>
<td><input type="text" value="{$vocabulary.vocabulary_prefix}" name="itemproperty_name" /></td></tr>
-->
<tr><td style="width:100px" valign="top">Element Name:</td>
<td><input type="text" value="{$vocabulary.vocabulary_name}" name="vocabulary_prefix" /></td></tr>
<tr><td style="width:100px" valign="top">Type:</td>
<td><select name="itemproperty_datatype">
<option {if $vocabulary.vocabulary_type == "STRING"} selected {/if} value="STRING">String (255 Chars max)</option>
<option {if $vocabulary.vocabulary_type == "TEXT"} selected {/if} value="TEXT">Full Text</option>
<option {if $vocabulary.vocabulary_type == "NUMERIC"} selected {/if} value="NUMERIC">Number</option>
<option {if $vocabulary.vocabulary_type == "BOOLEAN"} selected {/if} value="BOOLEAN">True/False (boolean)</option>
<option {if $vocabulary.vocabulary_type == "NODE"} selected {/if} value="NODE">Node</option>
</select></td></tr>
<tr><td style="width:100px" valign="top">Html Form:</td>
<td>
<select name="itemproperty_datatype">
<option {if $vocabulary.vocabulary_type == "NONE"} selected {/if} value="STRING">None</option>
<option {if $vocabulary.vocabulary_type == "SELECT"} selected {/if} value="STRING">Select Box</option>
<option {if $vocabulary.vocabulary_type == "STRING"} selected {/if} value="TEXT">String</option>
<option {if $vocabulary.vocabulary_type == "TEXT"} selected {/if} value="NUMERIC">Text Area</option>
<option {if $vocabulary.vocabulary_type == "RADIO"} selected {/if} value="BOOLEAN">Radio</option>
</select>
</td></tr>
<tr><td style="width:100px" valign="top">Default Value:</td>
<td><input type="text" value="{$default_value}" name="default_value" /></td></tr>
<tr><td style="width:100px" valign="top"></td>
<td><input type="checkbox" {if $vocabulary.schemadefinition_required == "TRUE"} checked {/if}  name="schemadefinition_required" value="TRUE" />Required</td></tr>
<tr><td style="width:100px" valign="top"></td>
<td><input type="checkbox" {if $vocabulary.schemadefinition_root == 1} checked {/if}  name="schemadefinition_root" value="1" />Root Node</td></tr>
<tr><td style="width:100px" valign="top"></td>
<td><input type="checkbox" {if $vocabulary.schemadefinition_setting == 1} checked {/if}  name="schemadefinition_setting" value="1" />Setting</td></tr>
<tr>
<td>
</td>

<td>
	<input type="submit" value="Save" />
	<input type="button" value="Remove" onclick="window.location='{$domain}{$section}/removeProperty?itemproperty_id={$itemproperty_id}';" />
	<input type="button" value="Cancel" onclick="window.location='{$domain}items';" />
</td>
</tr>
</table>
</form>

