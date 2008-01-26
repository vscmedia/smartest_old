<?php /* Smarty version 2.6.18, created on 2007-11-26 02:16:08
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Templates/Presentation/editTemplate.tpl */ ?>
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

</div>