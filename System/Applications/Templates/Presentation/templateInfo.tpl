<div id="work-area">
  
  {load_interface file="template_edit_tabs.tpl"}
  
  <h3>Template Info</h3>
  
  {if $template.id}
  
  <form action="{$domain}templates/updateTemplateInfo" method="post">
    
    <input type="hidden" name="template_id" value="{$template.id}" />
    
    <table cellspacing="1" border="0" class="info-table">
      <tr>
        <td style="width:170px;background-color:#fff" valign="middle" class="field-name">Template name:</td>
        <td>
          <input type="text" name="template_label" value="{$template.label}" maxlength="64" class="free-sl-text-input" />
        </td>
      </tr>
      <tr>
        <td style="width:150px;background-color:#fff" class="field-name">Name on disk:</td>
        <td><code>{$template.full_path}</code></td>
      </tr>
      <tr>
        <td class="field-name">Size:</td>
        <td>{$template.size}</td>
      </tr>
      <tr>
        <td class="field-name">Type:</td>
        <td><a href="{$domain}smartest/templates/{$template.type}">{$template.type_info.label}</a> <span style="color:#666">({$template.type})</span></td>
      </tr>
      {if $template.created > 0}
      <tr>
        <td class="field-name">Created:</td>
        <td>{$template.created|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
      </tr>
      {/if}
      {if $template.modified > 0}
      <tr>
        <td class="field-name">Modified:</td>
        <td>{$template.modified|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
      </tr>
      {/if}
      <tr>
        <td valign="middle" class="field-name">Owner:</td>
        <td>
          <select name="template_user_id">
  {foreach from=$potential_owners item="p_owner"}
            <option value="{$p_owner.id}"{if $template.owner.id == $p_owner.id} selected="selected"{/if}>{$p_owner.fullname} ({$p_owner.id})</option>
  {/foreach}
          </select>
        </td>
      </tr>
{if $template_type.model_specific != 'never'}
      <tr>
        <td valign="middle" class="field-name">Restrict to one model:</td>
        <td>
          <select name="template_model_id">
            {if $template_type.model_specific == 'sometimes'}<option value="0"{if $template.model_id == '0'} selected="selected"{/if}>No restriction</option>{/if}
  {foreach from=$models item="model"}
            <option value="{$model.id}"{if $template.model_id == $model.id} selected="selected"{/if}>{$model.pluralname}</option>
  {/foreach}
          </select>&nbsp;{help id="templates:data_in_templates"}What's this?{/help}
        </td>
      </tr>
{/if} <tr>
        <td valign="middle" class="field-name">Language:</td>
        <td>
          <select name="template_language" id="template-language">
        {foreach from=$_languages item="lang" key="langcode"}
            <option value="{$langcode}"{if $template.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
        {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td class="field-name">Original site:</td>
        <td>{$template.site.label}</td>
      </tr>
      <tr>
        <td class="field-name">Shared with other sites:</td>
        <td><input type="checkbox" name="template_shared"{if $template.shared==1} checked="checked"{/if} /></td>
      </tr>
    </table>
  
    <div class="buttons-bar" style="margin-top:5px">
      {save_buttons}
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
  
</div>
  
  {else}
    
    No template info available.
    
  {/if}

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