<script type="text/javascript">
  var assets = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
</script>

<div id="work-area">

{load_interface file="edit_filegroup_tabs.tpl"}

<h3>Files in group "{$group.label}"</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="asset_id" id="item_id_input" value="" />
</form>

<div class="special-box">
  <form id="mode-form" method="get" action="">
    <input type="hidden" name="group_id" value="{$group.id}" />
    Show: <select name="mode" onchange="$('mode-form').submit();">
      <option value="1"{if $mode == 1} selected="selected"{/if}>Files not in archive</option>
      <option value="0"{if $mode == 0} selected="selected"{/if}>All files in this group</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>Archived files</option>
    </select>
  </form>
</div>

<div id="options-view-chooser">
{$num_assets} file{if $num_assets != 1}s{/if}. View as:
<a href="#" onclick="return assets.setView('list', 'asset_list_style')">List</a> /
<a href="#" onclick="return assets.setView('grid', 'asset_list_style')">Icons</a>
</div>

<ul class="options-{$list_view}" style="margin-top:0px" id="options_grid">
{foreach from=$assets item="asset"}

<li>
    <a href="#" class="option" id="editableasset_{$asset.id}" onclick="return assets.setSelectedItem('{$asset.id}', 'editableasset');" ondblclick="assets.workWithItem('editAsset')" >

{if in_array($asset.type, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$domain}Resources/Images/ImageAssetThumbnails/{$asset.url}" class="grid" />
{else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" class="grid" />
{/if}
<img border="0" src="{$asset.small_icon}" class="list" />

{$asset.label}</a>

</li>

{/foreach}
</ul>
{if $error}{$error}{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="noneditableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About This File...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview This File</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" alt="" /> Archive/unarchive this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('deleteAssetConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete This File</a></li>
	{* <li class="permanent-action"><a href="#" onclick="assets.workWithItem('duplicateAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Duplicate This File</a></li> *}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('downloadAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Download This File</a></li>
</ul>

<ul class="actions-list" id="editableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
  <li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About This File...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit This File</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview This File</a></li>
	{if $allow_source_edit}<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editTextFragmentSource');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit File Source</a></li>{/if}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" alt="" /> Archive/unarchive this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('deleteAssetConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete This File</a></li>
	{* <li class="permanent-action"><a href="#" onclick="assets.workWithItem('duplicateAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_copy.png" border="0" alt="" /> Duplicate This File</a></li> *}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('downloadAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/disk.png" border="0" alt="" /> Download This File</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>File group options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/file/new?group_id={$group.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/add.png" border="0" alt="" /> Add a file to this group</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAssetGroupContents?group_id={$group.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_edit.png" border="0" alt="" /> Edit this group</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/newAssetGroup'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_add.png" border="0" alt="" /> Create a new file group</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Other options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/files/groups'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" /> View all file groups</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/files/types'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" /> View all files by type</a></li>
</ul>

</div>