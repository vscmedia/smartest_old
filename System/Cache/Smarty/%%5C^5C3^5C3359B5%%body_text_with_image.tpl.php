<?php /* Smarty version 2.6.18, created on 2007-12-23 21:39:17
         compiled from /var/www/html/Presentation/Assets/body_text_with_image.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'field', '/var/www/html/Presentation/Assets/body_text_with_image.tpl', 1, false),array('function', 'placeholder', '/var/www/html/Presentation/Assets/body_text_with_image.tpl', 3, false),)), $this); ?>
<?php if ($this->_tpl_vars['this']['fields']['subtitle']): ?><h4><?php echo smarty_function_field(array('name' => 'subtitle'), $this);?>
</h4><?php endif; ?>
<div class="image" style="float: right; margin-left: 10px;">
<?php echo smarty_function_placeholder(array('name' => 'headline_image','style' => "border: 1px solid #666; display:block"), $this);?>

<div class="caption" style="font-size:11px; width:350px"><?php echo smarty_function_field(array('name' => 'headline_image_caption'), $this);?>
</div>
</div>
<?php echo smarty_function_placeholder(array('name' => "main-body-text"), $this);?>

<br /><br clear="all" />