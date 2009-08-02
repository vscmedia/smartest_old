<div style="display:none;margin-top:8px;margin-bottom:8px" id="uploader" class="special-box">
  Name this Asset: <input type="text" name="string_id" />
  <br />Upload file: <input type="file" name="new_file" />
  <br /><a href="javascript:hideUploader()">never mind</a>
</div>

<div class="edit-form-row">
  <div class="form-section-label">File Name</div>
  <label for="new_filename">Store this file as: </label>
  {$new_asset_type_info.storage.location}<input type="text" name="new_filename" style="width:250px" />
</div>

<div style="width:100%" id="text_window" class="edit-form-row">
  <div class="form-section-label">File Contents</div>
  <textarea name="content" id="tpl_textArea" wrap="virtual"></textarea>
</div>

<div id="uploader_link" class="special-box">or, alternatively,
  <a href="javascript:showUploader();">upload a file</a>.
</div>