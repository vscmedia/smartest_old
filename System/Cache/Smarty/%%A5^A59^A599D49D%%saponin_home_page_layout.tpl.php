<?php /* Smarty version 2.6.18, created on 2008-01-03 10:00:37
         compiled from /var/www/html/Presentation/Assets/saponin_home_page_layout.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'placeholder', '/var/www/html/Presentation/Assets/saponin_home_page_layout.tpl', 3, false),)), $this); ?>
<div id="home-page-welcome-text">
  <!--img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/saponin_home_top_bg.gif" alt="" /-->
  <?php echo smarty_function_placeholder(array('name' => "welcome-text"), $this);?>

</div>

<div id="home-page-body-text">
  <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/saponin_home_bottom_heading.gif" alt="Why Saponin?" />
  <?php echo smarty_function_placeholder(array('name' => "main-body-text"), $this);?>

</div>