<select name="{$_input_data.name}" id="{$_input_data.id}">
    {if !$required}<option value="0"></option>{/if}
{foreach from=$_input_data.options item="user"}
    <option value="{$user.id}"{if $value.id==$user.id} selected="selected"{/if}>{$user.full_name} ({$user.username})</option>
{/foreach}
</select>