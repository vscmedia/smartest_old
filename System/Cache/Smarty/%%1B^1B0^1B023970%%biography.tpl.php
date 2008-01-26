<?php /* Smarty version 2.6.18, created on 2008-01-17 20:41:39
         compiled from /var/www/html/Presentation/Assets/biography.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'field', '/var/www/html/Presentation/Assets/biography.tpl', 1, false),array('function', 'placeholder', '/var/www/html/Presentation/Assets/biography.tpl', 4, false),)), $this); ?>
<h4><?php echo smarty_function_field(array('name' => 'subtitle'), $this);?>
</h4>

<div>
<?php echo smarty_function_placeholder(array('name' => 'headline_image','style' => "float: left; margin-right: 10px"), $this);?>

<?php echo smarty_function_placeholder(array('name' => "main-body-text"), $this);?>

</div><br clear="all" />