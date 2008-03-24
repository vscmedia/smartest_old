<div id="work-area">
  
  <h3>Attachments</h3>
  
  {foreach from=$attachments item="attachment"}
  <div style="padding:5px;background-color:#{cycle values="ddd,fff"}">
  <b>Name:</b>&nbsp;{$attachment._name}<br />
  <b>Attached File:</b>&nbsp;{$attachment.asset.url}<br />
  <b>Caption:</b>&nbsp;{$attachment.caption}<br />
  <b>Align:</b>&nbsp;{$attachment.alignment}<br />
  <input type="button" value="Edit..." onclick="window.location='{$domain}{$section}/defineAttachment?attachment={$attachment._name}&amp;asset_id={$asset.id}'" />
  </div>
  {/foreach}
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/>View all {$asset_type.label} files</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/>Edit in Rich-Text Editor</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTextFragmentSource?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_edit.png" alt=""/>Edit File Source</a></li>
  </ul>
</div>