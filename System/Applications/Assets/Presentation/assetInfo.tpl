<div id="work-area">
    {load_interface file="edit_asset_tabs.tpl"}
  <h3>File Info</h3>
  
  <form action="{$domain}assets/updateAssetInfo" method="post">
    
    <input type="hidden" name="asset_id" value="{$asset.id}" />
    
    <table cellspacing="1" cellpadding="2" border="0" style="width:100%;background-color:#ccc;margin-top:10px">
      <tr>
        <td style="width:150px;background-color:#fff" valign="top">File name:</td>
        <td style="background-color:#fff" valign="top">
          <input type="text" name="asset_label" value="{$asset.label}" style="width:250px" maxlength="64" />
        </td>
      </tr>
      <tr>
        <td style="width:150px;background-color:#fff" valign="top">Name on disk:</td>
        <td style="background-color:#fff" valign="top">{$asset.full_path}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Size:</td>
        <td style="background-color:#fff" valign="top">{$asset.size}{if $asset.is_image}, ({$asset.width} x {$asset.height} pixels){/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Type:</td>
        <td style="background-color:#fff" valign="top"><a href="{$domain}{$section}/getAssetTypeMembers?asset_type={$asset.type}">{$asset.type_info.label}</a> <span style="color:#666">({$asset.type})</span></td>
      </tr>
      {if $asset.created > 0}
      <tr>
        <td style="background-color:#fff" valign="top">Created:</td>
        <td style="background-color:#fff" valign="top">{$asset.created|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
      </tr>
      {/if}
      {if $asset.modified > 0}
      <tr>
        <td style="background-color:#fff" valign="top">Modified:</td>
        <td style="background-color:#fff" valign="top">{$asset.modified|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
      </tr>
      {/if}
      <tr>
        <td style="background-color:#fff" valign="top">Owner:</td>
        <td style="background-color:#fff" valign="top">
          <select name="asset_user_id">
  {foreach from=$potential_owners item="p_owner"}
            <option value="{$p_owner.id}"{if $asset.owner.id == $p_owner.id} selected="selected"{/if}>{$p_owner.fullname} ({$p_owner.id})</option>
  {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Original site:</td>
        <td style="background-color:#fff" valign="top">{$asset.site.label}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Shared with other sites:</td>
        <td style="background-color:#fff" valign="top"><input type="checkbox" name="asset_shared"{if $asset.shared==1} checked="checked"{/if} /></td>
      </tr>
    </table>
  
    <div class="buttons-bar" style="margin-top:5px">
      <input type="submit" value="Save changes" />
    </div>

  </form>
  
  <div class="special-box">
    <div class="special-box-key">File groups:</div>
    {if count($groups)}{foreach from=$groups item="group"}<a href="{$domain}{$section}/browseAssetGroup?group_id={$group.id}">{$group.label}</a> (<a href="{$domain}{$section}/transferSingleAsset?asset_id={$asset.id}&amp;group_id={$group.id}&amp;transferAction=remove">remove</a>), {/foreach}{else}<em style="color:#666">None</em>{/if}
{if count($possible_groups)}
      <div>
        <form action="{$domain}{$section}/transferSingleAsset" method="post">
          <input type="hidden" name="asset_id" value="{$asset.id}" />
          <input type="hidden" name="transferAction" value="add" />
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
      
{*      <h4 style="margin-top:15px">Usage of this file</h4> *}
      
      <h4 style="margin-top:15px">Comments on this file</h4>
      
      <ul style="padding:0px;margin:0px;list-style-type:none">
  {foreach from=$comments item="comment"}
        <li style="padding:5px;background-color:#{cycle values="fff,ddd"}">
          <b>{$comment.user.full_name}</b>, {$comment.posted_at|date_format:"%A %e %B, %Y"}<br />
          <p>{$comment.content}</p>
        </li>
  {foreachelse}
        <li style="padding:5px;"><div class="instruction">No comments yet</div></li>
  {/foreach}
      </ul>

      <div class="instruction">Leave a comment</div>

      <form action="{$domain}{$section}/attachCommentToAsset" method="post">
        <input type="hidden" name="asset_id" value="{$asset.id}" />
        <textarea name="comment_content" style="width:500px;height:90px"></textarea><br />
        <input type="submit" value="Save" />
      </form>
  
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