<?php /* Smarty version 2.6.18, created on 2007-12-01 17:15:50
         compiled from /var/www/html/System/Applications/Sets/Presentation/previewSet.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dud_link', '/var/www/html/System/Applications/Sets/Presentation/previewSet.tpl', 16, false),array('modifier', 'escape', '/var/www/html/System/Applications/Sets/Presentation/previewSet.tpl', 22, false),)), $this); ?>
<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
datamanager">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Sets</a> &gt; <?php echo $this->_tpl_vars['set']['name']; ?>
 </h3>
<a name="top"></a>

<div class="instruction">Items in this set:</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id"  value="<?php echo $this->_tpl_vars['set']['id']; ?>
" />
  <input type="hidden" name="item_id" id="item_id_input" value="" />
</form>

<div class="instruction">Found <?php echo $this->_tpl_vars['count']; ?>
 item<?php if ($this->_tpl_vars['count'] != 1): ?>s<?php endif; ?> in this data set</div>

View as:
<a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="setView('list', '<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options_list<?php else: ?>options_grid<?php endif; ?>')">List</a> /
<a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="setView('grid', '<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options_list<?php else: ?>options_grid<?php endif; ?>')">Icons</a>
  
  <ul class="<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options-list<?php else: ?>options-grid<?php endif; ?>" id="<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options_list<?php else: ?>options_grid<?php endif; ?>">
  <?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <li>
      <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" class="option" id="item_<?php echo $this->_tpl_vars['item']['id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['item']['id']; ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['item']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
');">
        <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/item.png"><?php echo $this->_tpl_vars['item']['name']; ?>
</a></li>
  <?php endforeach; endif; unset($_from); ?>
  </ul>

</div>

<div id="actions-area">
    
    <ul class="actions-list" id="item-specific-actions" style="display:none">
      <li><b>Selected Item</b></li>
      <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
datamanager/editItem?item_id='+selectedPage"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/model.png" style="width:16px;height:18px"> Edit Properties</a></li>	
    </ul>
    
    <ul class="actions-list">
      <li><b>Options</b></li>
      <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
smartest/sets'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/folder.png" style="width:16px;height:18px"> Back to Data Sets</a></li>
    	<li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
smartest/models'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/model.png" style="width:16px;height:18px"> Go to Models</a></li>	
    </ul>    
</div>