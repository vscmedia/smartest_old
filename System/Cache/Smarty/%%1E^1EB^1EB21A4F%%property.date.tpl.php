<?php /* Smarty version 2.6.18, created on 2007-11-25 20:47:48
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/CMS/property.date.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'humandate', '/var/vsc/clients/claritycapital/smartest/System/Applications/Items/Presentation/CMS/property.date.tpl', 2, false),)), $this); ?>
<?php ob_start(); ?><?php echo $this->_tpl_vars['raw_value']['Y']; ?>
-<?php echo $this->_tpl_vars['raw_value']['M']; ?>
-<?php echo $this->_tpl_vars['raw_value']['D']; ?>
<?php $this->_smarty_vars['capture']['_date'] = ob_get_contents();  $this->assign('_date', ob_get_contents());ob_end_clean(); ?>
<?php echo smarty_function_humandate(array('inputdate' => $this->_tpl_vars['_date']), $this);?>