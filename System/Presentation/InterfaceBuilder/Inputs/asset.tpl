<select name="{$_input_data.name}" id="{$_input_data.id}">
    {if !$required}<option value="0"></option>{/if}
{foreach from=$options item="asset"}
    <option value="{$asset.id}"{if $value.id==$asset.id} selected="selected"{/if}>{$asset.label} ({$asset.url})</option>
{/foreach}
</select>