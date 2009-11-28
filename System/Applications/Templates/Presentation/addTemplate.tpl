<script language="javascript">
{literal}

function showUploader(){
	document.getElementById('tplUploader').style.display = 'block';
	document.getElementById('tplUploadShowButton').style.display = 'none';
	document.getElementById('editTMPL').style.display = 'none';
	document.getElementById('add_type').value = 'UPLOAD';
}

function hideUploader(){
	document.getElementById('tplUploader').style.display = 'none';
	document.getElementById('tplUploadShowButton').style.display = 'block';
	document.getElementById('editTMPL').style.display = 'block';
	document.getElementById('add_type').value = 'DIRECT';
}

{/literal}
</script>

<div id="work-area">

<h3>{$interface_title}</h3>

{if $allow_save}

<form action="{$domain}{$section}/saveNewTemplate" method="post" name="newTemplate" enctype="multipart/form-data">

  <input type="hidden" name="template_type" value="{$template_type}" />
  <input type="hidden" name="add_type" id="add_type" value="DIRECT" />
  
  <div class="special-box">
    <label for="template_filename">Template Filename: </label>
    <input type="text" name="template_filename" /><br />
  </div>
  
  <div style="width:100%" id="editTMPL" class="textarea-holder">
    <textarea name="template_content" id="tpl_textArea" wrap="virtual"></textarea>
  </div>

  <div id="tplUploadShowButton">or, alternatively, <a href="javascript:showUploader();">upload a file</a>.</div>
  
  <div style="display:none;margin-top:8px;margin-bottom:8px" id="tplUploader">
    <br />
    <label for="template_upload">Upload file: </label>
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <input type="file" id="template_upload" name="template_upload" />
    <br />
    <a href="javascript:hideUploader()">Never Mind</a>
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save" />
    </div>
  </div>

</form>

<script src="{$domain}Resources/System/Javascript/CodeMirror-0.65/js/codemirror.js" type="text/javascript"></script>
<script src="{$domain}Resources/System/Javascript/CodeMirror-0.65/js/mirrorframe.js" type="text/javascript"></script>

<script type="text/javascript">
{literal}  var editor = new CodeMirror.fromTextArea('tpl_textArea', {{/literal}
  parserfile: 'parsexml.js',
  stylesheet: "{$domain}Resources/System/Javascript/CodeMirror-0.65/css/xmlcolors.css",
  continuousScanning: 500,
  height: '400px',
  path: "{$domain}Resources/System/Javascript/CodeMirror-0.65/js/"
{literal}  }); {/literal}
</script>

{else}
<div class="warning">The directory <code>{$path}</code> is not writable by the web server, so new templates cannot currently be created via Smartest. You may either upload them to the same place via FTP/SFTP, or speak to your server administrator to fix this.</div>

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();" />
  </div>
</div>
{/if}

</div>