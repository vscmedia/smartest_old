<script type="text/javascript">
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

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> > <a href="{$domain}modeltemplates/">Templates</a>  >  <a href="{$domain}modeltemplates/schemaDefinition?schema_id={$content.schema_id}">{$content.schema_name}</a> > Add A Child</h3>
<div class="instruction">Please enter the details.</div>

<div id="options-view-chooser">
<form action="{$domain}{$section}/addSchemaVocabulary" method="get" id="childTypeForm" onchange="document.getElementById('childTypeForm').submit();">
  <input type="hidden" name="schemadefinition_id" value="{$content.schemadefinition_id}" />
  <input type="hidden" name="vocabulary_id" value="{$content.vocabulary_id}" />
  <input type="hidden" name="schema_id" value="{$content.schema_id}" />
<input type="hidden" name="type_node" value="{$content.type_node}" />
  New Child Type: <select name="childType">

{if $content.type_node=='NODE'}
    <option value="tag" {if $smarty.get.childType == "tag"} selected="selected"{/if}>Tag Element</option>
    <option value="node" {if $smarty.get.childType == "node"} selected="selected"{/if}>Node Element</option>
{else} <option value="select" {if $smarty.get.childType == "select"} selected="selected"{/if}>Select One</option>
{/if}
    <option value="attribute"{if $smarty.get.childType == "attribute"} selected="selected"{/if}>Attribute</option>

  </select>
</form>
</div>

<form action="" method="post" id="pageViewForm" name="pageViewForm">
  <input type="hidden" name="schema_definition_id" value="{$content.schemadefinition_id}" />
  <input type="hidden" name="vocabulary_id" value="{$content.vocabulary_id}" />
  <input type="hidden" name="schema_id" value="{$content.schema_id}" />

{if $smarty.get.childType == "attribute" }

<table style="width:750px;margin-left:10px;" border="0" cellspacing="2" cellpadding="1">   

<tr><td style="width:100px" valign="top">Attribute Name:</td>
<td><input type="text"  name="name"  /></td></tr>
<tr><td style="width:100px" valign="top">Default Value:</td>
<td><input type="text"  name="value"  /></td></tr>

<tr><td ></td>
<td>	<input type="submit" value="Add" onclick="document.pageViewForm.action='{$domain}{$section}/insertAttribute'" />&nbsp;&nbsp;<input type="button" value="Cancel" onclick="window.location='{$domain}modeltemplates/schemaDefinition?schema_id={$schema.schema_id}';" />
</td>
</tr>
</table>

{elseif $smarty.get.childType == "node"}

<table style="width:750px;margin-left:10px;" border="0" cellspacing="2" cellpadding="1">

<tr><td style="width:100px" valign="top">Element Name:</td>
<td><input type="text"  name="vocabulary_name"  /></td></tr>

<tr><td style="width:100px" valign="top">Prefix:</td>
<td><input type="text"  name="prefix"  /></input></tr>

<tr><td style="width:100px" valign="top"></td>
<td><input type="checkbox"  name="schemadefinition_required" value="TRUE"  />Required</td></tr>

<tr>
<td>
</td>

<td>
	<input type="submit" value="Add" onclick="document.pageViewForm.action='{$domain}{$section}/insertSchemaVocabularyNode'"/>	
	<input type="button" value="Cancel" onclick="window.location='{$domain}{$section}';" />
</td>
</tr>
</table>

{elseif $smarty.get.childType != 'select' || $smarty.get.childType==''}

<table style="width:750px;margin-left:10px;" border="0" cellspacing="2" cellpadding="1">

<tr><td style="width:100px" valign="top">Element Name:</td>
<td><input type="text"  name="vocabulary_name"  /></td></tr>

<tr><td style="width:100px" valign="top">Type:</td>
<td><select name="vocabulary_datatype"  onchange="checkPrepertyType()" id="datatype">
<option value="STRING" >String (255 Chars max)</option>
<option value="TEXT" >Full Text</option>
<option value="NUMERIC" >Number</option>
<option value="BOOLEAN" >True/False (boolean)</option>
</select></td></tr>

<tr><td style="width:100px" valign="top">Prefix:</td>
<td><input type="text"  name="prefix"  /></input></tr>

<tr>
<tr><td style="width:100px" valign="top">Default Value:</td>
<td>
	<div id="default-value-text" style="display:block">
	<input type="text" style="width:240px" name="default_value[STRING]"  /></div>
        <div id="default-value-num" style="display:none"><input type="text" style="width:120px" name="default_value[NUMERIC]"  /></div>
        <div id="default-value-longtext" style="display:none"><textarea style="width:500px;height:170px;" name="default_value[TEXT]"></textarea></div>

        <div id="default-value-bool" style="display:none">
	<label for="default-value-bool-true">True</label> 
	<input type="radio" id="default-value-bool-true" name="default_value[BOOLEAN]" value="TRUE"  />
	<label for="default-value-bool-false"   >False</label> 
	<input type="radio" id="default-value-bool-false" name="default_value[BOOLEAN]" value="FALSE" /></div></td></tr>
<tr><td style="width:100px" valign="top"></td>
<td><input type="checkbox"  name="schemadefinition_required" value="TRUE"  />Required</td></tr>

<tr>
<td>
</td>

<td>
	<input type="submit" value="Add"  onclick="document.pageViewForm.action='{$domain}{$section}/insertSchemaVocabulary'"/>	
	<input type="button" value="Cancel" onclick="window.location='{$domain}{$section}';" />
</td>
</tr>
</table>

{/if}
</form>
</div>