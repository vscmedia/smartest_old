<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
<select name="item[{$property.id}]" id="item_property_{$property.id}">
  {if $property.required != 'TRUE'}<option value="0"></option>{/if}
  {foreach from=$property._options item="asset"}
    <option value="{$asset.id}"{if $value.id==$asset.id} selected="selected"{/if}>{$asset.url}</option>
  {/foreach}
</select>

{if count($asset.type_info.param) && $asset.id}
  <input type="button" onclick="window.location='{$domain}{$section}/editItemPropertyValueAssetData?item_id={$item.id}&amp;property_id={$property.id}'" value="Edit Parameters" />
{/if}

{if $asset.type_info.editable=='true'}
 <input type="button" onclick="window.location='{$domain}assets/editAsset?from=item_edit&amp;asset_id='+$('item_property_{$property.id}').value" value="Edit &gt;&gt;" />
{/if}