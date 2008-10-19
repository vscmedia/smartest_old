<div class="form-section-label">Choose a Model:</div>

<select name="foreign_key_filter">
  {foreach from=$foreign_key_filter_options item="option"}
  <option value="{$option.id}">{$option.plural_name}</option>
  {/foreach}
</select>