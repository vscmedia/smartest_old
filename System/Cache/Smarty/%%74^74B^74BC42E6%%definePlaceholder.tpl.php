<?php /* Smarty version 2.6.18, created on 2007-12-01 18:10:37
         compiled from /var/www/html/System/Applications/Pages/Presentation/definePlaceholder.tpl */ ?>
<div id="work-area">
  
  <h3>Define Placeholder</h3>
  
  <form id="pageViewForm" method="post" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updatePlaceholderDefinition">
    <input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page']['id']; ?>
" />
    <input type="hidden" name="placeholder_id" value="<?php echo $this->_tpl_vars['placeholder']['id']; ?>
" />
        
    <div class="edit-form-row">
      <div class="form-section-label">Choose a file to define this placeholder with</div>
      <select name="asset_id">
        <?php $_from = $this->_tpl_vars['assets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['asset']):
?>
          <option value="<?php echo $this->_tpl_vars['asset']['id']; ?>
"<?php if ($this->_tpl_vars['asset']['id'] == $this->_tpl_vars['draft_asset_id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['asset']['stringid']; ?>
<?php if ($this->_tpl_vars['asset']['id'] == $this->_tpl_vars['live_asset_id']): ?> (live)<?php endif; ?></option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
    </div>
    
    <?php $_from = $this->_tpl_vars['params']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['parameter_name'] => $this->_tpl_vars['parameter_value']):
?>
    <div class="edit-form-row">
      <div class="form-section-label"><?php echo $this->_tpl_vars['parameter_name']; ?>
</div>
      <input type="text" name="params[<?php echo $this->_tpl_vars['parameter_name']; ?>
]" style="width:250px" value="<?php echo $this->_tpl_vars['parameter_value']; ?>
" />
    </div>
    <?php endforeach; endif; unset($_from); ?>
    
    
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save Changes" />
      <input type="button" onclick="cancelForm();" value="Cancel" />
    </div>
  </div>
  
  </form>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected Asset</b></li>
    <li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){workWithItem(\'updatePlaceholderDefinition\');}'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" alt=""> Use This Asset</a></li>
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/pageAssets?page_id=<?php echo $this->_tpl_vars['page']['id']; ?>
'" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/cross.png" border="0" alt=""> Cancel</a></li>
  </ul>
  
</div>