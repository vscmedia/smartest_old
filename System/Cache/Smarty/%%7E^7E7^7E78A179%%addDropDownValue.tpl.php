<?php /* Smarty version 2.6.18, created on 2007-12-02 12:53:08
         compiled from /var/www/html/System/Applications/Dropdowns/Presentation/addDropDownValue.tpl */ ?>
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

<h3>

<a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">DropDowns</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/viewDropDown?drop_down=<?php echo $this->_tpl_vars['dropdown_details']['dropdown_id']; ?>
"><?php echo $this->_tpl_vars['dropdown_details']['dropdown_label']; ?>
</a> &gt; Add DropDownValue</h3>


<form id="pageViewForm" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/insertDropDownValue" onsubmit="return check();">

<input type="hidden" name="dropdown_id" value="<?php echo $this->_tpl_vars['dropdown_details']['dropdown_id']; ?>
" />

<div class="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Label:</div>
    <input type="text" name="dropdownvalue_label" id="drop_down_label" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Value:</div>
    <input type="text" name="dropdownvalue_value" id="drop_down_value" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Order:</div>
    <input type="text" name="dropdownvalue_order" id="drop_down_order" />
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onlick="cancelForm();" />
      <input type="submit" value="Save" />
    </div>
  </div>

</div>		

</form>

</div>