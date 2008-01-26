<?php /* Smarty version 2.6.18, created on 2007-11-26 14:36:08
         compiled from /var/www/html/System/Applications/Assets/Presentation/editAsset.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'load_interface', '/var/www/html/System/Applications/Assets/Presentation/editAsset.tpl', 2, false),array('function', 'dud_link', '/var/www/html/System/Applications/Assets/Presentation/editAsset.tpl', 8, false),)), $this); ?>
<div id="work-area">
<?php echo smarty_function_load_interface(array('file' => $this->_tpl_vars['formTemplateInclude']), $this);?>

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="<?php echo smarty_function_dud_link(array(), $this);?>
" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getAssetTypeMembers?asset_type=<?php echo $this->_tpl_vars['asset_type']['id']; ?>
'">View all <?php echo $this->_tpl_vars['asset_type']['label']; ?>
 assets</a></li>
  </ul>
</div>