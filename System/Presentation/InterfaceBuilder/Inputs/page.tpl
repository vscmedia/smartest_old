<select name="{$_input_data.name}" id="{$_input_data.id}">
    {if !$required}<option value="0"></option>{/if}
{foreach from=$_input_data.options item="page"}
    <option value="{$page.id}"{if $value.id==$page.id} selected="selected"{/if}>{$page.title}</option>
{/foreach}
</select>