<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
<select name="item[{$property.id}]" id="item_property_{$property.id}">
  {if $property.required != 'TRUE'}<option value="0"></option>{/if}
  {foreach from=$property._options item="foreign_item"}
    <option value="{$foreign_item.id}"{if $value==$foreign_item.id} selected="selected"{/if}>{$foreign_item.name}</option>
  {/foreach}
</select>

{if $asset.type_info.editable=='true'}
<input type="button" onclick="window.location='{$domain}datamanager/editItem?from=child_edit&amp;item_id='+$('item_property_{$property.id}').value" value="Edit 
&gt;&gt;" />
{/if}
