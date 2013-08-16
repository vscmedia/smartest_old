<div class="edit-form-row">
  <div class="form-section-label">Which file would you like to import?</div>
  <div style="padding:5px 0 10px 0">Unimported files in <code>{$directory}</code></div>
  <div style="height:250px;overflow:scroll;border:1px solid #ccc;padding:5px">
{foreach from=$unimported_files item="unimported_file"}
<input type="radio" name="chosen_file" value="{$unimported_file}" id="{$unimported_file|slug}" />&nbsp;<label for="{$unimported_file|slug}"><code>{$unimported_file}</code></label><br />
{/foreach}
  </div>
</div>