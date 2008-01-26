<?php /* Smarty version 2.6.18, created on 2007-11-25 23:13:52
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Dropdowns/Presentation/startPage.tpl */ ?>
<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; DropDowns</h3>
<a name="top"></a>

<div class="instruction">Your data is collected into functionally distinct types called DropDowns. Please choose one to continue.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="drop_down" id="item_id_input" value="" />
</form>

<ul class="<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options-list<?php else: ?>options-grid<?php endif; ?>" id="<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options_list<?php else: ?>options_grid<?php endif; ?>">
<?php $_from = $this->_tpl_vars['dropdowns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['dropdown']):
?>
  <li style="list-style:none;" ondblclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/viewDropDown?drop_down=<?php echo $this->_tpl_vars['dropdown']['dropdown_id']; ?>
'">
  <a class="option" id="item_<?php echo $this->_tpl_vars['dropdown']['dropdown_id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['dropdown']['dropdown_id']; ?>
');" >
  <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png">
  <?php echo $this->_tpl_vars['dropdown']['dropdown_label']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions">
  <li class="permanent-action"><b>Selection Options</b></li>
  <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){workWithItem(\'editDropDown\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> Edit</a></li>
 <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deleteDropDown\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_delete.png"> Delete</a></li>
 <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){workWithItem(\'viewDropDown\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> View DropDownValues</a></li>
</ul>
<ul class="actions-list">
  <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addDropDown'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Create DropDown</a></li>  
  <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
datamanager/'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_add.png"> Data Manager</a></li>
</ul>

</div>




