<div class="form-section-label">What Type of File?</div>

<select name="foreign_key_filter">
  {foreach from=$foreign_key_filter_options item="option"}
  <option value="{$option.id}">{$option.label}</option>
  {/foreach}
</select>