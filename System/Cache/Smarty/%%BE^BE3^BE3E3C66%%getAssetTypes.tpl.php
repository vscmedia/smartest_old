<?php /* Smarty version 2.6.18, created on 2007-11-26 04:00:22
         compiled from /var/www/html/System/Applications/Assets/Presentation/getAssetTypes.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/var/www/html/System/Applications/Assets/Presentation/getAssetTypes.tpl', 19, false),)), $this); ?>
<div id="work-area">

<h3>Media Assets</h3>
<a name="top"></a>
<div class="text" style="margin-bottom:10px">Double click an icon below to see assets in that category.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" id="item_id_input" name="asset_type" value="" />
</form>
  
<?php $_from = $this->_tpl_vars['assetTypeCats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['category_name'] => $this->_tpl_vars['assetTypeCategory']):
?>

<div class="form-section-label"><?php echo $this->_tpl_vars['category_name']; ?>
</div>

<ul class="options-grid-no-scroll" style="margin-top:0px">

<?php $_from = $this->_tpl_vars['assetTypeCategory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['assetType']):
?>
  <li ondblclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getAssetTypeMembers?asset_type=<?php echo $this->_tpl_vars['assetType']['id']; ?>
'">
    <a href="javascript:nothing();" id="item_<?php echo $this->_tpl_vars['assetType']['id']; ?>
" class="option" onclick="setSelectedItem('<?php echo $this->_tpl_vars['assetType']['id']; ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['assetType']['label'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
');">
      <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/folder.png" /><?php echo $this->_tpl_vars['assetType']['label']; ?>
</a></li><?php endforeach; endif; unset($_from); ?>

</ul><br clear="all" />
<?php endforeach; endif; unset($_from); ?>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected Asset Type</b></li>
	<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){ workWithItem(\'getAssetTypeMembers\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/layout_edit.png" border="0" alt=""> Show me all of this type</a></li>
	<li class="permanent-action"><a href="#" onclick="workWithItem('addAsset');" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt=""> Add another one of this type</a></li>
</ul>

</div>