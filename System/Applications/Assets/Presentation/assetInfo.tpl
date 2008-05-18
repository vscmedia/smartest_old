<div id="work-area">
  <h3>File Info</h3>
  
  <table cellspacing="1" cellpadding="2" border="0" style="width:100%;background-color:#ccc;margin-top:10px">
    <tr>
      <td style="width:150px;background-color:#fff" valign="top">Name on disk:</td>
      <td style="background-color:#fff" valign="top">{$asset.full_path}</td>
    </tr>
    <tr>
      <td style="background-color:#fff" valign="top">Size:</td>
      <td style="background-color:#fff" valign="top">{$asset.size}</td>
    </tr>
    <tr>
      <td style="background-color:#fff" valign="top">Type:</td>
      <td style="background-color:#fff" valign="top">{$asset.type_info.label} <span style="color:#666">({$asset.type})</span></td>
    </tr>
    {if $asset.created > 0}
    <tr>
      <td style="background-color:#fff" valign="top">Created:</td>
      <td style="background-color:#fff" valign="top">{$asset.created}</span></td>
    </tr>
    {/if}
    {if $asset.modified > 0}
    <tr>
      <td style="background-color:#fff" valign="top">Modified:</td>
      <td style="background-color:#fff" valign="top">{$asset.modified}</span></td>
    </tr>
    {/if}
    <tr>
      <td style="background-color:#fff" valign="top">Owner:</td>
      <td style="background-color:#fff" valign="top">{$asset.owner.firstname} {$asset.owner.lastname} <span style="color:#666">({$asset.owner.id})</span></td>
    </tr>
    
  </table>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?asset_type={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> Edit this file</a></li>
    {if $allow_source_edit}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTextFragmentSource?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/page_edit.png" alt=""/> Edit This File's Source</a></li>{/if}
    {if $show_attachments}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/textFragmentElements?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/attach.png" alt=""/> Edit File Attachments</a></li>{/if}
    {if $show_publish}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishTextAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Publish This Text</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset.type_info.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset.type_info.label} files</a></li>
  </ul>
</div>