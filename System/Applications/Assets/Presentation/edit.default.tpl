{* The file type you are trying to edit can only be edited externally. Please click <a href="{$domain}{$section}/downloadAsset?assettype_code={$assettype_code}&amp;asset_id={$asset_id}">here</a> to download it.<br />
<a href="javascript:history.back();">Back</a> *}

<h3>Edit File Parameters</h3>

<div class="instruction">You are editing {$asset.type_info.label}: ({$asset.url})</div>

<form action="{$domain}{$section}/updateAsset" method="post" enctype="multipart/form-data">
  
  <input type="hidden" name="asset_id" value="{$asset.id}" />
  
  <div id="edit-form-layout">
    
  {foreach from=$asset.default_parameters key="parameter_name" item="parameter_value"}
  <div class="edit-form-row">
    <div class="form-section-label">{$parameter_name}</div>
    <input type="text" name="params[{$parameter_name}]" value="{$parameter_value}" style="width:250px" />
  </div>
  {/foreach}
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save Changes" />
      <input type="button" onclick="cancelForm();" value="Done" />
    </div>
  </div>
  
  </div>
  
</form>