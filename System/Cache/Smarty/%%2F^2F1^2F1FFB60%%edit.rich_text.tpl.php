<?php /* Smarty version 2.6.18, created on 2007-11-25 21:28:22
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Assets/Presentation/edit.rich_text.tpl */ ?>
<h3>Edit Rich-Text Asset</h3>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

  <input type="hidden" name="asset_id" value="<?php echo $this->_tpl_vars['asset']['id']; ?>
" />
  <input type="hidden" name="asset_type" value="<?php echo $this->_tpl_vars['asset']['type']; ?>
" />
    
    <?php $_from = $this->_tpl_vars['asset']['type_info']['param']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['parameter']):
?>
    <div class="edit-form-row">
      <div class="form-section-label"><?php echo $this->_tpl_vars['parameter']['name']; ?>
</div>
      <input type="text" name="params[<?php echo $this->_tpl_vars['parameter']['name']; ?>
]" value="<?php echo $this->_tpl_vars['parameter']['value']; ?>
" style="width:250px" />
    </div>
    <?php endforeach; endif; unset($_from); ?>
    
    Name of the Asset:  <?php echo $this->_tpl_vars['asset']['stringid']; ?>
<br />
    <div id="textarea-holder" style="width:100%">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0"><?php echo $this->_tpl_vars['textfragment_content']; ?>
</textarea>
        <div class="buttons-bar">
            <input type="submit" value="Save Changes" />
            <input type="button" onclick="cancelForm();" value="Cancel" />
        </div>
    <div>
        
</form>

<script language="javascript" type="text/javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Javascript/System/tiny_mce/tiny_mce.js"></script>

<script language="javascript" type="text/javascript">

<?php echo '

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
	paste_strip_class_attributes: true
});

'; ?>

</script>