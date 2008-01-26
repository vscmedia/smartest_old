<?php /* Smarty version 2.6.18, created on 2007-12-02 14:52:32
         compiled from /var/www/html/Presentation/Assets/article_body.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'property', '/var/www/html/Presentation/Assets/article_body.tpl', 1, false),)), $this); ?>
<div style="font-size:0.7em;margin:0 0 5px 0"><?php echo smarty_function_property(array('name' => 'published_at'), $this);?>
</div>

<?php echo smarty_function_property(array('name' => 'upper_image','style' => "float:right;clear:right;display:block;margin:0 0 10px 10px;"), $this);?>

<?php echo smarty_function_property(array('name' => 'lower_image','style' => "float:right;clear:right;display:block;margin:0 0 10px 10px;"), $this);?>

<?php echo smarty_function_property(array('name' => 'article_text'), $this);?>