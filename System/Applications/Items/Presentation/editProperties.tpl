<h3><a href="{$domain}{$section}">Chooser</a> -> <a href="{$domain}{$section}/getItemClasses">Collection Manager</a> -> <a href="{$domain}{$section}/getItemClassMembers?class_id={$content.itemClass.itemclass_id}">{$content.itemClass.itemclass_name}</a> -> Edit Properties</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click a page to edit or choose from the options on the right.</div>

<form method="post" enctype="multipart/form-data" action="{$domain}{$section}/insertItem">
<input type="hidden" name="itemclass_id" value="{$content.itemProperties.itemclass_id}">
<!--<legend>Add A New {$content.itemProperties.itemclass_name}</legend>-->
<table style="width:750px" border="0" cellspacing="2" cellpadding="1">
<tr><td style="width:150px" valign="top">{$content.itemProperties.itemclass_name} Name</td>
<td><input type="text" name="itemName" size="20" /> (This can be any string)</td></tr>
{foreach from=$content.formProperties item="itemProperty"}
<tr><td style="width:150px" valign="top">{$itemProperty.itemproperty_name}</td>
<td>
{if $itemProperty.itemproperty_datatype == "FILE"}
<input type="file" name="itemProperty[{$itemProperty.itemproperty_varname}]" />
<input type="hidden" name="MAX_FILE_SIZE" value="124000">

{elseif $itemProperty.itemproperty_datatype == "BOOLEAN"}
<input type="radio" name="itemProperty[{$itemProperty.itemproperty_varname}]" value="FALSE" />FALSE
<input type="radio" name="itemProperty[{$itemProperty.itemproperty_varname}]" value="TRUE" />TRUE
{elseif $itemProperty.itemproperty_datatype == "NUMERIC"}
<input type="text" name="itemProperty[{$itemProperty.itemproperty_varname}]" size="12" maxlength="6" />
{elseif $itemProperty.itemproperty_datatype == "TEXT"}
<textarea name="itemProperty[{$itemProperty.itemproperty_varname}]" style="width:500px;height:110px"></textarea>
{elseif $itemProperty.itemproperty_datatype == "OTHERCLASS"}
<select name="itemProperty[{$itemProperty.itemproperty_varname}]" style="width:200px">
{foreach from=$content.otherClassMenus[$itemProperty.itemproperty_varname] item="otherClassMemberItem"}
<option value="{$otherClassMemberItem.id}">{$otherClassMemberItem.name}</option>
{/foreach}
</select>
{elseif $itemProperty.itemproperty_datatype == "DATE"}
<select name="itemProperty[{$itemProperty.itemproperty_varname}][M]">
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
<select name="itemProperty[{$itemProperty.itemproperty_varname}][D]">
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
<input type="text" name="itemProperty[{$itemProperty.itemproperty_varname}][Y]" size="5" maxlength="4" value="2006" />
{else}
<input type="text" name="itemProperty[{$itemProperty.itemproperty_varname}]" size="20" />
{/if}</td></tr>
{/foreach}
<tr><td colspan="2" align="right">
<input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$content.itemProperties.itemclass_webid}';" value="Cancel" />
<input type="submit" value="Add Item" /></td></tr>
</table>
</form>
