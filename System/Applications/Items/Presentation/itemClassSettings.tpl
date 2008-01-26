<script language="javascript" type="text/javascript">
{literal}
var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;


function setSelectedItem(item_id, pageName, rowColor){
	
	var row='itemproperty_'+item_id;
	var editForm = document.getElementById('pageViewForm');
	rowColor='#'+rowColor;
	selectedPage = item_id;
	selectedPageName = pageName;
	if(lastRow){
		document.getElementById(lastRow).style.backgroundColor=lastRowColor;
	}
	document.getElementById(row).style.backgroundColor='#99F';
	{/literal}
	{literal}
	lastRow = row;
	lastRowColor = rowColor;
	editForm.itemproperty_id.value = item_id;
}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(editForm){
		
		editForm.action=pageAction;
		editForm.submit();
		
	}
}

function viewPage(){
{/literal}
	var pageURL = '{$domain}{$section}/editItemProperty?property_id='+selectedPage;
	window.open(pageURL);
{literal}
}
{/literal}
</script>
<h3><a href="{$domain}{$section}">Data Manager</a> &gt; <a href="{$domain}{$section}">Model Templates</a> &gt; {$itemclass.itemclass_name} </h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click a page to edit or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="post" action="{$domain}{$section}/insertItemClassSettings">
<input type="hidden" name="itemclass_id" value="{$itemclass.itemclass_id}" />

<table border="0" cellspacing="10" cellpadding="0" style="width:850px">
  <tr>
    <td valign="top" style="width:550px">
<table cellpadding="0" cellspacing="0" border="0" style="width:550px;border:1px solid #ccc">
{if is_array( $content.settings)}
	{foreach from=$settings key=key item=item}
			
		{if $item.itemproperty_datatype == "NODE"}
		{else}
		<tr class="mainpanel-row-{cycle values="ddd,fff"}" id="itemproperty_{$item.itemproperty_id}" ondblclick="window.location='{$domain}{$section}/editItemProperty?property_id={$item.itemproperty_id}'">
			<!-- onmouseover="this.style.backgroundColor='#f90'" onmouseout="this.style.backgroundColor='{cycle name="return" values="fff,ddd"}'-->
			<td style="padding-left:{$indent}px;cursor:pointer" onclick="setSelectedItem('{$item.itemproperty_id}', '{$item.itemproperty_id|escape:quotes}', '{cycle name="returnValue" values="ddd,fff"}');" class="text">
				{$item.itemproperty_name} 
			</td>
			<td>
				<input type="text" value="{$item.itemproperty_setting_value}" name="{$item.itemproperty_id}">			
			</td>
		</tr>
		{/if}
	{/foreach}

	<tr>
		<td>
		</td>
		<td>
			<input type="submit" value="Save">		
			<input type="submit" value="Cancel">
		</td>
	</tr>
{else}
 <div class="notify-failure">No Settings were found. This model contains no Settings.</div>
{/if}
</table></td>

<td valign="top" style="width:250px">Actions<ul class="actions-list">
<!--
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('addVocabulary'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_add.png" border="0" alt="">Add</a></li>
	<li class="disabled-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editSchemaVocabulary'); }{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt="">Edit</a></li>
	<li class="disabled-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSchemaElement');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="">Delete</a></li>
-->
</ul>

</td>

</tr>
</form>
</table>







