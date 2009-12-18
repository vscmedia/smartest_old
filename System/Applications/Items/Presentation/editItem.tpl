<div id="work-area">

{load_interface file="edit_tabs.tpl"}

<h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}smartest/models">Models</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}">{$item._model.plural_name}</a> &gt; Edit {$item._model.name}</h3>

{if $item.deleted}<div class="warning">Warning: This {$item._model.name|strtolower} is currently in the trash.</div>{/if}

<div id="instruction">You are editing the draft property values of this item</div>

<div id="sets" class="special-box">
     Sets: {if count($sets)}{foreach from=$sets item="set"}<a href="{$domain}sets/previewSet?set_id={$set.id}">{$set.label}</a> (<a href="{$domain}sets/transferSingleItem?item_id={$item.id}&amp;set_id={$set.id}&amp;transferAction=remove">remove</a>), {/foreach}{else}<em style="color:#666">None</em>{/if}
 {if count($possible_sets)}
         <div>
           <form action="{$domain}sets/transferSingleItem" method="post">
             <input type="hidden" name="item_id" value="{$item.id}" />
             <input type="hidden" name="transferAction" value="add" />
             Add this item to set:
             <select name="set_id">
 {foreach from=$possible_sets item="possible_set"}
               <option value="{$possible_set.id}">{$possible_set.label}</option>
 {/foreach}
             </select>
             <input type="submit" value="Go" />
           </form>
         </div>
 {/if}
</div>

<form action="{$domain}{$section}/updateItem" enctype="multipart/form-data" method="post">

<input type="hidden" name="class_id" value="{$item._model.id}" />
<input type="hidden" name="item_id" value="{$item.id}" />

{if $smarty.get.from}<input type="hidden" name="from" value="{$smarty.get.from}" />{/if}

<div class="edit-form-row">
  <div class="form-section-label">{$item._model.name} name</div>
  <input type="text" name="item_name" value="{$item.name|escape_double_quotes}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">{$item._model.name} short name (Used in links and URLS)</div>
  {if $allow_edit_item_slug}<input type="text" name="item_slug" value="{$item.slug}" /><span class="form-hint">Numbers, lowercase letters and hyphens only, please</span>{else}{$item.slug}{/if}
</div>

<div class="edit-form-row">
  <div class="form-section-label">Status</div>
  {if $item.public == "TRUE"}
    Live <input type="button" value="Re-Publish" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}'" />&nbsp;<input type="button" value="Un-Publish" onclick="window.location='{$domain}{$section}/unpublishItem?item_id={$item.id}'" />
  {else}
    Not Published <input type="button" value="Publish" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}'" />
  {/if}
</div>

{foreach from=$item._editable_properties key="pid" item="property"}
<div class="edit-form-row">
  {item_field property=$property value=$item[$pid]}
</div>
{/foreach}

<div class="edit-form-row">
  <div class="form-section-label">Language</div>
  <select name="item_language">
{foreach from=$_languages item="lang" key="langcode"}
    <option value="{$langcode}"{if $item.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
{/foreach}
  </select>
</div>

{if count($metapages)}
<div class="edit-form-row">
  <div class="form-section-label">Meta-Page</div>
  <select name="item_metapage_id">
    {if $item._model.default_metapage_id}<option value="0">Model Default</option>{/if}
    {foreach from=$metapages item="page"}
    <option value="{$page.id}"{if $item.metapage_id == $page.id} selected="selected"{/if}>{$page.title}</option>
    {/foreach}
  </select>
</div>
{else}
<div class="warning">Warning: No meta-pages have been created for displaying {$item._model.plural_name|strtolower}. {$item._model.plural_name} will only be visible in lists on other pages.</div>
{/if}

<div class="edit-form-row">
  <div class="form-section-label">Search Terms</div>
  <textarea name="item_search_field" rows="3" cols="20" style="width:350px;height:60px">{$item.search_field}</textarea>
</div>

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}';" value="Cancel" />
    <input type="submit" value="Save Changes" />
  </div>
</div>

</form>

</div>

<div id="actions-area">

  <ul class="actions-list" id="non-specific-actions">
    <li><b>This {$item._model.name}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/itemInfo?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" />&nbsp;About this {$item._model.name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/lock_open.png" border="0" />&nbsp;Release for others to edit</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/approveItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Approve changes</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTodoItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Assign To-do</a></li>
    {if $default_metapage_id}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}websitemanager/preview?page_id={$default_metapage_id}&amp;item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/page.png" border="0" />&nbsp;Preview it</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" />&nbsp;Publish it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/toggleItemArchived?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" />&nbsp;{if $item.is_archived}Un-archive this {$item._model.name}{else}Archive this {$item._model.name}{/if}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item.itemclass_id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Finish editing for now</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>{$item._model.name} Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}';" class="right-nav-link">Back to {$item._model.plural_name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$item._model.id}';" class="right-nav-link">New {$item._model.name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/addSet?class_id={$item._model.id}';" class="right-nav-link">Create a new set of {$item._model.plural_name}</a></li>
  </ul>
  
</div>
