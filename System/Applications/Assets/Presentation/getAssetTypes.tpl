<div id="work-area">

<h3>Media Assets</h3>
<a name="top"></a>
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
  <li><b>Selected Asset Type</b></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('getAssetTypeMembers'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Show me all of this type</a></li>
	<li class="permanent-action"><a href="#" onclick="workWithItem('addAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add another one of this type</a></li>
</ul>

</div>