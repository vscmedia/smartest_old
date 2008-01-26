<?php /* Smarty version 2.6.18, created on 2007-12-01 14:55:33
         compiled from /var/www/html/System/Applications/Assets/Presentation/addAsset.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/www/html/System/Applications/Assets/Presentation/addAsset.tpl', 87, false),)), $this); ?>
<script language="javascript">

var acceptable_suffixes = <?php echo $this->_tpl_vars['suffixes']; ?>
;
var input_mode = '<?php echo $this->_tpl_vars['starting_mode']; ?>
';
var show_params_holder = false;

<?php echo '

function insertAssetClass(){
	var assetClassName = prompt("Enter the asset class name");
	var html = \'{assetclass get="\'+assetClassName+\'"}\';
	insertElement(html);
}

function insertElement(){
	var field = document.getElementById("tpl_textArea");
	field.focus();
	alert(field.value);
}

function toggleParamsHolder(){
  if(show_params_holder){
    new Effect.BlindUp(\'params-holder\', {duration: 0.6});
    show_params_holder = false;
    $(\'params-holder-toggle-link\').innerHTML = "Show Parameters";
  }else{
    new Effect.BlindDown(\'params-holder\', {duration: 0.6});
    show_params_holder = true;
    $(\'params-holder-toggle-link\').innerHTML = "Hide Parameters";
  }
}

function showUploader(){
	$(\'uploader\').style.display = \'block\';
	// new Effect.BlindDown(\'uploader\', {duration: 0.6});
	$(\'uploader_link\').style.display = \'none\';
	$(\'text_window\').style.display = \'none\';
	// new Effect.BlindUp(\'text_window\', {duration: 0.6});
	input_mode = \'upload\';
	$(\'input_mode\').value = input_mode;
	
}

function hideUploader(){
	$(\'uploader\').style.display = \'none\';
	// new Effect.BlindUp(\'uploader\', {duration: 0.6});
	$(\'uploader_link\').style.display = \'block\';
	$(\'text_window\').style.display = \'block\';
	// new Effect.BlindDown(\'text_window\', {duration: 0.6});
	input_mode = \'direct\';
	$(\'input_mode\').value = input_mode;
	$(\'tpl_textArea\').disabled = false;
	/* tinyMCE.init({
  	mode : "textareas",
  	theme : "advanced",
  	theme_advanced_buttons3_add_before : "tablecontrols,separator",
  	theme_advanced_toolbar_location : "top",
  	theme_advanced_resizing : true,
  	convert_fonts_to_spans : false
  });*/
}

function validateUploadSuffix(){
	
  if(input_mode == \'upload\'){
    
  }else{
    return true;
  }

}

'; ?>

</script>


<div id="work-area">
  
  <h3>Add a new file</h3>
  
  <form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/saveNewAsset" method="post" name="newAsset" enctype="multipart/form-data">
    
    <input type="hidden" name="asset_type" value="<?php echo $this->_tpl_vars['type_code']; ?>
" />
    <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
    <input type="hidden" name="input_mode" id="input_mode" value="<?php echo $this->_tpl_vars['starting_mode']; ?>
" />
    
    <?php echo smarty_function_load_interface(array('file' => $this->_tpl_vars['form_include']), $this);?>

    
    <?php if (! empty ( $this->_tpl_vars['params'] )): ?><a id="params-holder-toggle-link" href="javascript:toggleParamsHolder()">Show Parameters</a><?php endif; ?>
    
    <div id="params-holder" style="display:none">
    <?php $_from = $this->_tpl_vars['params']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['parameter_name'] => $this->_tpl_vars['parameter_value']):
?>
    <div class="edit-form-row">
      <div class="form-section-label"><?php echo $this->_tpl_vars['parameter_name']; ?>
</div>
      <input type="text" name="params[<?php echo $this->_tpl_vars['parameter_name']; ?>
]" style="width:250px" />
    </div>
    <?php endforeach; endif; unset($_from); ?>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Share this asset with other sites?</div>
      <input type="checkbox" name="asset_shared" /> Check here to allow all sites to use this file.
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Save">
      </div>
    </div>
    
  </form>
  
</div>

<div id="actions-area">

</div>