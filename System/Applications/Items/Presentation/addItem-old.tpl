<div id="work-area">

<h3><a href="{$domain}{$section}">Data Manager</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$content.itemProperties.itemclass_id}">{$content.itemProperties.itemclass_plural_name}</a> &gt; Add A New {$content.itemProperties.itemclass_name}</h3>
<a name="top"></a>
<div class="instruction">Add a new {$content.itemClass.itemclass_name|lower}</div>

<form method="post" enctype="multipart/form-data" action="{$domain}{$section}/insertItem">
<input type="hidden" name="class_id" value="{$content.itemProperties.itemclass_id}">
<input type="hidden" name="user_id" value="{$content.user_id}">

<table style="width:100%" border="0" cellspacing="2" cellpadding="1">
<tr><td valign="top">{$content.itemProperties.itemclass_name} Name</td>
<td><input type="text" name="itemName" size="30" /></td></tr>
<tr><td valign="top">Public View</td>
<td><label for="item_is_public">Visible</label><input type="radio" id="item_is_public" name="itemIsPublic" value="TRUE" checked="checked" />
<label for="item_is_not_public">Invisible</label><input type="radio" id="item_is_public" name="itemIsPublic" value="FALSE" /></td></tr>
{foreach from=$content.formProperties item="itemProperty"}
	<tr><td valign="top">{$itemProperty.itemproperty_name}</td>
	<td>
{if $itemProperty.itemproperty_datatype == 1}
	<input type="text" name="itemProperty[{$itemProperty.itemproperty_id}]" size="30" />
{elseif $itemProperty.itemproperty_datatype == 2}
	<textarea name="itemProperty[{$itemProperty.itemproperty_id}]" style="width:400px;height:110px"></textarea>
{elseif $itemProperty.itemproperty_datatype == 3}
	<input type="checkbox"  name="itemProperty[{$itemProperty.itemproperty_id}]" value="TRUE"  />
{elseif $itemProperty.itemproperty_datatype == 4}
	<select name="itemProperty[{$itemProperty.itemproperty_id}]">
	{foreach from=$itemProperty.dropdown item="dropdown"}
	<option value="{$dropdown.dropdownvalue_label}">{$dropdown.dropdownvalue_label}</option>
	{/foreach}
	</select>
{elseif $itemProperty.itemproperty_datatype == 5}
	<input type="text" name="itemProperty[{$itemProperty.itemproperty_id}]" size="30" />
{elseif $itemProperty.itemproperty_datatype == 6}
	<select name="itemProperty[{$itemProperty.itemproperty_id}][M]">
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
	<select name="itemProperty[{$itemProperty.itemproperty_id}][D]">
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
	<input type="text" name="itemProperty[{$itemProperty.itemproperty_id}][Y]" size="5" maxlength="4" value="2006" />
{elseif $itemProperty.itemproperty_datatype == 7}
	<input type="file" name="File_{$itemProperty.itemproperty_id}" />
	<input type="hidden" name="MAX_FILE_SIZE" value="124000">
{elseif $itemProperty.itemproperty_datatype == 8}
	<select name="itemProperty[{$itemProperty.itemproperty_id}]">
	{foreach from=$itemProperty.modeldropdown item="modeldropdown"}
	<option value="{$modeldropdown.item.item_name}">{$modeldropdown.item.item_name}</option>
	{/foreach}
	</select>
{/if}
	</td></tr>
{/foreach}
<tr><td colspan="2" align="right">
<div class="buttons-bar">
<input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$content.itemProperties.itemclass_id}';" value="Cancel" />
<input type="submit" value="Add Item" />
</div></td></tr>
</table>
</form>

</div>