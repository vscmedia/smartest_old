<script language="javascript" type="text/javascript">
{literal}

var customVarName = false;

function checkPrepertyType(){
	switch(document.getElementById('datatype').value){

		case "NUMERIC":

			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-num').style.display = "block";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			break;
		case "STRING":
		
			document.getElementById('default-value-text').style.display = "block";
			document.getElementById('default-value-num').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";

			break;
		case "TEXT":
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-num').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "block";
			document.getElementById('default-value-bool').style.display = "none";

			break;
		case "BOOLEAN":
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-num').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('default-value-bool').style.display = "block";

			break;
		case "NODE":

			document.getElementById('default-value-text').style.display = "block";
			document.getElementById('default-value-num').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";

			break;
		default:

			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-num').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			break;
	}
}

function setVarName(){
	
	if(document.getElementById('itemproperty_varname').value.length < 1){customVarName = false}
	
	var propertyName = document.getElementById('itemproperty_name').value;
	
	if(!customVarName){
		document.getElementById('itemproperty_varname').value = propertyName.toVarName();
	}
}


function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(editForm){
{/literal}		editForm.action="/{$section}/"+pageAction; {literal}
		editForm.submit();
	}
}
{/literal}
</script>
<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> > <a href="{$domain}modeltemplates/">Templates</a>  >  <a href="{$domain}modeltemplates/schemaDefinition?schema_id={$vocabulary.schema_id}">{$vocabulary.schema_name}</a> > {$vocabulary.vocabulary_name}</h3>

<form action="{$domain}{$section}/updateSchemaVocabulary" method="post" id="pageViewForm">
<input type="hidden" name="vocabulary_id" value="{$vocabulary.vocabulary_id}" />
<input type="hidden" name="schema_id" value="{$vocabulary.schema_id}" />
<input type="hidden" name="schemadefinition_id" value="{$vocabulary.schemadefinition_id}" />
<table style="width:750px" border="0" cellspacing="2" cellpadding="1">
<!--
<tr><td style="width:100px" valign="top">Schema:</td>
<td><input type="text" value="{$vocabulary.vocabulary_prefix}" name="itemproperty_name" /></td></tr>
-->
<tr><td style="width:100px" valign="top">Element Name:</td>
<td><input type="text" value="{$vocabulary.vocabulary_name}" name="vocabulary_name" /></td></tr>
<tr><td style="width:100px" valign="top">Type:</td>
<td><select name="vocabulary_datatype"   onchange="checkPrepertyType()" id="datatype">
<option {if $vocabulary.vocabulary_type == "STRING"} selected {/if} value="STRING">String (255 Chars max)</option>
<option {if $vocabulary.vocabulary_type == "TEXT"} selected {/if} value="TEXT">Full Text</option>
<option {if $vocabulary.vocabulary_type == "NUMERIC"} selected {/if} value="NUMERIC">Number</option>
<option {if $vocabulary.vocabulary_type == "BOOLEAN"} selected {/if} value="BOOLEAN">True/False (boolean)</option>
<option {if $vocabulary.vocabulary_type == "NODE"} selected {/if} value="NODE">Node</option>
</select></td></tr>

<tr><td style="width:100px" valign="top">Prefix:</td>
<td><input type="text"  name="prefix"  value="{$vocabulary.vocabulary_prefix}" /></tr>

<tr>
<tr><td style="width:100px" valign="top">Default Value:</td>

<td>

<div id="default-value-text" {if $vocabulary.vocabulary_type == 'STRING' || $vocabulary.vocabulary_type == 'NODE' }  style="display:block" {else} style="display:none" {/if}>
	<input type="text" style="width:240px" name="default_value[STRING]" value="{$vocabulary.vocabulary_default_content}"  /></div>

 <div id="default-value-num" {if $vocabulary.vocabulary_type == 'NUMERIC'}  style="display:block" {else} style="display:none" {/if} ><input type="text" style="width:120px" name="default_value[NUMERIC]"  value="{$vocabulary.vocabulary_default_content}" /></div>
 <div id="default-value-longtext" {if $vocabulary.vocabulary_type == 'TEXT'}  style="display:block" {else} style="display:none" {/if} ><textarea style="width:500px;height:170px;" name="default_value[TEXT]">{$vocabulary.vocabulary_default_content}</textarea></div>

        <div id="default-value-bool" {if $vocabulary.vocabulary_type == 'BOOLEAN'}  style="display:block" {else} style="display:none" {/if}  >
	<label for="default-value-bool-true">True</label> 
	<input type="radio" id="default-value-bool-true" name="default_value[BOOLEAN]" value="TRUE" {if $vocabulary.vocabulary_default_content == 'TRUE' } checked="true" {/if}  />
	<label for="default-value-bool-false"   >False</label> 
	<input type="radio" id="default-value-bool-false" name="default_value[BOOLEAN]" {if $vocabulary.vocabulary_default_content == 'FALSE' } checked="true" {/if} value="FALSE" /></div>
</td></tr>
<tr><td style="width:100px" valign="top"></td>
<td><input type="checkbox" {if $vocabulary.schemadefinition_required == 'TRUE' } checked {/if}  name="schemadefinition_required" value="TRUE" />Required</td></tr>

<tr>
<td>
</td>

<td>
	<input type="submit" value="Save" />
	<input type="button" value="Remove" onclick="{literal}if(confirm('Are you sure you want to delete this property ?')) workWithItem('removeSchemaElement');{/literal}" />
	<input type="button" value="Cancel" onclick="window.location='{$domain}items';" />
</td>
</tr>
</table>
</form>

</div>