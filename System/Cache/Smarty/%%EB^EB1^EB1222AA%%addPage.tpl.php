<?php /* Smarty version 2.6.18, created on 2007-12-02 12:22:26
         compiled from /var/www/html/System/Applications/Pages/Presentation/addPage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/www/html/System/Applications/Pages/Presentation/addPage.tpl', 54, false),)), $this); ?>
<?php echo '

<script language="javascript">

function showUploader(){
	document.getElementById(\'tplUploader\').style.display = \'block\';
	document.getElementById(\'tplUploadShowButton\').style.display = \'none\';
	document.getElementById(\'addPageDetails\').style.display = \'none\';
	document.getElementById(\'add_type\').value = \'upload\';
}

function hideUploader(){
	document.getElementById(\'tplUploader\').style.display = \'none\';
	document.getElementById(\'tplUploadShowButton\').style.display = \'block\';
	document.getElementById(\'addPageDetails\').style.display = \'block\';
	document.getElementById(\'add_type\').value = \'direct\';
}

function check(){
	var editForm = document.getElementById(\'insertPage\');
	if(editForm.page_url.value==\'\'){
		alert (\'please enter the url\');
		editForm.page_url.focus();
		return false;
	}else{
		return true;
	}
}

function addField(input_id, field_name){
	
	var myInput = document.getElementById(input_id);
	
	if(myInput){
		
		myInput.focus();
		
		if(field_name == \'name\' || field_name == \'id\'){
			myInput.value = myInput.value+\':\'+field_name;
		}else{
			myInput.value = myInput.value+\'$\'+field_name;
		}
	}else{
		alert(\'input not found\');
	}
}

</script>

'; ?>


<div id="work-area">

<?php echo smarty_function_load_interface(array('file' => $this->_tpl_vars['_stage_template']), $this);?>


</div>