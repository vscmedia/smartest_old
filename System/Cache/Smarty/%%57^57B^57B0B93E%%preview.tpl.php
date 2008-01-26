<?php /* Smarty version 2.6.18, created on 2007-11-26 00:58:44
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/preview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/preview.tpl', 3, false),array('modifier', 'lower', '/var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/preview.tpl', 5, false),)), $this); ?>
<div id="work-area">

<?php echo smarty_function_load_interface(array('file' => "editPage.tabs.tpl"), $this);?>


<h3>Preview of page: <?php echo $this->_tpl_vars['page']['title']; ?>
<?php if ($this->_tpl_vars['item']): ?> (as <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['_model']['name'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
 &quot;<?php echo $this->_tpl_vars['item']['name']; ?>
&quot;)<?php endif; ?></h3>

<?php if ($this->_tpl_vars['show_iframe']): ?>

<div style="margin-bottom:10px">
  <?php if ($this->_tpl_vars['item']): ?><input type="button" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
datamanager/editItem?item_id=<?php echo $this->_tpl_vars['item']['id']; ?>
&amp;from=pagePreview'" value="Edit <?php echo $this->_tpl_vars['item']['_model']['name']; ?>
" /><?php endif; ?>
  <input type="button" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/approvePageChanges?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
'" value="Approve Changes"<?php if (! $this->_tpl_vars['show_approve_button']): ?> disabled="disabled"<?php endif; ?>/>
  <input type="button" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/publishPage?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
'" value="Publish This Page" <?php if (! $this->_tpl_vars['show_publish_button']): ?> disabled="disabled"<?php endif; ?>/>
</div>

<div id="preview">
<iframe src="<?php echo $this->_tpl_vars['domain']; ?>
website/renderEditableDraftPage?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
<?php if ($this->_tpl_vars['item']): ?>&amp;item_id=<?php echo $this->_tpl_vars['item']['webid']; ?>
<?php endif; ?>"></iframe>
</div>

<?php elseif ($this->_tpl_vars['show_item_list']): ?>

<h4>To preview this page, please choose a specific <?php echo $this->_tpl_vars['model']['name']; ?>
:</h4>


<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/preview" method="get" id="item_chooser">
  <input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page']['webid']; ?>
" />
  <select name="item_id" style="width:300px" onchange="$('item_chooser').submit();" />
    <?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page_item']):
?>
    <option value="<?php echo $this->_tpl_vars['page_item']['id']; ?>
"><?php echo $this->_tpl_vars['page_item']['name']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
  </select>
  <input type="submit" value="Go" />
</form>

<?php endif; ?>

</div>