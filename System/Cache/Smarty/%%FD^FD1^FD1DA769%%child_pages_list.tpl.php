<?php /* Smarty version 2.6.18, created on 2007-12-17 16:36:42
         compiled from /var/www/html/Presentation/Assets/child_pages_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'placeholder', '/var/www/html/Presentation/Assets/child_pages_list.tpl', 1, false),array('function', 'field', '/var/www/html/Presentation/Assets/child_pages_list.tpl', 10, false),array('function', 'link', '/var/www/html/Presentation/Assets/child_pages_list.tpl', 15, false),array('modifier', 'nl2br', '/var/www/html/Presentation/Assets/child_pages_list.tpl', 15, false),)), $this); ?>
<?php echo smarty_function_placeholder(array('name' => 'category_masthead_banner'), $this);?>

<?php echo smarty_function_placeholder(array('name' => "main-body-text"), $this);?>


<?php if (empty ( $this->_tpl_vars['this']['navigation']['child_pages'] )): ?>
  
<?php else: ?>

<div class="child-pages-list">

<h4><?php if (strlen ( $this->_tpl_vars['this']['fields']['child_page_list_heading'] )): ?><?php echo smarty_function_field(array('name' => 'child_page_list_heading'), $this);?>
<?php else: ?>Read More<?php endif; ?></h4>

<ul>
  <?php $_from = $this->_tpl_vars['this']['navigation']['child_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sub_page']):
?>
  <?php ob_start(); ?>page:<?php echo $this->_tpl_vars['sub_page']['name']; ?>
<?php $this->_smarty_vars['capture']['sub_page_link'] = ob_get_contents();  $this->assign('sub_page_link', ob_get_contents());ob_end_clean(); ?>
  <li><h5><?php echo $this->_tpl_vars['sub_page']['title']; ?>
</h5><p><?php echo ((is_array($_tmp=$this->_tpl_vars['sub_page']['description'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</p><?php echo smarty_function_link(array('to' => $this->_tpl_vars['sub_page_link'],'with' => 'Read More'), $this);?>
</li>
  <?php endforeach; endif; unset($_from); ?>
</ul>

</div>

<?php endif; ?>