<div id="work-area">
  
  {load_interface file="edit_asset_tabs.tpl"}
  <h3>{$_l10n_action_strings.main_h3.before_filename} {$asset.label} {$_l10n_action_strings.main_h3.after_filename}</h3>
  
  {if $asset.deleted}<div class="warning">{$_l10n_action_strings.in_trash_warning.before_filetype} {$asset.type_info.label} {$_l10n_action_strings.in_trash_warning.after_filetype}</div>{/if}
  
  {if !$file_is_writable && $asset.type_info.editable}
    <div class="warning">This file is not currently writable by the web server, so it cannot be edited directly in Smartest.</div>
  {elseif !$dir_is_writable}
    <div class="warning">The directory where this file is stored is not currently writable by the web server, so this file cannot be edited directly in Smartest.</div>
  {/if}
  
  {* if $asset.is_too_large}<div class="warning">At {$asset.dimensions} pixels and {$asset.size}, this image seems quite large to use on a website. <a href="{$domain}assets/resizeImageAsset?asset_id={$asset.id}">Click here</a> to resize it.</div>{/if *}
  
  <div class="instruction">{$_l10n_action_strings.you_are_editing.before_file} {$asset.type_info.label}: <code>{$asset.type_info.storage.location}</code><strong><code>{$asset.url}</code></strong> {$_l10n_action_strings.you_are_editing.after_file} </div>
  
  <div id="groups" class="special-box">
    {$_l10n_strings.file_groups_box.label} {if count($groups)}{foreach from=$groups item="group"}<a href="{$domain}{$section}/browseAssetGroup?group_id={$group.id}">{$group.label}</a> (<a href="{$domain}{$section}/transferSingleAsset?asset_id={$asset.id}&amp;group_id={$group.id}&amp;transferAction=remove&amp;from=edit">{$_l10n_strings.file_groups_box.remove}</a>), {/foreach}{else}<em style="color:#666">{$_l10n_strings.file_groups_box.none}</em>{/if}
{if count($possible_groups)}
        <div>
          <form action="{$domain}{$section}/transferSingleAsset" method="post">
            
            <input type="hidden" name="asset_id" value="{$asset.id}" />
            <input type="hidden" name="transferAction" value="add" />
            <input type="hidden" name="from" value="edit" />
            
            Add this file to group:
            <select name="group_id">
{foreach from=$possible_groups item="possible_group"}
              <option value="{$possible_group.id}">{$possible_group.label}</option>
{/foreach}
            </select>
            <input type="submit" value="Go" />
          </form>
        </div>
{/if}
  </div>
  
{load_interface file=$formTemplateInclude}

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>File options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/assetInfo?asset_type={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/information.png" alt=""/> {$_l10n_strings.sidebar_options.file_info}</a></li>
    {if $allow_source_edit}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTextFragmentSource?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/page_edit.png" alt=""/> Edit This File's Source</a></li>{/if}
    {if $show_attachments}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/textFragmentElements?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/attach.png" alt=""/> Edit File Attachments</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/previewAsset?asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview This File</a></li>
    {if $show_publish}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishTextAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Publish This File</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset_type.label} files</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/assets'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" style="width:16px;height:16px" /> View all files by type</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><span style="color:#999">Recent {$asset.type_info.label|strtolower} files</span></li>
    {foreach from=$recent_assets item="recent_asset"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_asset.action_url}'"><img border="0" src="{$recent_asset.small_icon}" /> {$recent_asset.label|summary:"30"}</a></li>
    {/foreach}
  </ul>
</div>