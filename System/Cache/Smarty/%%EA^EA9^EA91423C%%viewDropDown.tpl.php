<?php /* Smarty version 2.6.18, created on 2007-11-26 00:19:33
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Dropdowns/Presentation/viewDropDown.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dud_link', '/var/vsc/clients/claritycapital/smartest/System/Applications/Dropdowns/Presentation/viewDropDown.tpl', 67, false),)), $this); ?>
<script language="javascript" type="text/javascript">


</script>

<div id="work-area">

<h3>
<a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">DropDowns</a> &gt;<?php echo $this->_tpl_vars['dropdown_details']['label']; ?>
</h3>
<a name="top"></a>

<div class="instruction">Double click one of the model_ones to edit it or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="drop_down" id="drop_down" value="<?php echo $this->_tpl_vars['dropdown_details']['id']; ?>
" />
  <input type="hidden" name="drop_down_value_id" id="item_id_input" value="" />
</form>

<?php if (! empty ( $this->_tpl_vars['dropdown_options'] )): ?>
	<ul class="<?php if (count ( $this->_tpl_vars['dropdown_options'] ) > 10): ?>options-list<?php else: ?>options-grid<?php endif; ?>" id="<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options_list<?php else: ?>options_grid<?php endif; ?>">

	<?php $_from = $this->_tpl_vars['dropdown_options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['option']):
?>
 	 <li style="list-style:none;" ondblclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editDropDownValue?drop_down=<?php echo $this->_tpl_vars['dropdown_details']['id']; ?>
&drop_down_value_id=<?php echo $this->_tpl_vars['option']['id']; ?>
'">
 	 <a class="option" id="item_<?php echo $this->_tpl_vars['option']['id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['option']['id']; ?>
', 'fff');" >
 	 <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png">
  	<?php echo $this->_tpl_vars['option']['label']; ?>
</a></li>
	<?php endforeach; endif; unset($_from); ?>
	</ul>
<?php else: ?>
  <div class="instruction">This dropdown menu has no options yet.</div>
  <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addDropDownValue?drop_down=<?php echo $this->_tpl_vars['dropdown_details']['id']; ?>
'">Click here to add a new Drop Down Value</a>
<?php endif; ?>
</td>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
  <li class="permanent-action"><b>Selection Options</b></li>
  <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){workWithItem(\'editDropDownValue\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> Edit</a></li>
 <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deleteDropDownValue\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_delete.png"> Delete</a></li>
 
</ul>
<ul class="actions-list">
  <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addDropDownValue?drop_down=<?php echo $this->_tpl_vars['dropdown_details']['id']; ?>
'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Add DropDown value</a></li> 
<?php if ($this->_tpl_vars['content']['count']): ?>
<li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/reorderDropDownValue?drop_down=<?php echo $this->_tpl_vars['dropdown_details']['id']; ?>
'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> Re-order DropDownValues</a></li> 
<?php endif; ?>
  <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Drops Down</a></li>
</ul>

</div>