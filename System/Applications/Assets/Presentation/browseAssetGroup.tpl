<script type="text/javascript">
  var assets = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
</script>

<div id="work-area">

{load_interface file="edit_filegroup_tabs.tpl"}

<h3>{if $group.is_gallery}{$_l10n_strings.groups.gallery_files}{else}{$_l10n_strings.groups.group_files}{/if}"{$group.label}"</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="asset_id" id="item_id_input" value="" />
  <input type="hidden" name="group_id" value="{$group.id}" />
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

{if $group.is_gallery}<div class="instruction">Drag and drop files in this gallery to change their order. The new order is saved automatically.</div>{/if}

<div id="options-view-chooser">
{$num_assets} file{if $num_assets != 1}s{/if}. View as:
<a href="#display-as-list" onclick="return assets.setView('list', 'asset_list_style')">List</a> /
<a href="#display-as-icons" onclick="return assets.setView('grid', 'asset_list_style')">Icons</a>
</div>

<ul class="options-{$list_view}{if $contact_sheet_view} images{/if}{if $group.is_gallery} reorderable{/if}" style="margin-top:0px" id="options_grid">
{foreach from=$assets item="asset"}

<li id="file_{$asset.id}">
    <a href="#select-file" class="option" id="editableasset_{$asset.id}" onclick="return assets.setSelectedItem('{$asset.id}', 'editableasset');" ondblclick="assets.workWithItem('editAsset')" >

{if in_array($asset.type, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$asset.image._ui_preview.web_path}" class="grid" />
{else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" class="grid" />
{/if}
<img border="0" src="{$asset.small_icon}" class="list" />
<span class="asset label">{$asset.label}</span></a>
</li>
{/foreach}
</ul>

{if $error}{$error}{/if}

{if $group.is_gallery}

<script type="text/javascript" src="/Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
<script type="text/javascript">

var url = sm_domain+'ajax:assets/updateGalleryOrder';
var groupId = {$group.id};
{literal}
var IDs;
var IDs_string;

var itemsList = Sortable.create('options_grid', {
      
      onUpdate: function(){
          
        IDs = Sortable.sequence('options_grid');
        IDs_string = IDs.join(',');
        
        new Ajax.Request(url, {
          method: 'get',
          parameters: {group_id: groupId, new_order: IDs_string},
          onSuccess: function(transport) {
            
          }
        });
      },
      
      constraint: false,
      scroll: window,
      scrollSensitivity: 35
      
  });
{/literal}
</script>
{/if}
</div>

<div id="actions-area">

<ul class="actions-list" id="noneditableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About This File...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview This File</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" alt="" /> Archive/unarchive this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('removeAssetFromGroup');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Remove this file from {if $group.is_gallery}gallery{else}group{/if}</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('downloadAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Download This File</a></li>
</ul>

<ul class="actions-list" id="editableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
  <li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About This File...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit This File</a></li>
	{if $group.is_gallery}<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editAssetGalleryMembership');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_white_edit.png" border="0" alt=""> Edit gallery membership</a></li>{/if}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview This File</a></li>
	{if $allow_source_edit}<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editTextFragmentSource');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit File Source</a></li>{/if}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" alt="" /> Archive/unarchive this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('removeAssetFromGroup');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Remove this file from {if $group.is_gallery}gallery{else}group{/if}</a></li>
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