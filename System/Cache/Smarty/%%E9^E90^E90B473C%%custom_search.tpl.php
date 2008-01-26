<?php /* Smarty version 2.6.18, created on 2007-11-26 03:35:59
         compiled from /var/www/html/Presentation/Assets/custom_search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'link', '/var/www/html/Presentation/Assets/custom_search.tpl', 6, false),)), $this); ?>
<h4>Custom Search</h4>

<ul class="custom-search-links">
  <?php $_from = $this->_tpl_vars['this']['tags']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tag']):
?>
  <?php ob_start(); ?>tag:<?php echo $this->_tpl_vars['tag']['name']; ?>
<?php $this->_smarty_vars['capture']['tag_link'] = ob_get_contents();  $this->assign('tag_link', ob_get_contents());ob_end_clean(); ?>
  <li><?php echo smarty_function_link(array('to' => $this->_tpl_vars['tag_link'],'with' => $this->_tpl_vars['tag']['label']), $this);?>
</li>
  <?php endforeach; endif; unset($_from); ?>
</ul>