{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{asset_group_select id=$property_id name=$name value=$value options=$property._options required=$property.required}

{if is_numeric($value.id)}
 <input type="button" id="edit-group-button-{$property.id}" value="Edit &gt;&gt;" />
 <script type="text/javascript">
 $('edit-group-button-{$property.id}').observe('click', function(){ldelim}
     window.location='{$domain}assets/editAssetGroup?from=item_edit&group_id='+$('item_property_{$property.id}').value
 {rdelim});
 </script>
{/if}

{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}