{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
{item_select name=$name id=$property_id value=$value options=$property._options}

{if $asset.type_info.editable=='true'}
<input type="button" onclick="window.location='{$domain}datamanager/editItem?from=child_edit&amp;item_id='+$('item_property_{$property.id}').value" value="Edit 
&gt;&gt;" />
{/if}
