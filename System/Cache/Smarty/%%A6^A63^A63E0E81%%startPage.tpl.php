<?php /* Smarty version 2.6.18, created on 2007-12-01 17:17:32
         compiled from /var/www/html/System/Applications/Sets/Presentation/startPage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'lower', '/var/www/html/System/Applications/Sets/Presentation/startPage.tpl', 18, false),)), $this); ?>
<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/data">Data Manager</a> &gt; Data Sets</h3>

<div class="instruction">Use Data Sets to organize your data into smaller groups.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id" id="item_id_input" value="" />
</form>


<ul class="<?php if ($this->_tpl_vars['content']['itemClassMemberCount'] > 10): ?>options-list<?php else: ?>options-grid<?php endif; ?>" id="<?php if ($this->_tpl_vars['content']['itemClassMemberCount'] > 10): ?>options_list<?php else: ?>options_grid<?php endif; ?>">
<?php $_from = $this->_tpl_vars['sets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['set']):
?>
  <li style="list-style:none;" 
			ondblclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editSet?set_id=<?php echo $this->_tpl_vars['set']['id']; ?>
'">
			<a class="option" id="item_<?php echo $this->_tpl_vars['set']['id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['set']['id']; ?>
', 'fff');" >
			  <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/folder.png">
			  <?php echo $this->_tpl_vars['set']['name']; ?>
 (<?php echo ((is_array($_tmp=$this->_tpl_vars['set']['type'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
)</a></li>
<?php endforeach; endif; unset($_from); ?>
</ul>
</div>

<div id="actions-area">
  
<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected Data Set</b></li>
  <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){workWithItem(\'editSet\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/folder_edit.png"> Edit Contents</a></li>
  <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){workWithItem(\'previewSet\');}'; ?>
" ><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/folder_go.png"> View Contents</a></li>
  <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deleteSet\');}'; ?>
" ><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/folder_delete.png"> Delete</a></li>
</ul>

<ul class="actions-list">
  <li><b>Data Options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addSet'"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/folder_add.png"> Create A New Data Set</a></li>  
  <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/models"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package.png" style="width:16px;height:18px"> Browse Data in Models</a></li>
  </ul>

</div>



