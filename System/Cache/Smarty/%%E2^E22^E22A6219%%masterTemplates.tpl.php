<?php /* Smarty version 2.6.18, created on 2007-12-04 07:14:41
         compiled from /var/www/html/System/Applications/Templates/Presentation/masterTemplates.tpl */ ?>
<div id="work-area">

<h3>Page Master Templates</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="type" value="SM_PAGE_MASTER_TEMPLATE" />
  <input type="hidden" name="template_name" id="item_id_input"  value="" />
</form>



<div id="options-view-chooser">
Found templates. View as:
<a href="javascript:nothing()" onclick="setView('list', 'options_grid')">List</a> /
<a href="javascript:nothing()" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-grid" style="margin-top:0px" id="options_grid">
<?php $_from = $this->_tpl_vars['templateList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['template']):
?>
<li>
  <a href="javascript:nothing()" class="option" id="item_<?php echo $this->_tpl_vars['template']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['template']; ?>
');">
    <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page.png" /><?php echo $this->_tpl_vars['template']; ?>
</a>
</li>
<?php endforeach; endif; unset($_from); ?>
</ul>

<?php if ($this->_tpl_vars['error']): ?><?php echo $this->_tpl_vars['error']; ?>
<?php endif; ?>
</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
    
  <li><b>Selected Template:</b></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage){ workWithItem(\'editTemplate\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Edit This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage && confirm(\'Really delete this template?\')){ workWithItem(\'deleteTemplate\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt=""> Delete This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage){ workWithItem(\'duplicateTemplate\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Duplicate This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage){ workWithItem(\'downloadTemplate\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Download This Template</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Template Options</b></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="workWithItem('addTemplate');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt=""> Add Another Master Template</a></li>
</ul>

</div>