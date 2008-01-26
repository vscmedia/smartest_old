<?php /* Smarty version 2.6.18, created on 2007-11-26 00:21:53
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Dropdowns/Presentation/editDropDownValue.tpl */ ?>
<script language="javascript" type="text/javascript">
<?php echo '

function check(){
	
	var editForm = document.getElementById(\'pageViewForm\');
	
	if(editForm.drop_down_value.value==\'\'){
		alert (\'please enter the DropDown Vaule\');
		editForm.drop_down_value.focus();
		return false;
	}else if(editForm.drop_down_order.value==\'\'){
		alert (\'please enter the Order\');
		editForm.drop_down_order.focus();
		return false;
	}else if(isNaN(editForm.drop_down_order.value)){
		alert (\'Invalid data format.\\nOnly numbers are allowed\');
		editForm.drop_down_order.select();
		return false;
	}else{
		return true;
	}
}

'; ?>

</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">DropDowns</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/viewDropDown?drop_down=<?php echo $this->_tpl_vars['dropdown_details']['dropdown_id']; ?>
"><?php echo $this->_tpl_vars['dropdown_details']['dropdown_label']; ?>
</a> &gt; Edit DropDownValue</h3>

<a name="top"></a>

<form id="pageViewForm" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateDropDownValue"  onsubmit="return check();">

<input type="hidden" name="drop_down_id" value="<?php echo $this->_tpl_vars['dropdown_details']['dropdown_id']; ?>
" />
<input type="hidden" name="drop_down_value_id" value="<?php echo $this->_tpl_vars['value_details']['dropdownvalue_dropdown_id']; ?>
" />

<div class="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Label:</div>
    <input type="text" name="drop_down_value" id="drop_down_value" value="<?php echo $this->_tpl_vars['value_details']['dropdownvalue_label']; ?>
">
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Order:</div>
    <input type="text" name="drop_down_order" id="drop_down_order" value="<?php echo $this->_tpl_vars['value_details']['dropdownvalue_order']; ?>
">
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onlick="cancelForm();">
      <input type="submit" value="Save">
    </div>
  </div>

</div>

</form>

</div>