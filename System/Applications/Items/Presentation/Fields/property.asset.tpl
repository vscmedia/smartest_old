{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{asset_select id=$property_id name=$name value=$value options=$property._options required=$property.required}

{if $item}<a href="{$domain}assets/startNewFileCreationForItemPropertyValue?property_id={$property.id}&amp;item_id={$item.id}">Use a new file</a>{/if}

{if $value.id}
  
  {if $value.id && is_array($value.type_info) && count($value.type_info.param)}
    <input type="button" onclick="window.location='{$domain}ipv:{$section}/editAssetData?item_id={$item.id}&amp;property_id={$property.id}'" value="Edit Parameters" />
  {/if}

  {if $value.id && is_array($value.type_info) && $value.type_info.editable=='true'}
   <input type="button" onclick="window.location='{$domain}assets/editAsset?from=item_edit&amp;asset_id='+$('item_property_{$property.id}').value" value="Edit &gt;&gt;" />
  {/if}
{/if}