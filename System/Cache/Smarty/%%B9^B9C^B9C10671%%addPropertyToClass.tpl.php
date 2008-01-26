<?php /* Smarty version 2.6.18, created on 2007-12-04 12:16:35
         compiled from /var/www/html/System/Applications/Items/Presentation/addPropertyToClass.tpl */ ?>
<script type="text/javascript">
var customVarName = false;
<?php echo '
function checkPrepertyType(){
// alert(document.getElementById(\'itemproperty_datatype\').value);
	switch(document.getElementById(\'itemproperty_datatype\').value){
		case \'1\':
			document.getElementById(\'default-value-text\').style.display = "block";
			document.getElementById(\'default-value-longtext\').style.display = "none";
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "none";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		case \'2\':
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "block";	
			document.getElementById(\'type-description\').style.display = "block";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "none";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		case \'3\':
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "none";	
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "block";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "none";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		case \'4\':
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "none";	
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "none";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "block";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		case \'5\':
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "none";	
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "block";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "none";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		case \'6\':
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "none";	
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "block";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "none";			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		case \'7\':
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "none";	
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "block";
			document.getElementById(\'default-value-model\').style.display = "none";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		case \'8\':
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "none";	
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "block";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
		default:
			document.getElementById(\'default-value-text\').style.display = "none";
			document.getElementById(\'default-value-longtext\').style.display = "none";
			document.getElementById(\'type-description\').style.display = "none";
			document.getElementById(\'default-value-bool\').style.display = "none";
			document.getElementById(\'default-value-url\').style.display = "none";
			document.getElementById(\'default-value-date\').style.display = "none";
			document.getElementById(\'default-value-file\').style.display = "none";
			document.getElementById(\'default-value-model\').style.display = "none";
			document.getElementById(\'default-value-dropdownMenu\').style.display = "none";
			document.getElementById(\'default-value-dropdown\').style.display = "none";
			break;
	}
}

function setVarName(){
	if(document.getElementById(\'itemproperty_varname\').value.length < 1){customVarName = false}
	
	var propertyName = document.getElementById(\'itemproperty_name\').value;
	
	if(!customVarName){
		document.getElementById(\'itemproperty_varname\').value = smartest.toVarName(propertyName);
	}
}
'; ?>

</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassMembers?class_id=<?php echo $this->_tpl_vars['model']['id']; ?>
"><?php echo $this->_tpl_vars['model']['plural_name']; ?>
</a> &gt; Add Property</h3>

<div class="instruction">You will be adding a new property to the model "imports".</div>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertItemClassProperty" method="post" enctype="multipart/form-data">
  <input type="hidden" name="class_id" value="<?php echo $this->_tpl_vars['model']['id']; ?>
" />

  <div class="edit-form-row">
      <div class="form-section-label">Property Name</div>
      <input type="text" value="" name="itemproperty_name" id="itemproperty_name" />
      <span class="help-text">properties must be three characters or longer and start with a letter.</span>
  </div>
  
  <div class="edit-form-row">
      <div class="form-section-label">Property Type</div>
      <select name="itemproperty_datatype" id='itemproperty_datatype' onchange="">
  	    <?php $_from = $this->_tpl_vars['data_types']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['data_type']):
?>
  	    <option value="<?php echo $this->_tpl_vars['data_type']['id']; ?>
"<?php if ($this->_tpl_vars['data_type']['id'] == $this->_tpl_vars['property']['datatype']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['data_type']['label']; ?>
</option>
  	    <?php endforeach; endif; unset($_from); ?>
      </select>
  </div>

 
    <div class="edit-form-row">
        <div class="form-section-label">Property Required:</div>
        <input type="checkbox" name="itemproperty_required" id="is-required" value="TRUE" /><label for="is-required">Check if this property is required</label>
    </div>
    
    <div class="edit-form-row">
        <div class="buttons-bar">
            Continue to: <select name="continue"><option value="PROPERTIES">View other properties of model <?php echo $this->_tpl_vars['model']['name']; ?>
</option><option value="NEW_PROPERTY">Add another property to model <?php echo $this->_tpl_vars['model']['name']; ?>
</option></select>
            <input type="button" value="Cancel" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassProperties?class_id=<?php echo $this->_tpl_vars['model']['id']; ?>
';" />
            <input type="submit" value="Save Property" />
        </div>
    </div>

</form>

</div>