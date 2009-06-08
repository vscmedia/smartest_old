<div id="work-area">

{load_interface file="edit_tabs.tpl"}

<h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}smartest/models">Models</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}">{$item._model.plural_name}</a> &gt; Edit {$item._model.name}</h3>

<div id="instruction">You are editing the draft property values of this item</div>

<form action="{$domain}{$section}/updateItem" enctype="multipart/form-data" method="post">

<input type="hidden" name="class_id" value="{$item._model.id}" />
<input type="hidden" name="item_id" value="{$item.id}" />

{if $smarty.get.from}<input type="hidden" name="from" value="{$smarty.get.from}"
/>{/if}

{if $item.deleted}<div class="warning">Warning: This {$item._model.name|strtolower} is currently in the trash.</div>{/if}

<div class="edit-form-row">
  <div class="form-section-label">{$item._model.name} name</div>
  <input type="text" name="item_name" value="{$item.name}" style="width:250px" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">{$item._model.name} short name (Used in URLS)</div>
  <input type="text" name="item_slug" value="{$item.slug}" style="width:250px" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Status</div>
  {if $item.public == "TRUE"}
    Live <input type="button" value="Re-Publish" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}'" />&nbsp;<input type="button" value="Un-Publish" onclick="window.location='{$domain}{$section}/unpublishItem?item_id={$item.id}'" />
  {else}
    Not Published <input type="button" value="Publish" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}'" />
  {/if}
</div>

<div class="edit-form-row">
  <div class="form-section-label">Meta-Page</div>
  <select name="item_metapage_id">
    {if $item._model.default_metapage_id}<option value="0">Model Default</option>{/if}
    {foreach from=$metapages item="page"}
    <option value="{$page.id}"{if $item.metapage_id == $page.id} selected="selected"{/if}>{$page.title}</option>
    {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Search Terms</div>
  <textarea name="item_search_field" rows="3" cols="20" style="width:350px;height:60px">{$item.search_field}</textarea>
</div>

{foreach from=$item._properties key="pid" item="property"}

<div class="edit-form-row">
  {item_field property=$property value=$item[$pid]}
</div>

{/foreach}

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
  </ul>
  
</div>
