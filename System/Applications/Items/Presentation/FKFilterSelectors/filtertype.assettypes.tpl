<div class="form-section-label">What Type of file?</div>

<select name="itemproperty_foreign_key_filter">
  {foreach from=$foreign_key_filter_options item="option"}
  <option value="{$option.id}">{$option.label}</option>
  {/foreach}
</select>