<div id="work-area">
  <h3>Create file group from placeholder</h3>
  <div class="special-box">
    <form action="{$domain}{$section}/newAssetGroupFromPlaceholder" method="get" id="mode-changer">Use:
      <input type="hidden" name="placeholder_id" value="{$placeholder.id}">
      <select name="mode" onchange="$('mode-changer').submit();">
        <option value="draft"{if $mode == "draft"} selected="selected"{/if}>Draft definitions</option>
        <option value="live"{if $mode == "live"} selected="selected"{/if}>Live definitions</option>
      </select>
    </form>
  </div>
  
  <form action="{$domain}{$section}/createNewAssetGroupFromPlaceholder" method="post">
    
    <input type="hidden" name="placeholder_id" value="{$placeholder.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">File group name</div>
      <input type="text" name="asset_group_label" value="Files defining placeholder &quot;{$placeholder.name}&quot;" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Files</div>
    
        <table width="100%" cellpadding="0" cellspacing="2" style="width:100%" border="0">
    
          <tr>
            <td style="width:25px"></td>
            <td><strong>File</strong></td>
            <td style="width:150px"><strong>Page</strong></td>
          </tr>
          {foreach from=$definitions item="definition"}
          <tr style="background-color:#{cycle values="fff,eee"}">
            <td style="width:20px">
              <input type="checkbox" name="asset_ids[]" value="{$definition.asset.id}" id="asset_{$definition.asset.id}" checked="checked" />
            </td>
            <td><img src="{$definition.asset.small_icon}" alt="" /> <label for="asset_{$definition.asset.id}">{$definition.asset.url}</label> (<a href="{$domain}assets/previewAsset?asset_id={$definition.asset.id}">view</a>)</td>
            <td style="width:100px">{$definition.page.title}</td>
          </tr>
          {/foreach}
        </table>
    
      </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="window.location='{$domain}websitemanager/placeholderDefinitions?placeholder_id={$placeholder.id}&amp;mode={$mode}'" />
          <input type="submit" value="Save file group" />
        </div>
      </div>
  
  </form>
  
</div>