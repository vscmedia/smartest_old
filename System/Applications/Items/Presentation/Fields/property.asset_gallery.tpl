{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{asset_group_select id=$property_id name=$name value=$value options=$property._options required=$property.required}

{if is_numeric($item.id) || is_numeric($value.id)}
  <ul class="item_property_actions">
    {* <li style="display:{if is_numeric($item.id)}block{else}none{/if}"><a href="{$domain}sets/addSet?class_id={$property.foreign_key_filter}&amp;from=editItem&amp;itemproperty_id={$property.id}&amp;item_id={$item.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}" title="Add a new set" id="new-set-button-{$property.id}"><img src="{$domain}Resources/Icons/add.png" alt="" /></a></li> *}
    <li style="display:{if is_numeric($value.id)}block{else}none{/if}"><a href="{$domain}assets/arrangeAssetGallery?from=item_edit&amp;group_id={$value.id}&amp;from=editItem&amp;item_id={$item.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}" title="Arrange this gallery" id="edit-set-button-{$property.id}"><img src="{$domain}Resources/Icons/arrow_switch.png" alt="" /></a></li>
  </ul>
{/if}

{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}