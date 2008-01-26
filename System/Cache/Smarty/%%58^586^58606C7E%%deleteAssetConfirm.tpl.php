<?php /* Smarty version 2.6.18, created on 2007-12-03 11:09:45
         compiled from /var/www/html/System/Applications/Assets/Presentation/deleteAssetConfirm.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', '/var/www/html/System/Applications/Assets/Presentation/deleteAssetConfirm.tpl', 34, false),)), $this); ?>
<div id="work-area">

<h3>Delete Asset</h3>
	
	<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/deleteAsset" method="post">
	
	<input type="hidden" name="asset_id" value="<?php echo $this->_tpl_vars['asset']['id']; ?>
" />
	
	<?php if ($this->_tpl_vars['num_live_instances'] == 0 && $this->_tpl_vars['num_draft_instances'] == 0): ?>
	
	<!-- Asset can be deleted if permissions are ok -->
	
	<div class="instruction">Are you sure you want to delete this asset?</div>
	
	<div class="buttons-bar">
	  <input type="button" value="Cancel" onclick="cancelForm()" />
	  <input type="submit" value="OK" />
	</div>
	
	<?php elseif ($this->_tpl_vars['num_live_instances'] == 0 && $this->_tpl_vars['num_draft_instances'] > 0): ?>
	
	<!-- Asset can still be deleted, providing user has permission, but warning is displayed-->
	
	<div class="instruction">Careful. This file is in use on one or more changed pages. You may still delete it, but those definitions will be reverted to their live definition</div>
	
	<table class="basic-table" cellspacing="1" cellpadding="2" border="0">
	  <tr class="head">
	   <td><b>Placeholder</b></td>
	   <td><b>Page</b></td>
	   <td><b>Site</b></td>
	   <td></td>
	  </tr>
	  <?php $_from = $this->_tpl_vars['draft_instances']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['instance']):
?>
	  <tr class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
">
	   <td><?php echo $this->_tpl_vars['instance']['assetclass']['name']; ?>
</td>
	   <td><?php echo $this->_tpl_vars['instance']['page']['title']; ?>
</td>
	   <td><?php echo $this->_tpl_vars['instance']['site']['name']; ?>
</td>
	   <td><a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/definePlaceholder?page_id=<?php echo $this->_tpl_vars['instance']['page']['webid']; ?>
&amp;assetclass_id=<?php echo $this->_tpl_vars['instance']['assetclass']['name']; ?>
">Edit</a></td>
	  </tr>
	  <?php endforeach; endif; unset($_from); ?>
	</table>
	
	<div class="buttons-bar">
	  <input type="submit" value="Proceed" />
	  <input type="button" value="Go Back (Recommended)" onclick="cancelForm()" />
	</div>
	
	<?php else: ?>
	
	<!-- Asset cannot be deleted because it is used on live pages -->
	
	<div class="instruction">This file can't be deleted because it is in use on one or more live pages:</div>
	
	<table class="basic-table" cellspacing="1" cellpadding="2" border="0">
	  <tr>
	   <td><b>Placeholder<b></td>
	   <td><b>Page<b></td>
	   <td><b>Site<b></td>
	   <td></td>
	  </tr>
	  <?php $_from = $this->_tpl_vars['live_instances']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['instance']):
?>
	  <tr>
	   <td><?php echo $this->_tpl_vars['instance']['assetclass']['name']; ?>
</td>
	   <td><?php echo $this->_tpl_vars['instance']['page']['title']; ?>
</td>
	   <td><?php echo $this->_tpl_vars['instance']['site']['name']; ?>
</td>
	   <td><a href="<?php echo $this->_tpl_vars['domain']; ?>
websitemanager/definePlaceholder?page_id=<?php echo $this->_tpl_vars['instance']['page']['webid']; ?>
&amp;assetclass_id=<?php echo $this->_tpl_vars['instance']['assetclass']['name']; ?>
">Edit</a></td>
	  </tr>
	  <?php endforeach; endif; unset($_from); ?>
	</table>
	
	<div class="buttons-bar"><input type="button" onclick="cancelForm()" value="OK" /></div>
	
	<?php endif; ?>
	
	</form>

</div>