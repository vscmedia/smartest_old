<div id="work-area">

<h3>Your Templates</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" id="item_id_input" name="type" value="" />
</form>

<div class="instruction">There are five different kinds of template. Please select which type you'd like to work with.</div>

<ul class="options-grid-no-scroll" style="margin-top:0px">
  {foreach from=$types item="assetType"}
    <li ondblclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$assetType.id}'">
      <a href="javascript:nothing();" id="item_{$assetType.id}" class="option" onclick="setSelectedItem('{$assetType.id}', '{$assetType.label|escape:quotes}');">
        <img border="0" src="{$domain}Resources/Icons/folder.png" />{$assetType.label}s</a></li>{* $assetType.icon *}
  {/foreach}
</ul><br clear="all" />

{* <ul class="basic-list">
  <li><a href="{$domain}templates/containerTemplates">Container Templates</a></li>
  <li><a href="{$domain}templates/masterTemplates">Master Templates</a></li>
  <li><a href="{$domain}templates/listItemTemplates">ListItem Templates</a></li>
</ul> *}

</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><strong>Select template type</strong></li>
    <li class="permament-action"><a href="#" onclick="workWithItem('listByType')" class="right-nav-link"><img src="{$domain}Resources/Icons/page_white_stack.png" /> Browse these templates</a></li>
    <li class="permament-action"><a href="#" onclick="workWithItem('addTemplate')" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" /> Add a template of this type</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><strong>Options</strong></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}templates/import'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_magnify.png" /> Detect new templates</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}templates/addTemplate'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" /> Add a new template</a></li>
  </ul>
  
</div>