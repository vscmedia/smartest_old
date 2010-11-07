<div class="edit-form-row">
  <div class="form-section-label">Which file would you like to import?</div>
  <div style="padding:5px 0 10px 0">Unimported files in <code>{$directory}</code></div>
  <div style="height:200px;overflow:scroll">
{foreach from=$unimported_files item="unimported_file"}
<input type="radio" name="chosen_file" value="{$unimported_file}" id="{$unimported_file|slug}" /><label for="{$unimported_file|slug}"><code>{$unimported_file}</code></label><br />
{/foreach}
  </div>
</div>