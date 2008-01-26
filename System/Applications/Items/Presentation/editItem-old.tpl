<div id="work-area">

<h3><a href="{$domain}{$section}">Data Manager</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$itemProperties.itemclass_id}">{$itemProperties.itemclass_plural_name}</a> &gt; Edit {$itemProperties.itemclass_name}</h3>

<div id="instruction">You are editing the draft property values of this item</id>

<form action="{$domain}{$section}/updateItem" enctype="multipart/form-data" method="post">

<input type="hidden" name="class_id" value="{$itemProperties.itemclass_id}" />
<input type="hidden" name="item_id" value="{$itemProperties.item_id}" />

<table style="width:100%" border="0" cellspacing="2" cellpadding="1">
  <tr>
    <td colspan="2" valign="top">
      <div class="form-section-label">{$itemProperties.itemclass_name} Name</div>
      <input type="text" name="itemName" value="{$itemProperties.item_name}" style="width:250px" />
    </td>
  </tr>
  <tr>
    <td colspan="2" valign="top">
      <div class="form-section-label">Public View</div>
      <label for="item_is_public">Visible</label><input type="radio" id="item_is_public" name="itemIsPublic" value="TRUE"  {if $itemProperties.item_public == "TRUE"} checked="true" {/if} />
      <label for="item_is_not_public">Invisible</label><input type="radio" id="item_is_not_public" name="itemIsPublic" value="FALSE"  {if $itemProperties.item_public == "FALSE"} checked="true" {/if} />
    </td>
  </tr>
{foreach from=$formProperties item="itemProperty"}

  <tr>
    <td colspan="2" valign="top">
    <div class="form-section-label">{$itemProperty.itemproperty_name}</div>
{if $itemProperty.propertytype_name == "single_line_text_field"}
	<input type="text" name="itemProperty[{$itemProperty.itemproperty_id}]" value="{$itemProperty.itempropertyvalue_content}" style="width:250px" />
{elseif $itemProperty.propertytype_name == "multi_line_text_field"}
	<textarea name="itemProperty[{$itemProperty.itemproperty_id}]" style="width:500px;height:110px">{$itemProperty.itempropertyvalue_content}</textarea>	
{elseif $itemProperty.propertytype_name == "checkbox"}
	<input type="checkbox"  name="itemProperty[{$itemProperty.itemproperty_id}]" value="TRUE" {if $itemProperty.itempropertyvalue_content == 'TRUE'} checked {/if} />
{elseif $itemProperty.propertytype_name == "dropdown_menu"}
	<select name="itemProperty[{$itemProperty.itemproperty_id}]">
	{foreach from=$dropdown item="dropdown"}
	<option value="{$dropdown.dropdownvalue_label}" {if $itemProperty.itempropertyvalue_content == $dropdown.dropdownvalue_label} selected {/if}>{$dropdown.dropdownvalue_label}</option>
	{/foreach}
	</select>
{elseif $itemProperty.propertytype_name == "url"}
	<input type="text" name="itemProperty[{$itemProperty.itemproperty_id}]" value="{$itemProperty.itempropertyvalue_content}" size="20" />	
{elseif $itemProperty.propertytype_name == "date"} 
	<select name="itemProperty[{$itemProperty.itemproperty_id}][M]">
		<option value="01" {if $datePropertyValues.M == "01"} selected {/if}>January</option>
		<option value="02" {if $datePropertyValues.M == "02"} selected {/if}>February</option>
		<option value="03" {if $datePropertyValues.M == "03"} selected {/if}>March</option>
		<option value="04" {if $datePropertyValues.M == "04"} selected {/if}>April</option>
		<option value="05" {if $datePropertyValues.M == "05"} selected {/if}>May</option>
		<option value="06" {if $datePropertyValues.M == "06"} selected {/if}>June</option>
		<option value="07" {if $datePropertyValues.M == "07"} selected {/if}>July</option>
		<option value="08" {if $datePropertyValues.M == "08"} selected {/if}>August</option>
		<option value="09" {if $datePropertyValues.M == "09"} selected {/if}>September</option>
		<option value="10" {if $datePropertyValues.M == "10"} selected {/if}>October</option>
		<option value="11" {if $datePropertyValues.M == "11"} selected {/if}>November</option>
		<option value="12" {if $datePropertyValues.M == "12"} selected {/if}>December</option>
	</select>
	<select name="itemProperty[{$itemProperty.itemproperty_id}][D]">
		<option value="01" {if $datePropertyValues.D == "01"} selected {/if}>1st</option>
		<option value="02" {if $datePropertyValues.D == "02"} selected {/if}>2nd</option>
		<option value="03" {if $datePropertyValues.D == "03"} selected {/if}>3rd</option>
		<option value="04" {if $datePropertyValues.D == "04"} selected {/if}>4th</option>
		<option value="05" {if $datePropertyValues.D == "05"} selected {/if}>5th</option>
		<option value="06" {if $datePropertyValues.D == "06"} selected {/if}>6th</option>
		<option value="07" {if $datePropertyValues.D == "07"} selected {/if}>7th</option>
		<option value="08" {if $datePropertyValues.D == "08"} selected {/if}>8th</option>
		<option value="09" {if $datePropertyValues.D == "09"} selected {/if}>9th</option>
		<option value="10" {if $datePropertyValues.D == "10"} selected {/if}>10th</option>
		<option value="11" {if $datePropertyValues.D == "11"} selected {/if}>11th</option>
		<option value="12" {if $datePropertyValues.D == "12"} selected {/if}>12th</option>
		<option value="13" {if $datePropertyValues.D == "13"} selected {/if}>13th</option>
		<option value="14" {if $datePropertyValues.D == "14"} selected {/if}>14th</option>
		<option value="15" {if $datePropertyValues.D == "15"} selected {/if}>15th</option>
		<option value="16" {if $datePropertyValues.D == "16"} selected {/if}>16th</option>
		<option value="17" {if $datePropertyValues.D == "17"} selected {/if}>17th</option>
		<option value="18" {if $datePropertyValues.D == "18"} selected {/if}>18th</option>
		<option value="19" {if $datePropertyValues.D == "19"} selected {/if}>19th</option>
		<option value="20" {if $datePropertyValues.D == "20"} selected {/if}>20th</option>
		<option value="21" {if $datePropertyValues.D == "21"} selected {/if}>21st</option>
		<option value="22" {if $datePropertyValues.D == "22"} selected {/if}>22nd</option>
		<option value="23" {if $datePropertyValues.D == "23"} selected {/if}>23rd</option>
		<option value="24" {if $datePropertyValues.D == "24"} selected {/if}>24th</option>
		<option value="25" {if $datePropertyValues.D == "25"} selected {/if}>25th</option>
		<option value="26" {if $datePropertyValues.D == "26"} selected {/if}>26th</option>
		<option value="27" {if $datePropertyValues.D == "27"} selected {/if}>27th</option>
		<option value="28" {if $datePropertyValues.D == "28"} selected {/if}>28th</option>
		<option value="29" {if $datePropertyValues.D == "29"} selected {/if}>29th</option>
		<option value="30" {if $datePropertyValues.D == "30"} selected {/if}>30th</option>
		<option value="31" {if $datePropertyValues.D == "31"} selected {/if}>31st</option>
	</select>
	<input type="text" name="itemProperty[{$itemProperty.itemproperty_id}][Y]" size="5" maxlength="4" value="{$datePropertyValues.Y}" />
{elseif $itemProperty.propertytype_name == "file"}
	{$itemProperty.itempropertyvalue_content}
	<input type="hidden" name="MAX_FILE_SIZE" value="124000">
	<input type="hidden" name="File_old"  value="{$itemProperty.itempropertyvalue_content}" >
<input type="button" value="Change" onclick="document.getElementById('img').style.display = 'block';">
<input type="file" name="File_{$itemProperty.itemproperty_id}"   id="img" style="display:none" />
{elseif $itemProperty.propertytype_name == "another_model"}
	<select name="itemProperty[{$itemProperty.itemproperty_id}]">
	{foreach from=$modeldropdown item="modeldropdown"}
	<option value="{$modeldropdown.item.item_name}" {if $itemProperty.itempropertyvalue_content == $modeldropdown.item.item_name} selected {/if}>{$modeldropdown.item.item_name}</option>
	{/foreach}
    </select>
{/if}
    </td>
  </tr>
{/foreach}

</table>

<div class="buttons-bar">
  <input type="submit" value="Save Changes" />
  <input type="button" onclick="window.location='{$domain}{$section}/publishItem?item_id={$itemProperties.item_id}';" value="Publish" />
  <input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$itemProperties.itemclass_id}';" value="Done" />
</div>

</form>

</div>
