{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
{asset_group_select id=$property_id name=$name value=$value options=$property._options required=$property.required}

{if is_numeric($value.id)}
 <input type="button" onclick="window.location='{$domain}assets/editAssetGroup?from=item_edit&amp;group_id='+$('item_property_{$value.id}').value" value="Edit &gt;&gt;" />
{/if}