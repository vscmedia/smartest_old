<div class="form-section-label">Choose a referring property:</div>

<select name="foreign_key_filter">
  {foreach from=$foreign_key_filter_options item="option"}
  <option value="{$option.id}">{$option._model.plural_name} / {$option.name}</option>
  {/foreach}
</select>