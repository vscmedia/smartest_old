<?php /* Smarty version 2.6.18, created on 2007-12-04 05:58:27
         compiled from /var/www/html/System/Applications/Items/Presentation/addItem.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'lower', '/var/www/html/System/Applications/Items/Presentation/addItem.tpl', 5, false),array('function', 'item_field', '/var/www/html/System/Applications/Items/Presentation/addItem.tpl', 21, false),)), $this); ?>
<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Data Manager</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getItemClassMembers?class_id=<?php echo $this->_tpl_vars['item']['_model']['id']; ?>
"><?php echo $this->_tpl_vars['item']['_model']['plural_name']; ?>
</a> &gt; Add <?php echo $this->_tpl_vars['item']['_model']['name']; ?>
</h3>

<div id="instruction">You are submitting the draft property values of the new <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['_model']['name'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.</div>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addItem" enctype="multipart/form-data" method="post">

<input type="hidden" name="class_id" value="<?php echo $this->_tpl_vars['item']['_model']['id']; ?>
" />
<input type="hidden" name="item_id" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />
<input type="hidden" name="save_item" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />

<div class="edit-form-row">
  <div class="form-section-label"><?php echo $this->_tpl_vars['item']['_model']['name']; ?>
 Name</div>
  <input type="text" name="item[_name]" value="Untitled <?php echo $this->_tpl_vars['item']['_model']['name']; ?>
" style="width:250px" />
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


  
</div>