<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

  <input type="hidden" name="asset_id" value="{$asset.id}" />
  <input type="hidden" name="asset_type" value="{$asset.type}" />
    
    <div class="special-box">
      <span class="heading">Language</span>
      <select name="asset_language">
        <option value="">{$lang.label}</option>
    {foreach from=$_languages item="lang" key="langcode"}
        <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
    {/foreach}
      </select>
    </div>
    
    {foreach from=$asset.type_info.param item="parameter"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter.name}</div>
      <input type="text" name="params[{$parameter.name}]" value="{$parameter.value}" />
    </div>
    {/foreach}
    
    <div id="textarea-holder" style="width:100%">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0">{$textfragment_content}</textarea>
        <span id="wordcount"></span>
        <div class="buttons-bar">
            {save_buttons}
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
	plugins : "paste",
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

var AutoSaver = new PeriodicalExecuter(function(pe){
  // autosave routine
}, 5);

{/literal}
</script>