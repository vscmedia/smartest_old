<select name="item[{$property.id}]" id="item_property_{$property.id}">
  {if $property.required != 'TRUE'}<option value=""></option>{/if}
  {foreach from=$property._options item="option"}
    <option value="{$option.value}"{if $value && $value.value==$option.value} selected="selected"{/if}>{$option.label}</option>
  {/foreach}
</select>
{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}