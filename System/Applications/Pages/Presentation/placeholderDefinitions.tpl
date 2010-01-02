<div id="work-area">
    
  {load_interface file="placeholder_tabs.tpl"}
  <h3>Placeholder definitions</h3>
  
  <div class="special-box">
    <form action="{$domain}{$section}/placeholderDefinitions" method="get" id="mode-changer">Show:
      <input type="hidden" name="placeholder_id" value="{$placeholder.id}">
      <select name="mode" onchange="$('mode-changer').submit();">
        <option value="draft"{if $mode == "draft"} selected="selected"{/if}>Draft definitions</option>
        <option value="live"{if $mode == "live"} selected="selected"{/if}>Live definitions</option>
      </select>
    </form>
  </div>
  
  <table cellpadding="2" border="0" cellspacing="1" style="background-color:#ccc;width:100%">
    <tr style="background-color:#ddd">
      <td style="width:150px"><strong>Page</strong></td>
      <td><strong>File</strong></td>
    </tr>
    {foreach from=$definitions item="definition"}
    <tr style="background-color:#{cycle values="fff,eee"}">
      <td style="width:100px">{$definition.page.title}</td>
      <td><img src="{$definition.asset.small_icon}" alt="" /> {$definition.asset.url} (<a href="{$domain}assets/previewAsset?asset_id={$definition.asset.id}">view</a>)</td>
    </tr>
    {/foreach}
  </table>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{$domain}assets/newAssetGroupFromPlaceholder?placeholder_id={$placeholder.id}&amp;mode={$mode}" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_add.png" border="0" alt=""> Create file group</a></li>
  </ul>
</div>