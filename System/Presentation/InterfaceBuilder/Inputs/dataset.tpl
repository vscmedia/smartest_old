<select name="{$_input_data.name}" id="{$_input_data.id}">
  {if !$_input_data.required}<option value="0"></option>{/if}
{foreach from=$_input_data.options item="dataset"}
    <option value="{$dataset.id}"{if $_input_data.value.id==$dataset.id} selected="selected"{/if}>{$dataset.label}</option>
{/foreach}
</select>