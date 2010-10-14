{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{item_select name=$name id=$property_id value=$value options=$property._options}

{if $asset.type_info.editable=='true'}
<input type="button" onclick="window.location='{$domain}datamanager/editItem?from=child_edit&amp;item_id='+$('item_property_{$property.id}').value" value="Edit 
&gt;&gt;" />
{/if}
