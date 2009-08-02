<script language="javascript" type="text/javascript">
{literal}

function exportItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(selectedPage && editForm){
		{/literal}		
		editForm.action="/{$section}/"+pageAction; 
		{literal}
		editForm.submit();
	}
}

function openPage(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	if(editForm){
{/literal}		editForm.action="/{$section}/"+pageAction+"?item_id=";{literal}
		editForm.submit();
	}
}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}smartest/data">Items</a> &gt; <a href="{$domain}smartest/models">Models</a> &gt; {$model.plural_name}</h3>
<a name="top"></a>
<div class="instruction">Double click one of the {$content.itemBaseValues.itemclass_plural_name|lower} to edit it or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="item_id" id="item_id_input" value="" />
  <input type="hidden" name="class_id" value="{$model.id}" />
</form>

<div class="special-box">
  <form id="mode-form" method="get" action="">
    <input type="hidden" name="class_id" value="{$model.id}" />
    Show: <select name="mode" onchange="$('mode-form').submit();">
      <option value="7"{if $mode == 7} selected="selected"{/if}>All {$model.plural_name|strtolower} not archived</option>
      <option value="1"{if $mode == 1} selected="selected"{/if}>Unpublished {$model.plural_name|strtolower}</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>Unpublished {$model.plural_name|strtolower} that are not approved</option>
      <option value="3"{if $mode == 3} selected="selected"{/if}>Unpublished {$model.plural_name|strtolower} that have been approved</option>
      <option value="4"{if $mode == 4} selected="selected"{/if}>Published {$model.plural_name|strtolower}</option>
      <option value="5"{if $mode == 5} selected="selected"{/if}>Published {$model.plural_name|strtolower} that have been modified, but not re-approved</option>
      <option value="6"{if $mode == 6} selected="selected"{/if}>Published {$model.plural_name|strtolower} that have been modified and re-approved</option>
      <option value="0"{if $mode == 0} selected="selected"{/if}>All {$model.plural_name|strtolower}</option>
      <option value="8"{if $mode == 8} selected="selected"{/if}>All archived {$model.plural_name|strtolower}</option>
    </select>
    containing <input type="text" name="q" value="{$query}" />
    <input type="submit" value="Go" />
  </form>
</div>

<div id="options-view-chooser">
Found {$num_items} {if $num_items != 1}{$model.plural_name}{else}{$model.name}{/if}. View as:
<a href="{dud_link}" onclick="setView('list', 'options_list')">List</a> /
<a href="{dud_link}" onclick="setView('grid', 'options_list')">Icons</a>
</div>
  
  {* <ul class="{if count($items) > 30}options-list{else}options-grid{/if}" id="options_list"> *}
    <ul class="options-grid" id="options_list">
  {foreach from=$items key="key" item="item"}
	
    <li ondblclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}'">
      <a href="{dud_link}" class="option" id="item_{$item.id}" onclick="setSelectedItem('{$item.id}', '{$item.name|escape:quotes}');">
        
        <img src="{$domain}Resources/Icons/item.png" border="0" />{$item.name}</a>{* if $item.public=='FALSE'}&nbsp;(hidden){/if *}</li>

  {/foreach}
  
  {* <object id="head-logo" data="{$domain}Resources/Icons/item.svg" type="image/svg+xml"> *}
  </ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected {$model.name}</b></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/information.png"> <a href="{dud_link}" onclick="workWithItem('itemInfo');">{$model.name} info</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/pencil.png"> <a href="{dud_link}" onclick="workWithItem('openItem');">Edit {$model.name}</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/lock_open.png"> <a href="{dud_link}" onclick="workWithItem('releaseItem');">Release</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="workWithItem('publishItem');">Publish</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="workWithItem('unpublishItem');">Un-Publish</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/accept.png"> <a href="{dud_link}" onclick="workWithItem('addTodoItem');">Add new to-do</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="workWithItem('toggleItemArchived');">Archive/Un-archive</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> <a href="{dud_link}" onclick="if(selectedPage && confirm('Are you sure you want to delete this {$model.name|lower} ?')) {ldelim}workWithItem('deleteItem');{rdelim}">Delete</a></li>
{* <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="if(selectedPage) {ldelim}workWithItem('duplicateItem');{rdelim}">Duplicate</a></li> *}

  <!--<tr style="height:25px"><td class="text"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="{literal}if(selectedPage){workWithItem('getItemXml');}{/literal}">Export</a></td></tr>-->
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Model Options</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png" /> Add a new {$model.name}</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseUserHeldItems?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/lock_open.png" /> Release all {$model.plural_name}</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editModel?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/information.png" /> Model info</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/pencil.png" /> Edit model properties</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/addSet?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png" /> Create a new set from this model</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/getItemClassSets?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/folder_old.png" /> View data sets for this model</a></li>
{* <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/importData?class_id={$itemBaseValues.itemclass_id}';"><img border="0" src="{$domain}Resources/Icons/page_code.png" /> Import data from CSV</a></li> *}
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
  {foreach from=$recent_items item="recent_item"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><img border="0" src="{$recent_item.small_icon}" /> {$recent_item.label}</a></li>
  {/foreach}
</ul>

</div>