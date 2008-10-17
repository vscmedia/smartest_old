<script language="javascript">
{literal}

function showUploader(){
	document.getElementById('tplUploader').style.display = 'block';
	document.getElementById('tplUploadShowButton').style.display = 'none';
	document.getElementById('editTemplateText').style.display = 'none';
	document.getElementById('add_type').value = 'UPLOAD';
}

function hideUploader(){
	document.getElementById('tplUploader').style.display = 'none';
	document.getElementById('tplUploadShowButton').style.display = 'block';
	document.getElementById('editTemplateText').style.display = 'block';
	document.getElementById('add_type').value = 'DIRECT';
}

{/literal}
</script>

<div id="work-area">

<h3>{$interface_title}</h3>

<form action="{$domain}{$section}/saveNewTemplate" method="post" name="newTemplate" enctype="multipart/form-data">

  <input type="hidden" name="template_type" value="{$template_type}" />
  <input type="hidden" name="add_type" id="add_type" value="DIRECT" />
  
  <div style="width:100%" id="editTemplateText">
    <label for="template_filename">Template Filename: </label>
    <input type="text" name="template_filename" /><br />
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
  
  <input type="submit" value="Save" />

</form>

</div>