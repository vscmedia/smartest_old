<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
<select name="item[{$property.id}]" id="item_property_{$property.id}">
  {if $property.required != 'TRUE'}<option value=""></option>{/if}
  {foreach from=$property._options item="option"}
    <option value="{$option.value}"{if $value==$option.value} selected="selected"{/if}>{$option.label}</option>
  {/foreach}
</select>