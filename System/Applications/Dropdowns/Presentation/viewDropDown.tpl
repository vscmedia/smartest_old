<script language="javascript" type="text/javascript">

{*

// alert(document.getElementById('pageViewForm'));
function setSelectedItem(page_id,rowColor){
	document.getElementById('item-specific-actions').style.display='block';
	var row='item_'+page_id;
	var editForm = document.getElementById('pageViewForm');
	
	// rowColor='#'+rowColor;
	selectedPage = page_id;
	// alert (page_id);
	if(lastRow){
		document.getElementById(lastRow).className="option";
		// document.getElementById('pageNameField').innerHTML='';
		// document.getElementById(lastRow).style.color='';
	}
	// alert(row);
	document.getElementById(row).className="selected-option";
	// document.getElementById('pageNameField').innerHTML='<b><img border="0" src="/Resources/Icons/page_code.png">&nbsp;Data Type:&nbsp;'+pageName+'<'+'/b>';
	lastRow = row;
	lastRowColor = rowColor;
	editForm.drop_down_value_id.value = page_id;
}

function workWithItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');	
	if(selectedPage && editForm){
		// alert('required Vars defined');
{/literal}		editForm.action="/{$section}/"+pageAction; {literal}
		// alert(editForm.action);
		editForm.submit();
	}
}

*}

</script>

<div id="work-area">

<h3>
<a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">DropDowns</a> &gt;{$dropdown_details.label}</h3>
<a name="top"></a>

<div class="instruction">Double click one of the model_ones to edit it or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="drop_down" id="drop_down" value="{$dropdown_details.id}" />
  <input type="hidden" name="drop_down_value_id" id="item_id_input" value="" />
</form>

{if !empty($dropdown_options)}
	<ul class="{if count($dropdown_options) > 10}options-list{else}options-grid{/if}" id="{if $content.count > 10}options_list{else}options_grid{/if}">

	{foreach from=$dropdown_options key=key item="option"}
 	 <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editDropDownValue?drop_down={$dropdown_details.id}&drop_down_value_id={$option.id}'">
 	 <a class="option" id="item_{$option.id}" onclick="setSelectedItem('{$option.id}', 'fff');" >
 	 <img border="0" src="{$domain}Resources/Icons/package.png">
  	{$option.label}</a></li>
	{/foreach}
	</ul>
{else}
  <div class="instruction">This dropdown menu has no options yet.</div>
  <a href="{dud_link}" onclick="window.location='{$domain}{$section}/addDropDownValue?drop_down={$dropdown_details.id}'">Click here to add a new Drop Down Value</a>
{/if}
</td>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
  <li class="permanent-action"><b>Selection Options</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editDropDownValue');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit</a></li>
 <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteDropDownValue');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete</a></li>
 
</ul>
<ul class="actions-list">
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addDropDownValue?drop_down={$dropdown_details.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Add DropDown value</a></li> 
{if $content.count}
<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/reorderDropDownValue?drop_down={$dropdown_details.id}'"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Re-order DropDownValues</a></li> 
{/if}
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Drops Down</a></li>
</ul>

</div>