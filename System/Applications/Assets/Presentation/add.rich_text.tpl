  <div style="display:none;margin-top:8px;margin-bottom:8px" id="uploader">
    Upload file: <input type="file" name="new_file" /><br />
    <a href="javascript:hideUploader();">never mind</a>
  </div>
  
  <div style="width:100%" id="text_window">
    Name This Asset: <input type="text" name="string_id" />
    <textarea name="content" id="tpl_textArea" wrap="virtual"></textarea>
  </div>

  <div id="uploader_link">or, alternatively, <a href="javascript:showUploader();">upload a file</a>.</div>

<script language="javascript" type="text/javascript" src="{$domain}Resources/Javascript/System/tiny_mce/tiny_mce.js"></script>

<script language="javascript" type="text/javascript">
{literal}
tinyMCE.init({
	mode : "exact",
	elements : "tpl_textArea",
	theme : "advanced",
	plugins : "paste",
	theme_advanced_buttons3_add_before : "tablecontrols,separator",
	theme_advanced_buttons3_add : "paste,pasteword,selectall",
	theme_advanced_disable : "image,styleprops",
	theme_advanced_toolbar_location : "top",
	theme_advanced_resizing : true,
	theme_advanced_toolbar_align : "left",
	convert_fonts_to_spans : true,
	paste_use_dialog : true,
	paste_remove_spans : true,
	paste_remove_styles: true,
	paste_strip_class_attributes: true,
	relative_urls : false,
  remove_script_host : true,
{/literal}  document_base_url : "{$domain}" {literal}
  
});{/literal}
</script>