<script type="text/javascript">
var customVarName = false;
{literal}
function checkPrepertyType(){
// alert(document.getElementById('itemproperty_datatype').value);
	switch(document.getElementById('itemproperty_datatype').value){
		case '1':
			document.getElementById('default-value-text').style.display = "block";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		case '2':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "block";	
			document.getElementById('type-description').style.display = "block";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		case '3':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "block";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		case '4':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "block";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		case '5':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "block";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		case '6':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "block";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		case '7':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "block";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		case '8':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "block";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
		default:
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			break;
	}
}

function setVarName(){
	if(document.getElementById('itemproperty_varname').value.length < 1){customVarName = false}
	
	var propertyName = document.getElementById('itemproperty_name').value;
	
	if(!customVarName){
		document.getElementById('itemproperty_varname').value = smartest.toVarName(propertyName);
	}
}
{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}{$section}">Data Manager</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Add Property</h3>

<div class="instruction">You will be adding a new property to the model "{$model.plural_name}".</div>

<form action="{$domain}{$section}/insertItemClassProperty" method="post" enctype="multipart/form-data">
  <input type="hidden" name="class_id" value="{$model.id}" />

  <div class="edit-form-row">
      <div class="form-section-label">Property Name</div>
      <input type="text" value="" name="itemproperty_name" id="itemproperty_name" />
      <span class="help-text">properties must be three characters or longer and start with a letter.</span>
  </div>
  
  <div class="edit-form-row">
      <div class="form-section-label">Property Type</div>
      <select name="itemproperty_datatype" id='itemproperty_datatype' onchange="">
  	    {foreach from=$data_types item="data_type"}
  	    <option value="{$data_type.id}"{if $data_type.id==$property.datatype} selected="selected"{/if}>{$data_type.label}</option>
  	    {/foreach}
      </select>
  </div>

 {*   <tr>
      <td style="width:200px" valign="top">Property Type:</td>
      <td>
	       <select name="itemproperty_datatype" id='itemproperty_datatype' onchange="checkPrepertyType()">
		<option value="">Select One</option>
		{foreach from=$Types item=propertyType}
		<option value="{$propertyType.propertytype_id}" {if $propertyType.propertytype_id==$content.type} selected{/if}>{$propertyType.propertytype_label}</option>
		{/foreach}
		</select>
		
		<select name="itemproperty_datatype" id='itemproperty_datatype' onchange="checkPrepertyType()">
		{foreach from=$datatypes item=propertyType}
		<option value="{$propertyType.id}" {if $propertyType.id==$content.type} selected{/if}>{$propertyType.label}</option>
		{/foreach}
		</select>

		<div id="type-description" {if $content.type} style="display:none" {else}style="font-size:0.8em;margin-top:5px"{/if}>Maximum 255 characters</div>

 		<div id="default-value-dropdownMenu" {if $content.sel_id} style="display:block" {else}style="display:none"{/if}><br>
		<select name="dropdownMenu" onchange="window.location='{$domain}{$section}/addPropertyToClass?class_id={$baseValues.itemclass_id}&amp;name='+document.getElementById('itemproperty_name').value+'&type='+document.getElementById('itemproperty_datatype').value+'&sel_id=' + document.getElementById('dropdownMenu').value" id="dropdownMenu"  >
		<option value="">Select One</option>
		{foreach from=$dropdownMenu key=key item=dropdown}
		<option value="{$dropdown.dropdown_id}" {if $dropdown.dropdown_id==$content.sel_id} selected{/if}>{$dropdown.dropdown_label}</option>{/foreach}
		</select>
		</div>


		<div id="default-value-model"  {if $content.model_id} style="display:block" {else}style="display:none"{/if}><br>
		<select  name="select_model" id="model_select"  onchange="window.location='{$domain}{$section}/addPropertyToClass?class_id={$baseValues.itemclass_id}&amp;name='+document.getElementById('itemproperty_name').value+'&type='+document.getElementById('itemproperty_datatype').value+'&model_id=' + document.getElementById('model_select').value" >
		<option value="">Select One</option>
		{foreach from=$models key=key item=item}
		<option value="{$item.itemclass_id}" {if $item.itemclass_id==$content.model_id} selected{/if}>{$item.itemclass_name}</option>{/foreach}</select>
		</div>
	</td>
	</tr>
    <tr>
      <td style="width:200px" valign="top">Default Value (optional):</td>
      <td>
        	<div id="default-value-text" style="display:none">
		<input type="text" style="width:240px" name="default_value[text]"  />
		</div>

	        <div id="default-value-longtext" style="display:none"><textarea style="width:500px;height:170px;" name="default_value[longtext]"></textarea>
		</div>

	 	<div id="default-value-bool" style="display:none">
		<label for="default-value-bool-true">Checked</label> 
		<input type="radio" id="default-value-bool-true" name="default_value[bool]" value="TRUE" {if $selectedVocabulary.vocabulary_default_content == "TRUE"} checked {/if} />
		<label for="default-value-bool-false"   >UnChecked</label> 
		<input type="radio" id="default-value-bool-false" name="default_value[bool]" value="FALSE" {if $selectedVocabulary.vocabulary_default_content == "FALSE"} checked {/if}/>
		</div>

		<div id="default-value-dropdown" >
{if $content.sel_id}	
		<select name="default_value[option_value]" id="defaultdropdown" >
		{foreach from=$dropdownValues key=key item=values}
		<option value="{$values.dropdownvalue_label}" >{$values.dropdownvalue_label}</option>
		{/foreach}
		</select>
{elseif $content.model_id}		
		<select name="default_value[sel_item]" id="defaultitems" >
		{foreach from=$sel_items key=key item=details}
		<option value="{$details.item.item_name}" >{$details.item.item_name}</option>
		{/foreach}
		</select>
{/if}
		</div>
	
        	<div id="default-value-url" style="display:none">
		<input type="text" style="width:240px" name="default_value[url]"  />
		</div>

	 	<div id="default-value-date" style="display:none"><select name="default_value[M]">
		<option value="01">January</option>
		<option value="02">February</option>
		<option value="03">March</option>
		<option value="04">April</option>
		<option value="05">May</option>
		<option value="06">June</option>
		<option value="07">July</option>
		<option value="08">August</option>
		<option value="09">September</option>
		<option value="10">October</option>
		<option value="11">November</option>
		<option value="12">December</option>
		</select>
		<select name="default_value[D]">
		<option value="01">1st</option>
		<option value="02">2nd</option>
		<option value="03">3rd</option>
		<option value="04">4th</option>
		<option value="05">5th</option>
		<option value="06">6th</option>
		<option value="07">7th</option>
		<option value="08">8th</option>
		<option value="09">9th</option>
		<option value="10">10th</option>
		<option value="11">11th</option>
		<option value="12">12th</option>
		<option value="13">13th</option>
		<option value="14">14th</option>
		<option value="15">15th</option>
		<option value="16">16th</option>
		<option value="17">17th</option>
		<option value="18">18th</option>
		<option value="19">19th</option>
		<option value="20">20th</option>
		<option value="21">21st</option>
		<option value="22">22nd</option>
		<option value="23">23rd</option>
		<option value="24">24th</option>
		<option value="25">25th</option>
		<option value="26">26th</option>
		<option value="27">27th</option>
		<option value="28">28th</option>
		<option value="29">29th</option>
		<option value="30">30th</option>
		<option value="31">31st</option>
		</select>
	</div>



        <div id="default-value-file" style="display:none">
		<input type="file" name="File" >
	</div>



	</td>

    </tr> *}

    <div class="edit-form-row">
        <div class="form-section-label">Property Required:</div>
        <input type="checkbox" name="itemproperty_required" id="is-required" value="TRUE" /><label for="is-required">Check if this property is required</label>
    </div>
    
    <div class="edit-form-row">
        <div class="buttons-bar">
            Continue to: <select name="continue"><option value="PROPERTIES">View other properties of model {$model.name}</option><option value="NEW_PROPERTY">Add another property to model {$model.name}</option></select>
            <input type="button" value="Cancel" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}';" />
            <input type="submit" value="Save Property" />
        </div>
    </div>

</form>

</div>