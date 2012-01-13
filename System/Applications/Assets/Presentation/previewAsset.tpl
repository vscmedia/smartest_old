<div id="work-area">
    {load_interface file="edit_asset_tabs.tpl"}
 <h3>Preview of file &lsquo;{$asset.label}&rsquo;</h3>
 <div class="instruction">The file will look something like this:</div>
 <div id="html-preview">
  {$html}
  <div class="breaker"></div>
 </div>
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/assetInfo?asset_type={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/information.png" alt=""/> About this file</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> Edit This File</a></li>
    {if $allow_source_edit}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTextFragmentSource?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/page_edit.png" alt=""/> Edit This File's Source</a></li>{/if}
    {if $show_attachments}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/textFragmentElements?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/attach.png" alt=""/> Edit File Attachments</a></li>{/if}
    {if $allow_approve}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/approveAsset?asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/tick.png" alt=""/> Approve this file</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTodoItem?asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/tick.png" alt=""/> Add a new to-do for this file</a></li>
    {if $show_publish}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishTextAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Publish This Text</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset_type.label} files</a></li>
  </ul>
</div>