<?php /* Smarty version 2.6.18, created on 2007-12-04 21:45:20
         compiled from /var/www/html/System/Applications/Items/Presentation/itemTags.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/www/html/System/Applications/Items/Presentation/itemTags.tpl', 3, false),array('function', 'dud_link', '/var/www/html/System/Applications/Items/Presentation/itemTags.tpl', 35, false),)), $this); ?>
<div id="work-area">
  
  <?php echo smarty_function_load_interface(array('file' => "editItem.tabs.tpl"), $this);?>

  
  <h3>Tags</h3>
  <div class="instruction">Choose which tags this item is attached to. Some pags may not make sense for certain sites, but they can be ignored.</div>
  
  <form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateItemTags" method="post">
    
    <input type="hidden" name="item_id" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />
    
    <ul class="basic-list">
      <?php $_from = $this->_tpl_vars['tags']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tag']):
?>
      <li><input type="checkbox" name="tags[<?php echo $this->_tpl_vars['tag']['id']; ?>
]" id="tag_<?php echo $this->_tpl_vars['tag']['id']; ?>
"<?php if ($this->_tpl_vars['tag']['attached']): ?> checked="checked"<?php endif; ?> /><label for="tag_<?php echo $this->_tpl_vars['tag']['id']; ?>
"><?php echo $this->_tpl_vars['tag']['label']; ?>
</label></li>
      <?php endforeach; endif; unset($_from); ?>
    </ul>
  
    <div id="edit-form-layout">
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" name="action" value="Save" />
        </div>
      </div>
    </div>
  
  </form>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tagging Options</b></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
metadata/addTag'"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>


  <ul class="actions-list" id="non-specific-actions">
    <li><b>This <?php echo $this->_tpl_vars['item']['_model']['name']; ?>
</b></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/releaseItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
';" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/lock_open.png" border="0" />&nbsp;Release for others to edit</a></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/approveItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
';" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" />&nbsp;Approve changes</a></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/publishItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
';" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_lightning.png" border="0" />&nbsp;Publish it</a></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassMembers?class_id=<?php echo $this->_tpl_vars['item']['itemclass_id']; ?>
';" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tick.png" border="0" />&nbsp;Finish editing for now</a></li>
  </ul>

</div>