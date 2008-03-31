<div id="work-area">
  <h3>Edit Text File Source</h3>
  <form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

    <input type="hidden" name="asset_id" value="{$asset.id}" />
    <input type="hidden" name="asset_type" value="{$asset.type}" />

      {foreach from=$asset.type_info.param item="parameter"}
      <div class="edit-form-row">
        <div class="form-section-label">{$parameter.name}</div>
        <input type="text" name="params[{$parameter.name}]" value="{$parameter.value}" style="width:250px" />
      </div>
      {/foreach}

      Name of the Asset:  {$asset.stringid}<br />
      <div id="textarea-holder" style="width:100%">
        {* <input type="button" onclick="alert(getCaretPosition('tpl_textArea'))" /> *}
          <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0" class="codepress php autocomplete-off">{$textfragment_content}</textarea>
          <div class="buttons-bar">
              <input type="submit" value="Save Changes" />
              <input type="button" onclick="cancelForm();" value="Cancel" />
          </div>
      <div>

  </form>
  
  {* <script src="{$domain}Resources/System/Javascript/codepress/codepress.js" type="text/javascript" language="javascript"></script> *}
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset_type.label} assets</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> Edit in Rich Text Editor</a></li>
    {if $show_attachments}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/textFragmentElements?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/attach.png" alt=""/> Edit File Attachments</a></li>{/if}
    {if $show_publish}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishTextAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Publish This Text</a></li>{/if}
  </ul>
</div>