<div id="work-area">
  <h3>Parameters</h3>
  
  <div class="instruction">How should this {$asset_type.label} look on {$model.name} '{$item.name}'</div>
  
  <form action="{$domain}{$section}/updateItemPropertyValueAssetData" method="post">
  
  <input type="hidden" name="item_id" value="{$item.id}" />
  <input type="hidden" name="property_id" value="{$property.id}" />

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

  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save" />
      <input type="button" value="Cancel" onclick="cancelForm()" />
    </div>
  </div>

</div>