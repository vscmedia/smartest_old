{capture name="property_id" assign="property_id"}item_property_{$_input_data.property.id}{/capture}

{color_input name=$_input_data.name value=$value id=$_input_data.property_id}{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}