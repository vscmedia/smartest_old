<?php /* Smarty version 2.6.18, created on 2007-12-02 12:52:56
         compiled from /var/www/html/System/Applications/Dropdowns/Presentation/editDropDown.tpl */ ?>
<script language="javascript" type="text/javascript">
<?php echo '

function check(){
	var editForm = document.getElementById(\'pageViewForm\');
	if(editForm.drop_down.value==\'\'){
		alert (\'please enter the DropDown Label\');
		editForm.drop_down.focus();
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
">DropDowns</a> &gt; Edit Drop Down </h3>

<a name="top"></a>



<form id="pageViewForm" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateDropDown" onsubmit="return check();">
  
  <div id="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Label: </div>
      <input type="text" name="drop_down" id="drop_down" value="<?php echo $this->_tpl_vars['dropdown_details']['dropdown_label']; ?>
">
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit"  value="Save">
      </div>
    </div>

  </div>
  
  <input type="hidden" name="drop_down_id" value="<?php echo $this->_tpl_vars['dropdown_details']['dropdown_id']; ?>
" />

</form>

</div>