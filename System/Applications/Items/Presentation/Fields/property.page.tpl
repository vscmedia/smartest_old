{page_select name=$_input_data.name id=$_input_data.id value=$value options=$property._options property_id=$property.id host_item_id=$item.id}

{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}