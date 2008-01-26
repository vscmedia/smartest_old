<?php /* Smarty version 2.6.18, created on 2007-11-28 06:35:17
         compiled from /var/www/html/System/Applications/Items/Presentation/getItemClassMembers.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'lower', '/var/www/html/System/Applications/Items/Presentation/getItemClassMembers.tpl', 32, false),array('modifier', 'escape', '/var/www/html/System/Applications/Items/Presentation/getItemClassMembers.tpl', 49, false),array('function', 'dud_link', '/var/www/html/System/Applications/Items/Presentation/getItemClassMembers.tpl', 41, false),)), $this); ?>
<script language="javascript" type="text/javascript">
<?php echo '

function exportItem(pageAction){
	
	var editForm = document.getElementById(\'pageViewForm\');
	
	if(selectedPage && editForm){
		'; ?>
		
		editForm.action="/<?php echo $this->_tpl_vars['section']; ?>
/"+pageAction; 
		<?php echo '
		editForm.submit();
	}
}

function openPage(pageAction){
	
	var editForm = document.getElementById(\'pageViewForm\');
	if(editForm){
'; ?>
		editForm.action="/<?php echo $this->_tpl_vars['section']; ?>
/"+pageAction+"?item_id=";<?php echo '
		editForm.submit();
	}
}

'; ?>

</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Data Manager</a> &gt; <?php echo $this->_tpl_vars['model']['plural_name']; ?>
</h3>
<a name="top"></a>
<div class="instruction">Double click one of the <?php echo ((is_array($_tmp=$this->_tpl_vars['content']['itemBaseValues']['itemclass_plural_name'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
 to edit it or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="item_id" id="item_id_input" value="" />
  <input type="hidden" name="class_id" value="<?php echo $this->_tpl_vars['model']['id']; ?>
" />
</form>

<div id="options-view-chooser">
Found <?php echo $this->_tpl_vars['num_items']; ?>
 <?php if ($this->_tpl_vars['num_items'] != 1): ?><?php echo $this->_tpl_vars['model']['plural_name']; ?>
<?php else: ?><?php echo $this->_tpl_vars['model']['name']; ?>
<?php endif; ?>. View as:
<a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="setView('list', 'options_list')">List</a> /
<a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="setView('grid', 'options_list')">Icons</a>
</div>
  
  <ul class="<?php if (count ( $this->_tpl_vars['items'] ) > 30): ?>options-list<?php else: ?>options-grid<?php endif; ?>" id="options_list">
  <?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
	
    <li ondblclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/openItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
'">
      <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" class="option" id="item_<?php echo $this->_tpl_vars['item']['id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['item']['id']; ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['item']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
');">
        
        <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/item.png" border="0" /><?php echo $this->_tpl_vars['item']['name']; ?>
</a><?php if ($this->_tpl_vars['item']['public'] == 'FALSE'): ?>&nbsp;(hidden)<?php endif; ?></li>

  <?php endforeach; endif; unset($_from); ?>
  
    </ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected <?php echo $this->_tpl_vars['content']['itemBaseValues']['itemclass_name']; ?>
</b></li>
  <li class="permanent-action"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/pencil.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('openItem');">Edit Properties</a></li>
  <li class="permanent-action"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/lock_open.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('releaseItem');">Release</a></li>
  <li class="permanent-action"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="workWithItem('publishItem');">Publish</a></li>
  <li class="permanent-action"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_delete.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="if(selectedPage && confirm('Are you sure you want to delete this <?php echo ((is_array($_tmp=$this->_tpl_vars['model']['name'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
 ?')) {workWithItem('deleteItem');}">Delete</a></li>

  <!--<tr style="height:25px"><td class="text"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="<?php echo 'if(selectedPage){workWithItem(\'getItemXml\');}'; ?>
">Export</a></td></tr>-->
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Model Options</b></li>
  <li class="permanent-action"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addItem?class_id=<?php echo $this->_tpl_vars['model']['id']; ?>
'">Add a New <?php echo $this->_tpl_vars['model']['name']; ?>
</a></li>
  <li class="permanent-action"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/releaseUserHeldItems?class_id=<?php echo $this->_tpl_vars['model']['id']; ?>
'">Release all <?php echo $this->_tpl_vars['model']['plural_name']; ?>
</a></li>
  <li class="permanent-action"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_small.png"> <a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editModel?class_id=<?php echo $this->_tpl_vars['model']['id']; ?>
'">Get Model Info</a></li>
</ul>

</div>