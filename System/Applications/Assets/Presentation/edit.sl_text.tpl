<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

  <input type="hidden" name="asset_id" value="{$asset.id}" />
  <input type="hidden" name="asset_type" value="{$asset.type}" />
  
  {foreach from=$asset.default_parameters key="parameter_name" item="parameter_value"}
  <div class="edit-form-row">
    <div class="form-section-label">{$parameter_name}</div>
    <input type="text" name="params[{$parameter_name}]" value="{$parameter_value}" style="width:250px" />
  </div>
  {/foreach}
  
  <div class="edit-form-row">
    <div class="form-section-label">Name of the Asset</div>
    {$asset.stringid}
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Line Contents</div>
    <input type="text" name="asset_content" value="{$textfragment_content}" maxlength="255" />
  </div>
  
  <div class="buttons-bar">
    <input type="submit" value="Save Changes" />
    <input type="button" onclick="cancelForm();" value="Cancel" />
  </div>
  
</form>