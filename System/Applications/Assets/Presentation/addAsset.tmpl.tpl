<script language="javascript">
{literal}

function insertAssetClass(){
	var assetClassName = prompt("Enter the asset class name");
	var html = '{assetclass get="'+assetClassName+'"}';
	insertElement(html);
}

function insertElement(){
	var field = document.getElementById("tpl_textArea");
	field.focus();
	alert(field.value);
}

function showUploader(){
	document.getElementById('tplUploader').style.display = 'block';
	document.getElementById('tplUploadShowButton').style.display = 'none';
	document.getElementById('editTemplateText').style.display = 'none';
	document.getElementById('editorButtons').style.display = 'none';
	document.getElementById('add-type').value = 'upload';
}

function hideUploader(){
	document.getElementById('tplUploader').style.display = 'none';
	document.getElementById('tplUploadShowButton').style.display = 'block';
	document.getElementById('editTemplateText').style.display = 'block';
	document.getElementById('editorButtons').style.display = 'inline';
	document.getElementById('add-type').value = 'direct';
}

{/literal}
</script>

<h3>Add a Template Asset</h3>

<form action="{$domain}{$section}/saveNewTemplateAsset" method="post" name="newTemplate" enctype="multipart/form-data">

  <input type="hidden" name="template_type" value="{$content.newAssetTypeId}" />
  <input type="hidden" name="add_type" id="add-type" value="direct" />
  
  <div style="display:inline" id="editorButtons">
    <input type="button" onclick="insertLink();" value="Insert Link" disabled="disabled" style="float:right;margin-right:5px;" />
    <input type="button" onclick="insertImage();" value="Insert Image" disabled="disabled" style="float:right;margin-right:5px;" />
    <input type="button" onclick="insertAssetClass();" value="Insert Asset Class" disabled="disabled" style="float:right;margin-right:5px;" />
  </div>

  <div style="width:100%" id="editTemplateText">
    <label for="template_filename">Template Filename: </label>
    <input type="text" name="template_filename" /><br />
    <textarea name="template_content" id="tpl_textArea" wrap="virtual"></textarea>
  </div>

  <div id="tplUploadShowButton">or, alternatively, <a href="javascript:showUploader();">upload a file</a>.</div>
<div style="display:none;margin-top:8px;margin-bottom:8px" id="tplUploader">
<label for="template_stringid">Name this Asset: </label><input type="text" name="template_stringid" />
<br /><label for="template_uploaded">Upload file: </label><input type="file" name="template_uploaded" />
  <br /><a href="javascript:hideUploader()">never mind</a></div>
  <div class="buttons-bar"><input type="submit" value="Save"></div>

</form>

