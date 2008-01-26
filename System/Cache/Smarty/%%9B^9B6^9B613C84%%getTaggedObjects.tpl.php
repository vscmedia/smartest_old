<?php /* Smarty version 2.6.18, created on 2007-12-04 08:29:14
         compiled from /var/www/html/System/Applications/MetaData/Presentation/getTaggedObjects.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/var/www/html/System/Applications/MetaData/Presentation/getTaggedObjects.tpl', 8, false),array('function', 'dud_link', '/var/www/html/System/Applications/MetaData/Presentation/getTaggedObjects.tpl', 21, false),)), $this); ?>
<div id="work-area">

  <h3>Tagged Items: <?php echo $this->_tpl_vars['tag_name']; ?>
</h3>
  
  <?php if (empty ( $this->_tpl_vars['objects'] )): ?>
  <div class="instruction">No items or pages are tagged with "<?php echo $this->_tpl_vars['tag_name']; ?>
" on this site.</div>
  <?php else: ?>
  <div class="instruction"><?php echo ((is_array($_tmp=$this->_tpl_vars['objects'])) ? $this->_run_mod_handler('count', true, $_tmp) : count($_tmp)); ?>
 objects have ben tagged with "<?php echo $this->_tpl_vars['tag_name']; ?>
".</div>
  <ul>
    <?php $_from = $this->_tpl_vars['objects']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object']):
?>
    <li><?php echo $this->_tpl_vars['object']['type']; ?>
: <a href="<?php echo $this->_tpl_vars['object']['url']; ?>
"><?php echo $this->_tpl_vars['object']['title']; ?>
</a> (<a href="<?php if ($this->_tpl_vars['object']['type'] == 'Page'): ?><?php echo $this->_tpl_vars['domain']; ?>
websitemanager/editPage?page_id=<?php echo $this->_tpl_vars['object']['webid']; ?>
<?php else: ?><?php echo $this->_tpl_vars['domain']; ?>
datamanager/editItem?item_id=<?php echo $this->_tpl_vars['object']['id']; ?>
<?php endif; ?>">edit</a>) </li>
    <?php endforeach; endif; unset($_from); ?>
  </ul>
  <?php endif; ?>

</div>

<div id="actions-area">
  <ul id="non-specific-actions" class="actions-list">
    <li><strong>Options</strong></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
smartest/tags'"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" alt="tick">Go back to tags</a></li>
  </ul>
</div>