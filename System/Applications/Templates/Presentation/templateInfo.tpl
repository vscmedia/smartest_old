<div id="work-area">
    
  {load_interface file="template_edit_tabs.tpl"}
    
  <h3>Template info</h3>
  
  <table cellspacing="1" cellpadding="2" border="0" style="width:100%;background-color:#ccc;margin-top:10px">
    <tr>
      <td style="width:150px;background-color:#fff" valign="top">Name on disk:</td>
      <td style="background-color:#fff" valign="top"><code>{$template.full_path}</code></td>
    </tr>
    <tr>
      <td style="background-color:#fff" valign="top">Size:</td>
      <td style="background-color:#fff" valign="top">{$template.size}</td>
    </tr>
    <tr>
      <td style="background-color:#fff" valign="top">Type:</td>
      <td style="background-color:#fff" valign="top">{$template.type_info.label} <span style="color:#666">({$template.type})</span></td>
    </tr>
    {if $template.created > 0}
    <tr>
      <td style="background-color:#fff" valign="top">Created:</td>
      <td style="background-color:#fff" valign="top">{$template.created|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
    </tr>
    {/if}
    {if $template.modified > 0}
    <tr>
      <td style="background-color:#fff" valign="top">Modified:</td>
      <td style="background-color:#fff" valign="top">{$template.modified|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
    </tr>
    {/if}
    <tr>
      <td style="background-color:#fff" valign="top">Owner:</td>
      <td style="background-color:#fff" valign="top">{$template.owner.firstname} {$template.owner.lastname} <span style="color:#666">({$template.owner.id})</span></td>
    </tr>
{*    <tr>
      <td style="background-color:#fff" valign="top">File groups:</td>
      <td style="background-color:#fff" valign="top">{if count($groups)}{foreach from=$groups item="group"}<a href="{$domain}{$section}/browseAssetGroup?group_id={$group.id}">{$group.label}</a> (<a href="{$domain}{$section}/transferSingleAsset?asset_id={$template.id}&amp;group_id={$group.id}&amp;transferAction=remove">remove</a>), {/foreach}{else}<em style="color:#666">None</em>{/if}
{if count($possible_groups)}
        <div>
          <form action="{$domain}{$section}/transferSingleAsset" method="post">
            <input type="hidden" name="asset_id" value="{$template.id}" />
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
      </td>
    </tr> *}
    
  </table>
      
{*      <h4 style="margin-top:15px">Usage of this file</h4> *}
      
 {*     <h4 style="margin-top:15px">Comments on this file</h4>
      
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
        <input type="hidden" name="asset_id" value="{$template.id}" />
        <textarea name="comment_content" style="width:500px;height:90px"></textarea><br />
        <input type="submit" value="Save" />
      </form> *}
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTemplate?asset_type={$asset_type.id}&amp;template={$template.id}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> Edit this file</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/listByType?type={$template.type_info.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$template.type_info.label} files</a></li>
  </ul>
</div>