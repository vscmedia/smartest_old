<?php /* Smarty version 2.6.18, created on 2007-12-03 18:13:54
         compiled from /var/www/html/System/Applications/Templates/Presentation/editTemplate.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dud_link', '/var/www/html/System/Applications/Templates/Presentation/editTemplate.tpl', 32, false),)), $this); ?>
<div id="work-area">

<h3><?php echo $this->_tpl_vars['interface_title']; ?>
</h3>

<?php if ($this->_tpl_vars['show_form']): ?>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateTemplate" method="post" name="newTemplate" enctype="multipart/form-data">
  
  <input type="hidden" name="type" value="<?php echo $this->_tpl_vars['template_type']; ?>
" />
  <input type="hidden" name="filename" value="<?php echo $this->_tpl_vars['template_name']; ?>
" />
  <?php if ($this->_tpl_vars['template_type'] == 'SM_CONTAINER_TEMPLATE'): ?><input type="hidden" name="template_id" value="<?php echo $this->_tpl_vars['template_id']; ?>
" /><?php endif; ?>
  
  <div style="width:100%" id="editTMPL">
    Template Filename: <?php echo $this->_tpl_vars['template_name']; ?>

    <textarea name="template_content" id="tpl_textArea" style="display:block"><?php echo $this->_tpl_vars['template_content']; ?>
</textarea>
  </div>
  
  <div class="buttons-bar">
    <input type="submit" value="Save Changes" />
    <input type="button" onclick="cancelForm();" value="Done" />
  </div>
  
</form>

<?php endif; ?>

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <?php if ($this->_tpl_vars['template_type'] == 'SM_CONTAINER_TEMPLATE'): ?><li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/containerTemplates'">Back to container temnplates</a></li><?php endif; ?>
    <?php if ($this->_tpl_vars['template_type'] == 'SM_LISTITEM_TEMPLATE'): ?><li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/listItemTemplates'">Back to list item temnplates</a></li><?php endif; ?>
    <?php if ($this->_tpl_vars['template_type'] == 'SM_MASTER_TEMPLATE'): ?><li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/masterTemplates'">Back to master temnplates</a></li><?php endif; ?>
  </ul>
</div>