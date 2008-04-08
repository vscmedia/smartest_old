<div id="work-area">
  
  <h3>Define Placeholder</h3>
  
  <form id="file_chooser" method="get" action="{$domain}{$section}/definePlaceholder">
    
    <div class="edit-form-row">
      <div class="form-section-label">Choose a file to define this placeholder with</div>
      <select name="chosen_asset_id" onchange="$('file_chooser').submit()">
        {if !$valid_definition}<option value="">None Selected</option>{/if}
        {foreach from=$assets item="available_asset"}
          <option value="{$available_asset.id}"{if $available_asset.id==$asset.id} selected="selected"{/if}>{if $available_asset.url}{$available_asset.url}{else}{$available_asset.stringid}{/if}{if $available_asset.id==$live_asset_id} - LIVE DEF{/if}</option>
        {/foreach}
      </select>
      
    </div>
    
    <input type="hidden" name="assetclass_id" value="{$placeholder.name}" />
    <input type="hidden" name="page_id" value="{$page.webid}" />
    
    </form>
    
    <form id="pageViewForm" method="post" action="{$domain}{$section}/updatePlaceholderDefinition">
    
      <input type="hidden" name="page_id" value="{$page.id}" />
      <input type="hidden" name="placeholder_id" value="{$placeholder.id}" />
    
    {if $valid_definition}
    
      <input type="hidden" name="asset_id" value="{$asset.id}" />
    
      <div class="edit-form-row">
        <div class="form-section-label">Chosen File:</div>
        <b>{$asset.stringid}</b> ({if $asset_type.storage.type == 'file'}{$asset_type.storage.location}{/if}{$asset.url}) - {$asset_type.label}
      </div>
    
    {foreach from=$params key="parameter_name" item="parameter"}
      <div class="edit-form-row">
        <div class="form-section-label">{$parameter_name}</div>
        <input type="text" name="params[{$parameter_name}]" style="width:250px" value="{$parameter.value}" />
      </div>
    {/foreach}
    
    {/if}
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      {if $valid_definition}<input type="submit" value="Save Changes" />{/if}
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