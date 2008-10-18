<div class="form-section-label">Choose a model:</div>

<select name="itemproperty_foreign_key_filter">
  {foreach from=$foreign_key_filter_options item="option"}
  <option value="{$option.id}">{$option.plural_name}</option>
  {/foreach}
</select>