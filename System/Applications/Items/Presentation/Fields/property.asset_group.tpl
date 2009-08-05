<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
<select name="item[{$property.id}]" id="item_property_{$property.id}">
  {if $property.required != 'TRUE'}<option value="0"></option>{/if}
  {foreach from=$property._options item="group"}
    <option value="{$group.id}"{if $value.id==$group.id} selected="selected"{/if}>{$group.label}</option>
  {/foreach}
</select>

{if is_numeric($value.id)}
 <input type="button" onclick="window.location='{$domain}assets/editAssetGroup?from=item_edit&amp;group_id='+$('item_property_{$value.id}').value" value="Edit &gt;&gt;" />
{/if}