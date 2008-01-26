<script language="javascript" type="text/javascript">


</script>

<div id="work-area">

<h3>{$type_label} Files</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assettype_code" value="{$type_code}" />
 <input type="hidden" name="asset_id" id="item_id_input" value="" />
</form>

<div id="options-view-chooser">
Found {$num_assets} file{if $num_assets != 1}s{/if}. View as:
<a href="#" onclick="setView('list', 'options_grid')">List</a> /
<a href="#" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-list" style="margin-top:0px" id="options_grid">
{foreach from=$assets item="asset"}

<li>
    <a href="#" class="option" id="item_{$asset.asset_id}" onclick="setSelectedItem('{$asset.asset_id}', 'Template', '{$sidebartype}');" >

{if in_array($type_code, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$domain}Resources/Images/ImageAssetThumbnails/{$asset.asset_url}" />{$asset.asset_stringid}</a>
{else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" />{$asset.asset_stringid}</a>
{/if}

</li>

{/foreach}
</ul>
{if $error}{$error}{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="noneditableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('deleteAssetConfirm'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete This File</a></li>
	{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('duplicateAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Duplicate This File</a></li> *}
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('downloadAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Download This File</a></li>
</ul>

<ul class="actions-list" id="editableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('editAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit This File</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('deleteAssetConfirm'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete This File</a></li>
	{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('duplicateAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Duplicate This File</a></li> *}
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('downloadAsset'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Download This File</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Media Asset Options</b></li>
	<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addAsset?asset_type={$type_code}'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt="" /> Add a new file of this type</a></li>
</ul>

</div>