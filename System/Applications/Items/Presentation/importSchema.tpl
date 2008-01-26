<h3><a href="{$domain}items">Chooser</a> -> <a href="{$domain}items/getItemSchemas">Schema Manager</a> -> (TYPE) -> Vocabulary Editor</h3>

<form action="{$domain}items/updateItemClassProperty" method="post">
<input type="hidden" name="itemproperty_itemclass_id" value="{$content.itemproperty_itemclass_id}" />
<input type="hidden" name="itemproperty_id" value="{$content.itemproperty_id}" />
  <table style="width:750px" border="0" cellspacing="2" cellpadding="1">
<tr><td style="width:150px" valign="top">Property Name:</td>
<td><input type="text" value="{$content.itemproperty_name}" name="itemproperty_name" /></td></tr>
<tr><td style="width:150px" valign="top">Property Type:</td>
<td>
<select name="itemproperty_datatype">
<option {if $content.itemproperty_datatype == "STRING"} selected {/if} value="STRING">String (255 Chars max)</option>
<option {if $content.itemproperty_datatype == "TEXT"} selected {/if} value="TEXT">Full Text</option>
<option {if $content.itemproperty_datatype == "NUMERIC"} selected {/if} value="NUMERIC">Number</option>
<option {if $content.itemproperty_datatype == "BOOLEAN"} selected {/if} value="BOOLEAN">True/False (boolean)</option>
<!--
<option {if $content.itemproperty_datatype == "DATE"} selected {/if} value="DATE">Date</option>
<option {if $content.itemproperty_datatype == "FILE"} selected {/if} value="FILE">File</option>
<option {if $content.itemproperty_datatype == "ITEM"} selected {/if} value="ITEM">Custom Item</option>
-->
</select></td></tr>
<tr><td style="width:150px" valign="top">Default Value (optional):</td>
<td><input type="text" value="{$content.default_value}" name="default_value" /></td></tr>
<tr><td style="width:150px" valign="top">Property Required:</td>
<td><input type="checkbox" {if $content.itemproperty_required == "TRUE"} checked {/if}  name="itemproperty_required" value="TRUE" />Check if required</td></tr>
<!--<tr><td style="width:150px" valign="top">Class Parent:</td>
<td><input type="text" name="itemclass_name" /></td></tr>-->
<tr><td style="width:150px" valign="top" colspan="2" align="right">
<input type="button" value="Cancel" onclick="window.location='{$domain}items';" />
<input type="button" value="Remove" onclick="window.location='{$domain}items/removeProperty?itemproperty_id={$content.itemproperty_id}';" />
<input type="submit" value="Save" /></td></tr>
</table>
</form>
