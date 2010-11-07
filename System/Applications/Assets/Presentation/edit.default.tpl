<form action="{$domain}{$section}/updateAsset" method="post" enctype="multipart/form-data">
  
  <input type="hidden" name="asset_id" value="{$asset.id}" />
  
  <div class="special-box">
    <span class="heading">Language</span>
    <select name="asset_language">
      <option value="">{$lang.label}</option>
  {foreach from=$_languages item="lang" key="langcode"}
      <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
  {/foreach}
    </select>
  </div>
  
  <div id="edit-form-layout">
    
    {foreach from=$asset.default_parameters key="parameter_name" item="parameter_value"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter_name}</div>
      <input type="text" name="params[{$parameter_name}]" value="{$parameter_value}" style="width:250px" />
    </div>
    {/foreach}
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        {* <input type="submit" value="Save Changes" />
        <input type="button" onclick="cancelForm();" value="Done" /> *}
        {save_buttons}
      </div>
    </div>
  
  </div>
  
</form>