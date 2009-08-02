<div id="work-area">

<h3>Files Repository</h3>

<div class="text" style="margin-bottom:10px">Double click an icon below to see assets in that category.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" id="item_id_input" name="asset_type" value="" />
</form>
  
{foreach from=$assetTypeCats item="assetTypeCategory" key="category_name"}

<div class="form-section-label">{$category_name}</div>

<ul class="options-grid-no-scroll" style="margin-top:0px">

{foreach from=$assetTypeCategory item="assetType"}
  <li ondblclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$assetType.id}'">
    <a href="javascript:nothing();" id="item_{$assetType.id}" class="option" onclick="setSelectedItem('{$assetType.id}', '{$assetType.label|escape:quotes}');">
      <img border="0" src="{$domain}Resources/Icons/folder.png" />{$assetType.label}</a></li>{* $assetType.icon *}
{/foreach}

</ul><br clear="all" />
{/foreach}

</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected File Type</b></li>
  	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('getAssetTypeMembers'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Show me all of this type</a></li>
  	<li class="permanent-action"><a href="#" onclick="workWithItem('addAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add another one of this type</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Repository Options</b></li>
  	<li class="permanent-action"><a href="#" onclick="window.location='{$domain}assets/detectNewUploads'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_magnify.png" border="0" alt="" /> Detect newly uploaded files</a></li>
  	<li class="permanent-action"><a href="#" onclick="window.location='{$domain}assets/assetGroups'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" border="0" alt="" style="width:16px;height:16px" /> View file groups</a></li>
  	<li class="permanent-action"><a href="#" onclick="window.location='{$domain}assets/newAssetGroup'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt="" /> Create a new file group</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><span style="color:#999">Recently edited files</span></li>
    {foreach from=$recent_assets item="recent_asset"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_asset.action_url}'"><img border="0" src="{$recent_asset.small_icon}" /> {$recent_asset.label|summary:"30"}</a></li>
    {/foreach}
  </ul>

</div>