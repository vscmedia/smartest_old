<div class="form-section-label">What Type of File?</div>

<select name="foreign_key_filter">
  <optgroup label="File Types">
    {foreach from=$foreign_key_filter_options.asset_types item="option"}
    <option value="{$option.id}">{$option.label}</option>
    {/foreach}
  </optgroup>
  <optgroup label="Placeholder Types">
    {foreach from=$foreign_key_filter_options.placeholder_types item="option"}
    <option value="{$option.id}">{$option.label}</option>
    {/foreach}
  </optgroup>
</select>