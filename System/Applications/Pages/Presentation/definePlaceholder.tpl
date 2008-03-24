<div id="work-area">
  
  <h3>Define Placeholder</h3>
  
  <form id="pageViewForm" method="post" action="{$domain}{$section}/updatePlaceholderDefinition">
    <input type="hidden" name="page_id" value="{$page.id}" />
    <input type="hidden" name="placeholder_id" value="{$placeholder.id}" />
    {* <input type="hidden" name="asset_id" id="item_id_input" value="" /> *}
    
    <div class="edit-form-row">
      <div class="form-section-label">Choose a file to define this placeholder with</div>
      <select name="asset_id">
        {foreach from=$assets item="asset"}
          <option value="{$asset.id}"{if $asset.id==$draft_asset_id} selected="selected"{/if}>{if $asset.url}{$asset.url}{else}{$asset.stringid}{/if}{if $asset.id==$live_asset_id} - LIVE DEF{/if}</option>
        {/foreach}
      </select>
    </div>
    
    {foreach from=$params key="parameter_name" item="parameter_value"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter_name}</div>
      <input type="text" name="params[{$parameter_name}]" style="width:250px" value="{$parameter_value}" />
    </div>
    {/foreach}
    
  {* </form>
  
   <div id="options-view-chooser">
  <a href="javascript:nothing()" onclick="setView('list', 'options_grid')">List</a> /
  <a href="javascript:nothing()" onclick="setView('grid', 'options_grid')">Icons</a>
  </div>

   <ul class="options-grid" style="margin-top:0px" id="options_grid">
  {foreach from=$assets item="asset"}
  <li>
      <a href="javascript:nothing()" class="option" id="item_{$asset.id}" onclick="setSelectedItem('{$asset.id}');" >
      <img border="0" src="{$domain}Resources/Icons/page.png" />{$asset.url}</a>
  </li>
  {/foreach}
  </ul> *}
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save Changes" />
      <input type="button" onclick="cancelForm();" value="Cancel" />
    </div>
  </div>
  
  </form>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected Asset</b></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('updatePlaceholderDefinition');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Use This Asset</a></li>
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/pageAssets?page_id={$page.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Cancel</a></li>
  </ul>
  
</div>