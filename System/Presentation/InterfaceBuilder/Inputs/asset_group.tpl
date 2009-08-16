<select name="{$_input_data.name}" id="{$_input_data.id}">
  {if !$_input_data.required}<option value="0"></option>{/if}
{foreach from=$_input_data.options item="group"}
    <option value="{$group.id}"{if $_input_data.value.id==$group.id} selected="selected"{/if}>{$group.label}</option>
{/foreach}
</select>