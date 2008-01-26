<h3><a href="{$domain}{$section}">Data Manager</a> > <a href="{$domain}{$section}/schemaDefinition">Templates</a>  > RSS > Editor</h3>

<form action="{$domain}{$section}/updateItemClassProperty" method="post">
<input type="hidden" name="vocabulary_id" value="{$content.vocabulary.vocabulary_name}" />
<table style="width:750px" border="0" cellspacing="2" cellpadding="1">
<tr><td style="width:100px" valign="top">Name:</td>
<td><input type="text" value="{$content.vocabulary.vocabulary_name}" name="itemproperty_name" /></td></tr>
<tr><td style="width:100px" valign="top">Type:</td>
<td>



<select name="itemproperty_datatype">
<option {if $content.vocabulary.vocabulary_type == "STRING"} selected {/if} value="STRING">String (255 Chars max)</option>
<option {if $content.vocabulary.vocabulary_type == "TEXT"} selected {/if} value="TEXT">Full Text</option>
<option {if $content.vocabulary.vocabulary_type == "NUMERIC"} selected {/if} value="NUMERIC">Number</option>
<option {if $content.vocabulary.vocabulary_type == "BOOLEAN"} selected {/if} value="BOOLEAN">True/False (boolean)</option>
<option {if $content.vocabulary.vocabulary_type == "NODE"} selected {/if} value="NODE">Node</option>
</select></td></tr>
<tr><td style="width:100px" valign="top">Default Value:</td>
<td><input type="text" value="{$content.default_value}" name="default_value" /></td></tr>
<tr><td style="width:100px" valign="top">Required:</td>
<td><input type="checkbox" {if $content.itemproperty_required == "TRUE"} checked {/if}  name="itemproperty_required" value="TRUE" />Check if required</td></tr>
<tr><td style="width:100px" valign="top">Property:</td>
<td><input type="checkbox" {if $content.itemproperty_required == "TRUE"} checked {/if}  name="itemproperty_required" value="TRUE" />Collection wide option</td></tr>
<tr>
<td>
</td>

<td>
	<input type="submit" value="Save" />
	<input type="button" value="Remove" onclick="window.location='{$domain}{$section}/removeProperty?itemproperty_id={$content.itemproperty_id}';" />
	<input type="button" value="Cancel" onclick="window.location='{$domain}items';" />
</td>
</tr>
</table>
</form>

