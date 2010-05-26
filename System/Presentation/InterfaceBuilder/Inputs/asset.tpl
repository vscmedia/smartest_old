<select name="{$_input_data.name}" id="{$_input_data.id}">
    {if !$_input_data.required}<option value="0"></option>{/if}

{foreach from=$_input_data.options item="asset"}
    <option value="{$asset.id}"{if $_input_data.value.id==$asset.id} selected="selected"{/if}>{$asset.label} ({$asset.url})</option>
{/foreach}
</select>