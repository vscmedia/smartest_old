<select name="{$_input_data.name}" id="{$_input_data.id}">
    {if !$_input_data.required}<option value="0"></option>{/if}
{foreach from=$_input_data.options item="tag"}
    <option value="{$tag.id}"{if $_input_data.value.id==$tag.id} selected="selected"{/if}>{$tag.label}</option>
{/foreach}
</select>