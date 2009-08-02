<div id="work-area">

<h3>{$type_label} Files</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assettype_code" value="{$type_code}" />
  <input type="hidden" name="asset_id" id="item_id_input" value="" />
</form>

{if $num_assets < 1 && $mode != 2}
<div class="special-box">There are no {$type_label|strtolower} files yet. <a href="{$domain}{$section}/addAsset?asset_type={$type_code}">Click here</a> to add one.</div>
{else}
<div class="special-box">
  <form id="mode-form" method="get" action="">
    <input type="hidden" name="asset_type" value="{$type_code}" />
    Show: <select name="mode" onchange="$('mode-form').submit();">
      <option value="1"{if $mode == 1} selected="selected"{/if}>{$type_label} files not in archive</option>
      <option value="0"{if $mode == 0} selected="selected"{/if}>All {$type_label} files</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>Archived {$type_label} files</option>
    </select>
  </form>
</div>

<div id="options-view-chooser">
Found {$num_assets} file{if $num_assets != 1}s{/if}. View as:
<a href="{dud_link}" onclick="setView('list', 'options_grid')">List</a> /
<a href="{dud_link}" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-grid" style="margin-top:0px" id="options_grid">
{foreach from=$assets item="asset"}

<li>
    <a href="{dud_link}" class="option" id="item_{$asset.id}" onclick="setSelectedItem('{$asset.id}', 'Template', '{$sidebartype}');" >

{if in_array($type_code, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$domain}Resources/Images/ImageAssetThumbnails/{$asset.url}" />{$asset.url}</a>
{else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" />{$asset.stringid}</a>
{/if}

</li>

{/foreach}
</ul>
{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="noneditableasset-specific-actions" style="display:none">
  <li><b>Selected file</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('assetInfo'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About this file...</a></li>
	<li class="permanent-action"><a href="{dud_link}href="{dud_link}"" onclick="{literal}if(selectedPage){ workWithItem('addTodoItem'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('previewAsset'); }{/literal}"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview this file</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('toggleAssetArchived'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" alt="" /> Archive/unarchive this file...</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('deleteAssetConfirm'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete this file</a></li>
	{* <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('duplicateAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Duplicate this file</a></li> *}
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('downloadAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Download this file</a></li>
</ul>

<ul class="actions-list" id="editableasset-specific-actions" style="display:none">
  <li><b>Selected file</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('assetInfo'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About this file...</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('editAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit this file</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('previewAsset'); }{/literal}"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview this file</a></li>
	{if $allow_source_edit}<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('editTextFragmentSource'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit file source</a></li>{/if}
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('addTodoItem'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('toggleAssetArchived'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" alt="" /> Archive/unarchive this file...</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('deleteAssetConfirm'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete this file</a></li>
	{* <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('duplicateAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_copy.png" border="0" alt="" /> Duplicate this file</a></li> *}
	<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage){ workWithItem('downloadAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/disk.png" border="0" alt="" /> Download this file</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Repository options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addAsset?asset_type={$type_code}'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt="" /> Add a new file of this type</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/newAssetGroup?filter_type={$type_code}'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt="" /> Add a new group from these files</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/assets'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" style="width:16px;height:16px" /> View all files by type</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recent {$type_label|strtolower} files</span></li>
  {foreach from=$recent_assets item="recent_asset"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_asset.action_url}'"><img border="0" src="{$recent_asset.small_icon}" /> {$recent_asset.label|summary:"30"}</a></li>
  {/foreach}
</ul>

</div>