<?php /* Smarty version 2.6.18, created on 2007-12-01 15:02:33
         compiled from /var/www/html/System/Applications/Assets/Presentation/edit.default.tpl */ ?>

<h3>Edit File Parameters</h3>

<div class="instruction">You are editing <?php echo $this->_tpl_vars['asset']['type_info']['label']; ?>
: (<?php echo $this->_tpl_vars['asset']['url']; ?>
)</div>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateAsset" method="post" enctype="multipart/form-data">
  
  <input type="hidden" name="asset_id" value="<?php echo $this->_tpl_vars['asset']['id']; ?>
" />
  
  <div id="edit-form-layout">
    
  <?php $_from = $this->_tpl_vars['asset']['default_parameters']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['parameter_name'] => $this->_tpl_vars['parameter_value']):
?>
  <div class="edit-form-row">
    <div class="form-section-label"><?php echo $this->_tpl_vars['parameter_name']; ?>
</div>
    <input type="text" name="params[<?php echo $this->_tpl_vars['parameter_name']; ?>
]" value="<?php echo $this->_tpl_vars['parameter_value']; ?>
" style="width:250px" />
  </div>
  <?php endforeach; endif; unset($_from); ?>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save Changes" />
      <input type="button" onclick="cancelForm();" value="Done" />
    </div>
  </div>
  
  </div>
  
</form>