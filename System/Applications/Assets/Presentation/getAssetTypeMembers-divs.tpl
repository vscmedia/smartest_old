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

<div class="options-grid" style="margin-top:0px" id="options_grid">
{foreach from=$assets item="asset"}

<div class="option-div">
    <a href="{dud_link}" class="option" id="item_{$asset.asset_id}" onclick="setSelectedItem('{$asset.asset_id}', 'Template', '{$sidebartype}');" >

{if in_array($type_code, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$domain}Resources/Images/ImageAssetThumbnails/{$asset.asset_url}" />{$asset.asset_stringid}
{else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" />{$asset.asset_stringid}
{/if}</a>

</div>

{/foreach}

</div>
{if $error}{$error}{/if}

</div>