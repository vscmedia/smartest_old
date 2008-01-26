<?php /* Smarty version 2.6.18, created on 2007-12-04 08:25:45
         compiled from /var/www/html/System/Applications/MetaData/Presentation/startPage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dud_link', '/var/www/html/System/Applications/MetaData/Presentation/startPage.tpl', 14, false),)), $this); ?>
<div id="work-area">
<h3>Tags</h3>

<div class="instruction">Tags exist across all your sites. Some pags may not make sense for certain sites, but they can be ignored.</div>

<?php $_from = $this->_tpl_vars['tags']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['tag']):
?>
<a style="font-size:1.2em" href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getTaggedObjects?tag=<?php echo $this->_tpl_vars['tag']['name']; ?>
"><?php echo $this->_tpl_vars['tag']['label']; ?>
</a><?php if ($this->_tpl_vars['key'] < count ( $this->_tpl_vars['tags'] )): ?>, <?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tags Options</b></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addTag'"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>
</div>