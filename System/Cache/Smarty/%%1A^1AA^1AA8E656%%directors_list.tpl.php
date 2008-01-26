<?php /* Smarty version 2.6.18, created on 2008-01-18 09:07:01
         compiled from /var/www/html/Presentation/Assets/directors_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'placeholder', '/var/www/html/Presentation/Assets/directors_list.tpl', 1, false),array('function', 'field', '/var/www/html/Presentation/Assets/directors_list.tpl', 9, false),array('function', 'url', '/var/www/html/Presentation/Assets/directors_list.tpl', 17, false),array('modifier', 'nl2br', '/var/www/html/Presentation/Assets/directors_list.tpl', 16, false),)), $this); ?>
<?php echo smarty_function_placeholder(array('name' => "main-body-text"), $this);?>


<?php if (empty ( $this->_tpl_vars['this']['navigation']['child_pages'] )): ?>
  
<?php else: ?>

<div class="child-pages-list">

<h4><?php if (strlen ( $this->_tpl_vars['this']['fields']['child_page_list_heading'] )): ?><?php echo smarty_function_field(array('name' => 'child_page_list_heading'), $this);?>
<?php else: ?>Read More<?php endif; ?></h4>

<table border="0" cellpadding="0" cellspacing="0" style="width:480px">
  <?php $_from = $this->_tpl_vars['this']['navigation']['child_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sub_page']):
?>
  <?php ob_start(); ?>page:<?php echo $this->_tpl_vars['sub_page']['name']; ?>
<?php $this->_smarty_vars['capture']['sub_page_link'] = ob_get_contents();  $this->assign('sub_page_link', ob_get_contents());ob_end_clean(); ?>
  <tr>
    <td valign="top"><?php if ($this->_tpl_vars['sub_page']['icon_image']): ?><img style="float:left;display:block;margin-right:8px" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/<?php echo $this->_tpl_vars['sub_page']['icon_image']; ?>
" alt="" /><?php else: ?>&nbsp;<?php endif; ?></td>
    <td style="padding-bottom:10px"><h5 style="color:#444"><?php echo $this->_tpl_vars['sub_page']['title']; ?>
</h5><p><?php echo ((is_array($_tmp=$this->_tpl_vars['sub_page']['description'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</p>
      <a href="<?php echo smarty_function_url(array('to' => $this->_tpl_vars['sub_page_link']), $this);?>
" style="font-size:12px">Read More</a><br /></td>
  </tr>
  <?php endforeach; endif; unset($_from); ?>
</table>

</div>

<?php endif; ?>