<?php /* Smarty version 2.6.18, created on 2007-12-04 20:58:26
         compiled from /var/www/html/System/Applications/Items/Presentation/editModel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'camelcase', '/var/www/html/System/Applications/Items/Presentation/editModel.tpl', 18, false),)), $this); ?>
<div id="work-area">
  
  <h3>Edit Model</h3>
  
  <form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateModel" method="post">
    
    <input type="hidden" name="class_id" value="<?php echo $this->_tpl_vars['model']['id']; ?>
" />
    
    <div class="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Model Name</div>
        <?php echo $this->_tpl_vars['model']['name']; ?>

      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Model Class</div>
        Library/ObjectModel/<?php echo ((is_array($_tmp=$this->_tpl_vars['model']['name'])) ? $this->_run_mod_handler('camelcase', true, $_tmp) : smarty_modifier_camelcase($_tmp)); ?>
.class.php
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Model Plural Name</div>
        <input type="text" name="itemclass_plural_name" value="<?php echo $this->_tpl_vars['model']['plural_name']; ?>
" style="width:250px" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Default Meta-Page</div>
        <select name="itemclass_default_metapage_id">
          <?php if (! $this->_tpl_vars['model']['default_metapage_id']): ?><option value="0"></option><?php endif; ?>
          <?php $_from = $this->_tpl_vars['metapages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
          <option value="<?php echo $this->_tpl_vars['page']['id']; ?>
"<?php if ($this->_tpl_vars['model']['default_metapage_id'] == $this->_tpl_vars['page']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['page']['title']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Description Property</div>
        <select name="itemclass_default_description_property_id">
          <?php if (! $this->_tpl_vars['model']['default_description_property_id']): ?><option value="0"></option><?php endif; ?>
          <?php $_from = $this->_tpl_vars['description_properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['property']):
?>
          <option value="<?php echo $this->_tpl_vars['property']['id']; ?>
"<?php if ($this->_tpl_vars['model']['default_description_property_id'] == $this->_tpl_vars['property']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['property']['name']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>
      </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm()" />
          <input type="submit" value="Save Changes" />
        </div>
      </div>
      
    </div>
    
  </form>
  
</div>

<div id="actions-area">
  
</div>