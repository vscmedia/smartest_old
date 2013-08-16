<script type="text/javascript">
  var itemList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_list');
</script>

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

{load_interface file="model_list_tabs.tpl"}

<h3><a href="{$domain}smartest/models">Items</a> &gt; {$model.plural_name}</h3>
<a name="top"></a>
<div class="instruction">Double click one of the {$model.plural_name|strtolower} below to edit it, or click once and choose from the options on the right.</div>

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
    filter by {$model.item_name_field_name|lower}: <input type="text" name="q" id="items-search-name" value="{$query}" />
    <input type="submit" value="Go" />
  </form>
</div>

<!--<div id="autocomplete_choices" class="autocomplete"></div>-->
  
  <script type="text/javascript">
    {literal}
    
    function getSelectionId(text, li) {
        var bits = li.id.split('-');
        window.location=sm_domain+'datamanager/openItem?item_id='+bits[1];
    }
    
    /* new Ajax.Autocompleter("items-search-name", "autocomplete_choices", "/ajax:datamanager/simpleItemTextSearch", {
        paramName: "query", 
        minChars: 2,
        delay: 50,
        width: 300,
        afterUpdateElement : getSelectionId
    }); */
    
    {/literal}
  </script>

<div id="options-view-chooser">
  Found {$num_items} {if $num_items != 1}{$model.plural_name}{else}{$model.name}{/if}. View as:
  <a href="#" onclick="return itemList.setView('list', 'item_list_style')">List</a> /
  <a href="#" onclick="return itemList.setView('grid', 'item_list_style')">Icons</a>
</div>

  <ul class="options-{$list_view}" id="options_list">
{foreach from=$items key="key" item="item"}
	<li ondblclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}'" class="item {if $item.public=='FALSE'}unpublished{else}published{/if} {if $item.is_archived=='1'}archived{else}current{/if}">
      <a href="#" class="option" id="item_{$item.id}" onclick="return itemList.setSelectedItem('{$item.id}', 'item', {literal}{{/literal}updateFields: {literal}{{/literal}item_name_field: '{$item.name|summary:"29"|escape:quotes|trim}', archive_action_name: '{if $item.is_archived}Unarchive{else}Archive{/if}'{literal}}{/literal}{literal}}{/literal});">
        {if $item.public == 'TRUE'}<img src="{$domain}Resources/Icons/item.png" border="0" class="grid" /><img border="0" src="/Resources/Icons/package_small.png" class="list" />{else}<img src="{$domain}Resources/Icons/item_grey.png" border="0" class="grid" /><img border="0" src="/Resources/Icons/package_small_grey.png" class="list" />{/if}{$item.name}</a></li>
{/foreach}
  </ul>


</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b><span class="item_name_field">{$model.name}</span></b></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/pencil.png"> <a href="{dud_link}" onclick="itemList.workWithItem('openItem');">Open</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/information.png"> <a href="{dud_link}" onclick="MODALS.load('datamanager/itemInfo?item_id='+itemList.lastItemId+'&amp;enable_ajax=1', '{$model.name} info');">{$model.name} info</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/lock_open.png"> <a href="{dud_link}" onclick="itemList.workWithItem('releaseItem');">Release</a></li>
  {if $has_metapages}<li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/eye.png"> <a href="{dud_link}" onclick="itemList.workWithItem('preview');">Preview</a></li>{/if}
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_lightning.png"> <a href="{dud_link}" onclick="itemList.workWithItem('publishItem');">Publish</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="itemList.workWithItem('unpublishItem');">Un-Publish</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/accept.png"> <a href="{dud_link}" onclick="itemList.workWithItem('addTodoItem');">Add new to-do</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="itemList.workWithItem('toggleItemArchived');"><span class="archive_action_name">Archive/Un-archive<span></a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_white_copy.png"> <a href="{dud_link}" onclick="itemList.workWithItem('duplicateItem');">Duplicate</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> <a href="{dud_link}" onclick="itemList.workWithItem('deleteItem', {ldelim}confirm: 'Are you sure you want to delete this {$model.name|lower} ?'{rdelim});">Delete</a></li>
{* <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="itemList.workWithItem('duplicateItem');">Duplicate</a></li> *}
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Model Options</b></li>
  {if $allow_create_new}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/add.png" /> Create a new {$model.name}</a></li>{/if}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseUserHeldItems?class_id={$model.id}';"><img border="0" src="{$domain}Resources/Icons/lock_open.png" /> Release all {$model.plural_name}</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="MODALS.load('datamanager/modelInfo?class_id={$model.id}', 'Model info');"><img border="0" src="{$domain}Resources/Icons/information.png" /> Model info</a></li>
  {if $can_edit_properties}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/tag_blue_edit.png" /> Edit model properties</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editItemClassPropertyOrder?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/arrow_switch.png" /> Edit property order</a></li>{/if}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/addSet?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png" /> Create a new set from this model</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/getItemClassSets?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/folder_old.png" /> View data sets for this model</a></li>
{* <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/importData?class_id={$itemBaseValues.itemclass_id}';"><img border="0" src="{$domain}Resources/Icons/page_code.png" /> Import data from CSV</a></li> *}
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
  {foreach from=$recent_items item="recent_item"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><img border="0" src="{$recent_item.small_icon}" /> {$recent_item.label|summary:"28"}</a></li>
  {/foreach}
</ul>

</div>