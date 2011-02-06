<select name="{$name}" id="item_property_{$property.id}">  
  {if !$value_required}<option value=""></option>{/if}
  {foreach from=$options item="option"}
    <option value="{$option.value}"{if $value.value==$option.value} selected="selected"{/if}>{$option.label}</option>
  {/foreach}
</select>