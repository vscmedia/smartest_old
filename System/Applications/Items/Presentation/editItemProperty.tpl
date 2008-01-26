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
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
		case '2':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "block";	
			document.getElementById('type-description').style.display = "block";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";	
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
		case '3':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "block";
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";	
			document.getElementById('default-value-url').style.display = "none";	
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
		case '4':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";		
			document.getElementById('default-value-dropdownMenu').style.display = "block";
			document.getElementById('default-value-dropdown').style.display = "block";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
		case '5':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";			
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			document.getElementById('default-value-url').style.display = "block";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
		case '6':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";	
			document.getElementById('default-value-dropdown').style.display = "none";	
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "block";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
		case '7':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";			
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "block";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
		case '8':
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";	
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";
			document.getElementById('default-value-dropdownMenu').style.display = "none";	
			document.getElementById('default-value-dropdown').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";	
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "block";
			document.getElementById('default-value-dropitems').style.display = "block";
			break;
		default:
			document.getElementById('default-value-text').style.display = "none";
			document.getElementById('default-value-longtext').style.display = "none";
			document.getElementById('type-description').style.display = "none";
			document.getElementById('default-value-bool').style.display = "none";			
			document.getElementById('default-value-dropdownMenu').style.display = "none";
			document.getElementById('default-value-dropdown').style.display = "none";
			document.getElementById('default-value-url').style.display = "none";
			document.getElementById('default-value-date').style.display = "none";
			document.getElementById('default-value-file').style.display = "none";
			document.getElementById('default-value-model').style.display = "none";
			document.getElementById('default-value-dropitems').style.display = "none";
			break;
	}
}


{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}{$section}">Data Manager</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Edit Property</h3>

<div id="instruction">You are editing the property &quot;{$property.name}&quot; of model &quot;{$model.plural_name}&quot;</div>

<form action="{$domain}{$section}/updateItemClassProperty" method="post" enctype="multipart/form-data">
  
  <input type="hidden" name="class_id" value="{$model.id}" />
  <input type="hidden" name="itemproperty_id" value="{$property.id}" />

<div class="edit-form-row">
    <div class="form-section-label">Property Name</div>
    <input type="text" value="{$property.name}" name="itemproperty_name" id="itemproperty_name" />
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

{* start default types *}

{* <div id="type-description" style="display:none">Maximum 255 characters</div>

<div id="default-value-dropdownMenu"  {if $content.type==4} style="display:block" {else}style="display:none"{/if}>
		<select name="dropdownMenu" onchange="window.location='{$domain}{$section}/editItemProperty?class_id={$itemclass.itemclass_id}&amp;itemproperty_id={$details.itemproperty_id}&amp;name='+document.getElementById('itemproperty_name').value+'&amp;type='+document.getElementById('itemproperty_datatype').value+'&amp;sel_id=' + document.getElementById('dropdownMenu').value" id="dropdownMenu"  >
		{foreach from=$dropdownMenu key=key item=dropdown}
		<option value="{$dropdown.dropdown_id}" {if $dropdown.dropdown_id == $content.sel_id} selected{/if}>{$dropdown.dropdown_label}</option>{/foreach}
		</select>
		</div>
		
		<div id="default-value-model"  {if $content.type == 8} style="display:block" {else}style="display:none"{/if}>
		<select  name="select_model" id="model_select"  onchange="window.location='{$domain}{$section}/editItemProperty?class_id={$itemclass.itemclass_id}&amp;itemproperty_id={$details.itemproperty_id}&amp;name='+document.getElementById('itemproperty_name').value+'&amp;type='+document.getElementById('itemproperty_datatype').value+'&amp;model_id=' + document.getElementById('model_select').value" >
		<option value="">Select One</option>
		{foreach from=$models key=key item=item}
		<option value="{$item.itemclass_id}" {if $item.itemclass_id==$content.model_id} selected{/if}>{$item.itemclass_name}</option>{/foreach}</select>
		</div>
	</td>
	</tr>
    <tr>
      <td style="width:200px" valign="top">Default Value (optional):</td>
      <td>
        	<div id="default-value-text" {if $content.type==1}style="display:block"{else}style="display:none"{/if}>
		<input type="text" style="width:240px" name="default_value[text]" value="{$details.itemproperty_defaultvalue}"  />
		</div>

	        <div id="default-value-longtext" {if $content.type==2}style="display:block"{else}style="display:none"{/if}><textarea style="width:500px;height:170px;" name="default_value[longtext]">{$details.itemproperty_defaultvalue}</textarea>
		</div>

	 	<div id="default-value-bool" {if $content.type==3}style="display:block"{else}style="display:none"{/if}>
		<label for="default-value-bool-true">Checked</label> 
		<input type="radio" id="default-value-bool-true" name="default_value[bool]" value="TRUE" {if $details.itemproperty_defaultvalue == "TRUE"} checked {/if} />
		<label for="default-value-bool-false"   >UnChecked</label> 
		<input type="radio" id="default-value-bool-false" name="default_value[bool]" value="FALSE" {if $details.itemproperty_defaultvalue == "FALSE"} checked {/if}/>
		</div>
   
		<div id="default-value-dropdown" {if $content.sel_id || $content.model_id }style="display:block"{else}style="display:none"{/if}>	
		{if $content.type==4}
		<div>
		<select name="default_value[option_value]" id="defaultdropdown" >
		{foreach from=$dropdownValues key=key item=values}
		<option value="{$values.dropdownvalue_label}" {if $values.dropdownvalue_label==$details.itemproperty_defaultvalue} selected{/if}>{$values.dropdownvalue_label}</option>
		{/foreach}
		</select>
		</div>
		{elseif $content.type==8}
		<div>
		<select name="default_value[sel_item]" id="defaultitems" >
		{foreach from=$sel_items key=key item=item_detail}
		<option value="{$item_detail.item.item_name}" >{$item_detail.item.item_name}</option>
		{/foreach}
		</select>
		</div>
		{/if}
		</div>


	       	<div id="default-value-url" {if $content.type==5}style="display:block"{else}style="display:none"{/if}>
		<input type="text" style="width:240px" name="default_value[url]"  value="{$details.itemproperty_defaultvalue}" />
		</div>

	 	<div id="default-value-date" {if $content.type==6}style="display:block"{else}style="display:none"{/if} ><select name="default_value[M]">
		<option value="01" {if $content.month == "01"} selected {/if}>January</option>
		<option value="02" {if $content.month == "02"} selected {/if}>February</option>
		<option value="03" {if $content.month == "03"} selected {/if}>March</option>
		<option value="04" {if $content.month == "04"} selected {/if}>April</option>
		<option value="05" {if $content.month == "05"} selected {/if}>May</option>
		<option value="06" {if $content.month == "06"} selected {/if}>June</option>
		<option value="07" {if $content.month == "07"} selected {/if}>July</option>
		<option value="08" {if $content.month == "08"} selected {/if}>August</option>
		<option value="09" {if $content.month == "09"} selected {/if}>September</option>
		<option value="10" {if $content.month == "10"} selected {/if}>October</option>
		<option value="11" {if $content.month == "11"} selected {/if}>November</option>
		<option value="12" {if $content.month == "12"} selected {/if}>December</option>
		</select>
	<select name="default_value[D]">
		<option value="01" {if $content.day == "01"} selected {/if}>1st</option>
		<option value="02" {if $content.day == "02"} selected {/if}>2nd</option>
		<option value="03" {if $content.day == "03"} selected {/if}>3rd</option>
		<option value="04" {if $content.day == "04"} selected {/if}>4th</option>
		<option value="05" {if $content.day == "05"} selected {/if}>5th</option>
		<option value="06" {if $content.day == "06"} selected {/if}>6th</option>
		<option value="07" {if $content.day == "07"} selected {/if}>7th</option>
		<option value="08" {if $content.day == "08"} selected {/if}>8th</option>
		<option value="09" {if $content.day == "09"} selected {/if}>9th</option>
		<option value="10" {if $content.day == "10"} selected {/if}>10th</option>
		<option value="11" {if $content.day == "11"} selected {/if}>11th</option>
		<option value="12" {if $content.day == "12"} selected {/if}>12th</option>
		<option value="13" {if $content.day == "13"} selected {/if}>13th</option>
		<option value="14" {if $content.day == "14"} selected {/if}>14th</option>
		<option value="15" {if $content.day == "15"} selected {/if}>15th</option>
		<option value="16" {if $content.day == "16"} selected {/if}>16th</option>
		<option value="17" {if $content.day == "17"} selected {/if}>17th</option>
		<option value="18" {if $content.day == "18"} selected {/if}>18th</option>
		<option value="19" {if $content.day == "19"} selected {/if}>19th</option>
		<option value="20" {if $content.day == "20"} selected {/if}>20th</option>
		<option value="21" {if $content.day == "21"} selected {/if}>21st</option>
		<option value="22" {if $content.day == "22"} selected {/if}>22nd</option>
		<option value="23" {if $content.day == "23"} selected {/if}>23rd</option>
		<option value="24" {if $content.day == "24"} selected {/if}>24th</option>
		<option value="25" {if $content.day == "25"} selected {/if}>25th</option>
		<option value="26" {if $content.day == "26"} selected {/if}>26th</option>
		<option value="27" {if $content.day == "27"} selected {/if}>27th</option>
		<option value="28" {if $content.day == "28"} selected {/if}>28th</option>
		<option value="29" {if $content.day == "29"} selected {/if}>29th</option>
		<option value="30" {if $content.day == "30"} selected {/if}>30th</option>
		<option value="31" {if $content.day == "31"} selected {/if}>31st</option>
	</select>
	</div>

        <div id="default-value-file" {if $content.type==7}style="display:block"{else}style="display:none"{/if}>
		<input type="hidden" name="File_old"  value="{$details.itemproperty_defaultvalue}" > &nbsp;<b>{$details.itemproperty_defaultvalue}</b><input type="button" value="Change" onclick="document.getElementById('img').style.display = 'block';">
		<input type="file" name="File" id="img" style="display:none"  > 
	</div>


	</td>

    </tr> *}
    
    <div class="edit-form-row">
        <div class="form-section-label">Property Required:</div>
        <input type="checkbox" name="itemproperty_required" id="is-required" value="TRUE"  {if $property.required == "TRUE"} checked="checked"{/if}/><label for="is-required">Check if required</label>
    </div>
    
    <div class="edit-form-row">
        <div class="buttons-bar">
            <input type="button" value="Cancel" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}';" />
            <input type="submit" value="Save Changes" />
        </div>
    </div>

</form>

</div>