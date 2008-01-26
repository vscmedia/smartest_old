<?php /* Smarty version 2.6.18, created on 2008-01-17 12:22:12
         compiled from /var/www/html/System/Applications/MetaData/Presentation/viewPageFieldDefinitions.tpl */ ?>
<div id="work-area">
  <h3>Definitions of page field: <?php echo $this->_tpl_vars['field']['label']; ?>
 (<?php echo $this->_tpl_vars['field']['name']; ?>
)</h3>
  <ul class="basic-list">
    <?php $_from = $this->_tpl_vars['definitions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['def']):
?>
    <li><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page.png" />&nbsp;Page:&nbsp;<b><?php echo $this->_tpl_vars['def']['page_title']; ?>
</b>:&nbsp;Draft Value: <b><?php echo $this->_tpl_vars['def']['pagepropertyvalue_draft_value']; ?>
</b><?php if ($this->_tpl_vars['def']['pagepropertyvalue_live_value']): ?>&nbsp;Live Value: <b><?php echo $this->_tpl_vars['def']['pagepropertyvalue_live_value']; ?>
</b><?php endif; ?></li>
    <?php endforeach; endif; unset($_from); ?>
  </ul>
</div>