<div id="work-area">
  
  <h3>Attachments</h3>
  
  <div class="special-box">The following attachment tags were recognized in this text:</div>
  
  {foreach from=$attachments item="attachment"}
  <div style="padding:5px;background-color:#{cycle values="ddd,fff"}">
  <b>Name:</b>&nbsp;{$attachment.name}<br />
  <b>Attached File:</b>&nbsp;{if $attachment.asset.id}{$attachment.asset.url}{else}<em style="color:#999">None yet</em>{/if}<br />
  <b>Caption:</b>&nbsp;{$attachment.caption}<br />
  <b>Align:</b>&nbsp;{$attachment.alignment}
  <div style="margin-top:10px"><input type="button" value="{if $attachment.asset.id}Edit...{else}Attach file...{/if}" onclick="window.location='{$domain}{$section}/defineAttachment?attachment={$attachment.name}&amp;asset_id={$asset.id}'" /></div>
  </div>
  {foreachelse}
  <div class="special-box">There are no attachment tags in this text yet. <a href="{$domain}{$section}/editTextFragmentSource?asset_id={$asset.id}">Click here</a> to add some.</div>
  {/foreach}
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/previewAsset?asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview this file</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset_type.label} files</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> Edit in rich-text editor</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTextFragmentSource?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/page_edit.png" alt=""/> Edit file source</a></li>
  </ul>
</div>