<?php /* Smarty version 2.6.18, created on 2007-12-01 11:30:33
         compiled from /var/www/html/System/Applications/Templates/Presentation/containerTemplates.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dud_link', '/var/www/html/System/Applications/Templates/Presentation/containerTemplates.tpl', 32, false),)), $this); ?>
<div id="work-area">

<h3>Container Templates</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="type" value="SM_CONTAINER_TEMPLATE" />
  <input type="hidden" name="template_id" id="item_id_input" value="" />
</form>

<div id="options-view-chooser">
Found  assets. View as:
<a href="javascript:nothing()" onclick="setView('list', 'options_grid')">List</a> /
<a href="javascript:nothing()" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-grid" style="margin-top:0px" id="options_grid">
<?php $_from = $this->_tpl_vars['assetList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['asset']):
?>
<li>
    <a href="javascript:nothing()" class="option" id="item_<?php echo $this->_tpl_vars['asset']['asset_id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['asset']['asset_id']; ?>
');" >
    <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page.png" /><?php echo $this->_tpl_vars['asset']['asset_stringid']; ?>
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
	<li class="disabled-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('editTemplate');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Edit This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage && confirm(\'Really delete this template?\')){ workWithItem(\'deleteTemplate\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt=""> Delete This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="workWithItem('duplicateTemplate');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Duplicate This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="workWithItem('downloadTemplate');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Download This Template</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li><b>Template Options</b></li>
	<li class="disabled-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addTemplate?type=SM_CONTAINER_TEMPLATE" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt=""> Add Another Template</a></li>
</ul>

</div>