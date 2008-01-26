<?php /* Smarty version 2.6.18, created on 2007-11-25 20:56:28
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/editItem.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/editItem.tpl', 3, false),array('function', 'item_field', '/var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/editItem.tpl', 48, false),array('function', 'dud_link', '/var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/editItem.tpl', 68, false),)), $this); ?>
<div id="work-area">

<?php echo smarty_function_load_interface(array('file' => "editItem.tabs.tpl"), $this);?>


<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassMembers?class_id=<?php echo $this->_tpl_vars['item']['_model']['id']; ?>
"><?php echo $this->_tpl_vars['item']['_model']['plural_name']; ?>
</a> &gt; Edit <?php echo $this->_tpl_vars['item']['_model']['name']; ?>
</h3>

<div id="instruction">You are editing the draft property values of this item</div>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateItem" enctype="multipart/form-data" method="post">

<input type="hidden" name="class_id" value="<?php echo $this->_tpl_vars['item']['_model']['id']; ?>
" />
<input type="hidden" name="item_id" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />

<div class="edit-form-row">
  <div class="form-section-label"><?php echo $this->_tpl_vars['item']['_model']['name']; ?>
 Name</div>
  <input type="text" name="item_name" value="<?php echo $this->_tpl_vars['item']['name']; ?>
" style="width:250px" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Status</div>
  <?php if ($this->_tpl_vars['item']['public'] == 'TRUE'): ?>
    Live <input type="button" value="Re-Publish" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/publishItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
'" />&nbsp;<input type="button" value="Un-Publish" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/unpublishItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
'" />
  <?php else: ?>
    Not Published <input type="button" value="Publish" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/publishItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
'" />
  <?php endif; ?>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Meta-Page</div>
  <select name="item_metapage_id">
    <?php if ($this->_tpl_vars['item']['_model']['default_metapage_id']): ?><option value="0">Model Default</option><?php endif; ?>
    <?php $_from = $this->_tpl_vars['metapages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
    <option value="<?php echo $this->_tpl_vars['page']['id']; ?>
"<?php if ($this->_tpl_vars['item']['metapage_id'] == $this->_tpl_vars['page']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['page']['title']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Search Terms</div>
  <textarea name="item_search_field" rows="3" cols="20" style="width:350px;height:60px"><?php echo $this->_tpl_vars['item']['search_field']; ?>
</textarea>
</div>

<?php $_from = $this->_tpl_vars['item']['_properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pid'] => $this->_tpl_vars['property']):
?>

<div class="edit-form-row">
  <?php echo smarty_function_item_field(array('property' => $this->_tpl_vars['property'],'value' => $this->_tpl_vars['item'][$this->_tpl_vars['pid']]), $this);?>

</div>

<?php endforeach; endif; unset($_from); ?>

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassMembers?class_id=<?php echo $this->_tpl_vars['item']['_model']['id']; ?>
';" value="Cancel" />
    <input type="submit" value="Save Changes" />
  </div>
</div>

</form>

</div>

<div id="actions-area">

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
    <?php if ($this->_tpl_vars['default_metapage_id']): ?><li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/preview?page_id=<?php echo $this->_tpl_vars['default_metapage_id']; ?>
&amp;item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
';" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page.png" border="0" />&nbsp;Preview it</a></li><?php endif; ?>
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
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b><?php echo $this->_tpl_vars['item']['_model']['name']; ?>
 Options</b></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassMembers?class_id=<?php echo $this->_tpl_vars['item']['_model']['id']; ?>
';" class="right-nav-link">Back to <?php echo $this->_tpl_vars['item']['_model']['plural_name']; ?>
</a></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addItem?class_id=<?php echo $this->_tpl_vars['item']['_model']['id']; ?>
';" class="right-nav-link">New <?php echo $this->_tpl_vars['item']['_model']['name']; ?>
</a></li>
  </ul>
  
</div>