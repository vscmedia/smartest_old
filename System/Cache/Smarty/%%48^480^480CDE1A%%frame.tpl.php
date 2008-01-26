<?php /* Smarty version 2.6.18, created on 2007-11-25 20:38:11
         compiled from /var/vsc/clients/claritycapital/smartest/System/Presentation/Backend/frame.tpl */ ?>
<div id="user-message-container">
  <?php $_from = $this->_tpl_vars['sm_messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['messages'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['messages']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['message']):
        $this->_foreach['messages']['iteration']++;
?>
  <div class="user-message" id="user_message_<?php echo $this->_foreach['messages']['iteration']; ?>
">
    <input type="button" value="OK" class="user-message-dismiss" onclick="hideUserMessage('user_message_<?php echo $this->_foreach['messages']['iteration']; ?>
');" />
    <?php echo $this->_tpl_vars['message']->getMessage(); ?>

  </div>
  <?php endforeach; endif; unset($_from); ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['sm_navigation'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['sm_interface'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>