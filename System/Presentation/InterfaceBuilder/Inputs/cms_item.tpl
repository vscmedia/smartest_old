<select name="{$_input_data.name}" id="{$_input_data.id}">
    {if !$_input_data.required}<option value="0"></option>{/if}
{foreach from=$_input_data.options item="foreign_item"}
    <option value="{$foreign_item.id}"{if $_input_data.value.id==$foreign_item.id} selected="selected"{/if}>{$foreign_item.name}</option>
{/foreach}
</select>