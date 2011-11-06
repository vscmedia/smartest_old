{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{dataset_select id=$property_id name=$name value=$value options=$property._options required=$property.required}

{if is_numeric($value.id)}
 {* <input type="button" onclick="window.location='{$domain}sets/editSet?from=item_edit&amp;set_id='+$('item_property_{$value.id}').value" value="Edit &gt;&gt;" /> *}
{/if}

{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}