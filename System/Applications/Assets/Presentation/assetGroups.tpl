<div id="work-area">
    
  <h3>{$_l10n_strings.title}</h3>
  
{if count($locations)}
  <div class="warning">
      <p>{$_l10n_strings.warnings.storage_locations_unwriteable}</p>
      <ul>
{foreach from=$locations item="l"}
        <li><code>{$l}</code></li>
{/foreach}        
      </ul>
  </div>
{/if}
  
  {load_interface file="file_browse_tabs.tpl"}
  
  <div class="instruction">{$_l10n_strings.groups.explanation}</div>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="group_id" id="item_id_input" value="" />
  </form>

  <ul class="{if count($groups) > 10}options-list{else}options-grid{/if}" id="{if count($groups) > 10}options_list{else}options_grid{/if}">
  {foreach from=$groups key="key" item="group"}
    <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/browseAssetGroup?group_id={$group.id}'">
  			<a class="option" id="item_{$group.id}" onclick="setSelectedItem('{$group.id}', 'fff');" >
  			  <img border="0" src="{$domain}Resources/Icons/folder.png">
  			  {$group.label}</a></li>
  {/foreach}
  </ul>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected Group</b></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editAssetGroup');}{/literal}"><img border="0" src="{$domain}Resources/Icons/information.png"> Group info</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editAssetGroupContents');}{/literal}"><img border="0" src="{$domain}Resources/Icons/folder_edit.png"> Edit contents</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('browseAssetGroup');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_magnify.png"> Browse contents</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('deleteAssetGroupConfirm');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_delete.png"> Delete group</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>{$_l10n_strings.general.general_options_label}</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addAsset'" class="right-nav-link"><img src="{$domain}Resources/Icons/add.png" border="0" alt="" /> {$_l10n_strings.sidebar_options.create_file}</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/newAssetGroup'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_add.png" border="0" alt="" /> {$_l10n_strings.sidebar_options.create_file_group}</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}assets/newAssetGroup?is_gallery=true&amp;asset_type=SM_ASSETCLASS_STATIC_IMAGE&amp;group_label={$_l10n_strings.general.new_image_gallery_name}'" class="right-nav-link"><img src="{$domain}Resources/Icons/photos.png" border="0" alt="" /> {$_l10n_strings.sidebar_options.create_image_gallery}</a></li>
  	<li class="permanent-action"><a href="#" onclick="window.location='{$domain}smartest/files/types'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" style="width:16px;height:16px" /> {$_l10n_strings.sidebar_options.view_by_type}</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><span style="color:#999">{$_l10n_strings.general.recently_edited_label}</span></li>
    {foreach from=$recent_assets item="recent_asset"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_asset.action_url}'"><img border="0" src="{$recent_asset.small_icon}" /> {$recent_asset.label|summary:"30"}</a></li>
    {/foreach}
  </ul>
  
</div>