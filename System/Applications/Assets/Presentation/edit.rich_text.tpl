<h3>Edit Rich-Text Asset</h3>

<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

  <input type="hidden" name="asset_id" value="{$asset.id}" />
  <input type="hidden" name="asset_type" value="{$asset.type}" />
    
    {foreach from=$asset.type_info.param item="parameter"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter.name}</div>
      <input type="text" name="params[{$parameter.name}]" value="{$parameter.value}" style="width:250px" />
    </div>
    {/foreach}
    
    Name of the Asset:  {$asset.stringid}<br />
    <div id="textarea-holder" style="width:100%">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0">{$textfragment_content}</textarea>
        <div class="buttons-bar">
            <input type="submit" value="Save Changes" />
            <input type="button" onclick="cancelForm();" value="Cancel" />
        </div>
    <div>
        
</form>

<script language="javascript" type="text/javascript" src="{$domain}Resources/System/Javascript/tiny_mce/tiny_mce.js"></script>

<script language="javascript" type="text/javascript">

{literal}

tinyMCE.init({
  entity_encoding : "raw",
	mode : "exact",
	elements : "tpl_textArea",
	theme : "advanced",
	verify_html: false,
	plugins : "ibrowser,paste",
	theme_advanced_buttons3_add_before : "ibrowser,separator",
	theme_advanced_buttons3_add : "paste,pasteword,selectall",
	theme_advanced_disable : "image,styleprops",
	theme_advanced_toolbar_location : "top",
	theme_advanced_resizing : true,
	theme_advanced_toolbar_align : "center",
	convert_fonts_to_spans : true,
	paste_use_dialog : true,
  paste_remove_spans : true,
  paste_remove_styles: true,
  paste_strip_class_attributes: true,
  relative_urls : false,
  remove_script_host : true,
{/literal}  document_base_url : "{$domain}" {literal}
  
});

{/literal}
</script>