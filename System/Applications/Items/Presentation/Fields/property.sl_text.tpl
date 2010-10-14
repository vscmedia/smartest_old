{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{text_input name=$name value=$value id=$property_id}<span class="form-hint">Max 255 characters</span>