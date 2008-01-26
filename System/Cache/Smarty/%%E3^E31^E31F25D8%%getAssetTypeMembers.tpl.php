<?php /* Smarty version 2.6.18, created on 2007-11-27 12:48:47
         compiled from /var/www/html/System/Applications/Assets/Presentation/getAssetTypeMembers.tpl */ ?>
<script language="javascript" type="text/javascript">


</script>

<div id="work-area">

<h3><?php echo $this->_tpl_vars['type_label']; ?>
 Files</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assettype_code" value="<?php echo $this->_tpl_vars['type_code']; ?>
" />
 <input type="hidden" name="asset_id" id="item_id_input" value="" />
</form>

<div id="options-view-chooser">
Found <?php echo $this->_tpl_vars['num_assets']; ?>
 file<?php if ($this->_tpl_vars['num_assets'] != 1): ?>s<?php endif; ?>. View as:
<a href="#" onclick="setView('list', 'options_grid')">List</a> /
<a href="#" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-list" style="margin-top:0px" id="options_grid">
<?php $_from = $this->_tpl_vars['assets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['asset']):
?>

<li>
    <a href="#" class="option" id="item_<?php echo $this->_tpl_vars['asset']['asset_id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['asset']['asset_id']; ?>
', 'Template', '<?php echo $this->_tpl_vars['sidebartype']; ?>
');" >

<?php if (in_array ( $this->_tpl_vars['type_code'] , array ( 'SM_ASSETTYPE_JPEG_IMAGE' , 'SM_ASSETTYPE_GIF_IMAGE' , 'SM_ASSETTYPE_PNG_IMAGE' ) )): ?>
    <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/ImageAssetThumbnails/<?php echo $this->_tpl_vars['asset']['asset_url']; ?>
" /><?php echo $this->_tpl_vars['asset']['asset_stringid']; ?>
</a>
<?php else: ?>
    <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/blank_page.png" /><?php echo $this->_tpl_vars['asset']['asset_stringid']; ?>
</a>
<?php endif; ?>

</li>

<?php endforeach; endif; unset($_from); ?>
</ul>
<?php if ($this->_tpl_vars['error']): ?><?php echo $this->_tpl_vars['error']; ?>
<?php endif; ?>

</div>

<div id="actions-area">

<ul class="actions-list" id="noneditableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
	<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){ workWithItem(\'deleteAssetConfirm\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt="" /> Delete This File</a></li>
		<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){ workWithItem(\'downloadAsset\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt="" /> Download This File</a></li>
</ul>

<ul class="actions-list" id="editableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
	<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){ workWithItem(\'editAsset\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt=""> Edit This File</a></li>
	<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){ workWithItem(\'deleteAssetConfirm\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_delete.png" border="0" alt="" /> Delete This File</a></li>
		<li class="permanent-action"><a href="#" onclick="<?php echo 'if(selectedPage){ workWithItem(\'downloadAsset\'); }'; ?>
" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_edit.png" border="0" alt="" /> Download This File</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Media Asset Options</b></li>
	<li class="permanent-action"><a href="#" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addAsset?asset_type=<?php echo $this->_tpl_vars['type_code']; ?>
'" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_add.png" border="0" alt="" /> Add a new file of this type</a></li>
</ul>

</div>