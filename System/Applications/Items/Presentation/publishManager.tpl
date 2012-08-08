<script type="text/javascript">
  var itemList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_list');
</script>

<div id="work-area">

<h3>Publish {$model.plural_name}</h3>

<div class="instruction">This tool will only make items public and display the most recent values. You will need to empty the pages cache after you have published to ensure items display correctly on the site. Use this tool only as a last-resort.</div>

<form id="pageViewForm" method="get" action="">
  <div class="special-box">Action:
    <select name="publish_action">
      <option value="ALL">Publish all {$model.plural_name} below</option>
      <option value="PUBLISHED">Re-publish {$model.plural_name} already published</option>
      <option value="HIDDEN">Publish only {$model.plural_name} that are not published</option>
    </select>
    <input type="button" id="submit-button" value="Go" />
  </div>
  <input type="hidden" name="item_id" id="item_id_input" value="" />
  <input type="hidden" name="class_id" value="{$model.id}" />
</form>

<script type="text/javascript">{literal}$('submit-button').observe('click', function(){$('pageViewForm').action='publishManagerAction'; $('pageViewForm').submit();});{/literal}</script>

<div id="options-view-chooser">
Found {$num_items} {if $num_items != 1}{$model.plural_name}{else}{$model.name}{/if}. View as:
<a href="#" onclick="return itemList.setView('list', 'item_list_style')">List</a> /
<a href="#" onclick="return itemList.setView('grid', 'item_list_style')">Icons</a>
</div>

    <ul class="options-{$list_view}" id="options_list">
{foreach from=$items key="key" item="item"}
	
    <li ondblclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}'" class="item {if $item.public=='FALSE'}unpublished{else}published{/if} {if $item.is_archived=='1'}archived{else}current{/if}">
      <a href="#" class="option" id="item_{$item.id}" onclick="return itemList.setSelectedItem('{$item.id}', 'item', {literal}{{/literal}updateFields: {literal}{{/literal}item_name_field: '{$item.name|summary:"29"|escape:quotes|trim}', archive_action_name: '{if $item.is_archived}Unarchive{else}Archive{/if}'{literal}}{/literal}{literal}}{/literal});">
        {if $item.public == 'TRUE'}<img src="{$domain}Resources/Icons/item.png" border="0" class="grid" /><img border="0" src="/Resources/Icons/package_small.png" class="list" />{else}<img src="{$domain}Resources/Icons/item_grey.png" border="0" class="grid" /><img border="0" src="/Resources/Icons/package_small_grey.png" class="list" />{/if}{$item.name}</a>{* if $item.public=='FALSE'}&nbsp;(hidden){/if *}</li>

{/foreach}
  
  {* <object id="head-logo" data="{$domain}Resources/Icons/item.svg" type="image/svg+xml"> *}
  </ul>


</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b><span class="item_name_field">{$model.name}</span></b></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/pencil.png"> <a href="{dud_link}" onclick="itemList.workWithItem('openItem');">Open</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/information.png"> <a href="{dud_link}" onclick="MODALS.load('datamanager/itemInfo?item_id='+itemList.lastItemId, '{$model.name} info');">{$model.name} info</a></li>
  {if $has_metapages}<li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/eye.png"> <a href="{dud_link}" onclick="itemList.workWithItem('preview');">Preview</a></li>{/if}
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_lightning.png"> <a href="{dud_link}" onclick="itemList.workWithItem('publishItem');">Publish</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/accept.png"> <a href="{dud_link}" onclick="itemList.workWithItem('addTodoItem');">Add new to-do</a></li>
  <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> <a href="{dud_link}" onclick="itemList.workWithItem('deleteItem', {ldelim}confirm: 'Are you sure you want to delete this {$model.name|lower} ?'{rdelim});">Delete</a></li>
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