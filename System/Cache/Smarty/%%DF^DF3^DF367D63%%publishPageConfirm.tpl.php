<?php /* Smarty version 2.6.18, created on 2007-11-28 10:56:07
         compiled from /var/www/html/System/Applications/Pages/Presentation/publishPageConfirm.tpl */ ?>
<div id="work-area">

<h3>Publish Page</h3>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/publishPage" method="get">

<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page_id']; ?>
" />

<?php if ($this->_tpl_vars['allow_publish']): ?>

<?php if ($this->_tpl_vars['count'] < 1): ?>

<div class="instruction">Are you sure you want to publish this page?</div>

<?php elseif ($this->_tpl_vars['count'] == 1): ?>

<div class="instruction"><b>Warning</b>: The <?php echo $this->_tpl_vars['undefined_asset_classes'][0]['info']['type']; ?>
 "<?php echo $this->_tpl_vars['undefined_asset_classes'][0]['info']['assetclass_label']; ?>
" is not defined.</div>
	
<?php elseif ($this->_tpl_vars['count'] > 1): ?>

<div class="instruction"><b>Warning</b>: The following elements are not defined in the draft version of this page:</div>

<ul class="basic-list">

	<?php $_from = $this->_tpl_vars['undefined_asset_classes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['undefinedAssetClass']):
?>
	<li><?php echo $this->_tpl_vars['undefinedAssetClass']['info']['type']; ?>
 <b><?php echo $this->_tpl_vars['undefinedAssetClass']['info']['assetclass_name']; ?>
</b></li>
	<?php endforeach; endif; unset($_from); ?>

</ul>

<div class="instruction">Publishing this page will cause undefined placeholders and containers to be included on a live page.<br />Are you sure you want to continue?</div>

<?php endif; ?>

<?php else: ?>

<div class="instruction">You can't publish this page at the moment</div>

<?php endif; ?>

<div class="buttons-bar">
  <input type="button" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editPage?page_id=<?php echo $this->_tpl_vars['page_id']; ?>
'" value="Cancel" />
  <?php if ($this->_tpl_vars['allow_publish']): ?><input type="submit" value="Publish" /><?php endif; ?>
</div>
	
</div>