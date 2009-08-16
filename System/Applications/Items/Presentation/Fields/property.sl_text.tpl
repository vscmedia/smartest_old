{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
{text_input name=$name value=$value id=$property_id}