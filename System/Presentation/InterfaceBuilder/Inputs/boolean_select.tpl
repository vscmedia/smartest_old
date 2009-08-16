<select name="{$_input_data.name}" id="{$_input_data.id}">
    {if !$_input_data.required}<option value=""></option>{/if}
    <option value="TRUE"{if $_input_data.value == true} checked="checked"{/if}>On</option>
    <option value="FALSE"{if $_input_data.value === false} checked="checked"{/if}>Off</option>
</select>