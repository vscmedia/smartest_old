<?php /* Smarty version 2.6.18, created on 2007-11-26 14:36:00
         compiled from /var/www/html/System/Applications/CmsFrontEnd/Presentation/renderEditableDraftPage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cms_page', '/var/www/html/System/Applications/CmsFrontEnd/Presentation/renderEditableDraftPage.tpl', 2, false),)), $this); ?>
<?php echo smarty_function_cms_page(array('page' => $this->_tpl_vars['_page'],'draft' => 'true'), $this);?>