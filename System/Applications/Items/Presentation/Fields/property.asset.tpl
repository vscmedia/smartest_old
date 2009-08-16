{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
{asset_select id=$property_id name=$name value=$value options=$property._options required=$property.required}

{if count($asset.type_info.param) && $asset.id}
  <input type="button" onclick="window.location='{$domain}{$section}/editItemPropertyValueAssetData?item_id={$item.id}&amp;property_id={$property.id}'" value="Edit Parameters" />
{/if}

{if $asset.type_info.editable=='true'}
 <input type="button" onclick="window.location='{$domain}assets/editAsset?from=item_edit&amp;asset_id='+$('item_property_{$property.id}').value" value="Edit &gt;&gt;" />
{/if}