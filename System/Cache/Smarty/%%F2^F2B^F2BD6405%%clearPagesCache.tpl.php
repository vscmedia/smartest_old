<?php /* Smarty version 2.6.18, created on 2007-11-26 00:33:12
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/clearPagesCache.tpl */ ?>
<div id="work-area">
  
  <h3>Clear Pages Cache</h3>
  
  <?php if ($this->_tpl_vars['show_result']): ?>
  
  <div class="instruction">Result:</div>
  
  <ul class="basic-list">
    <?php $_from = $this->_tpl_vars['deleted_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['file']):
?>
      <li>Deleted: <?php echo $this->_tpl_vars['cache_path']; ?>
<?php echo $this->_tpl_vars['file']; ?>
</li>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['failed_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['file']):
?>
      <li>Failed to Delete: <?php echo $this->_tpl_vars['cache_path']; ?>
<?php echo $this->_tpl_vars['file']; ?>
</li>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['untouched_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['file']):
?>
      <li>Left Alone: <?php echo $this->_tpl_vars['cache_path']; ?>
<?php echo $this->_tpl_vars['file']; ?>
</li>
    <?php endforeach; endif; unset($_from); ?>
  </ul>
  
  <?php endif; ?>
  
</div>