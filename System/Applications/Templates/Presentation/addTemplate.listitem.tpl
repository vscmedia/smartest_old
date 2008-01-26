<script language="javascript">
{literal}

function showUploader(){document.getElementById('add_type').value = 'upload';
	document.getElementById('tplUploader').style.display = 'block';
	document.getElementById('tplUploadShowButton').style.display = 'none';
	document.getElementById('editTemplateText').style.display = 'none';
	document.getElementById('editorButtons').style.display = 'none';
	
}

function hideUploader(){
	document.getElementById('tplUploader').style.display = 'none';
	document.getElementById('tplUploadShowButton').style.display = 'block';
	document.getElementById('editTemplateText').style.display = 'block';
	document.getElementById('editorButtons').style.display = 'inline';
	document.getElementById('add_type').value = 'direct';
}

{/literal}
</script>

<h3>Add a {$content.newTemplateType} Template</h3>

<form action="{$domain}{$section}/saveNewTemplate" method="post" name="newTemplate" enctype="multipart/form-data">

  <input type="hidden" name="template_type" value="{$content.newTemplateType}" />
  <input type="hidden" name="add_type" id="add_type" value="direct" />
  
  
  <div style="width:100%" id="editTemplateText">
    <label for="template_filename">Template Filename: </label>
    <input type="text" name="template_filename" /><br />
    <textarea name="template_content" id="tpl_textArea" wrap="virtual"></textarea>
  </div>

  <div id="tplUploadShowButton">or, alternatively, <a href="javascript:showUploader();">upload a file</a>.</div>
<div style="display:none;margin-top:8px;margin-bottom:8px" id="tplUploader">
<br /><label for="template_uploaded">Upload file: </label><input type="file" name="template_uploaded" />
  <br /><a href="javascript:hideUploader()">never mind</a></div>
  <input type="submit" value="Save">

</form>

