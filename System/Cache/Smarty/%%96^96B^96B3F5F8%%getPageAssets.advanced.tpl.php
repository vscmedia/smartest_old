<?php /* Smarty version 2.6.18, created on 2007-11-30 12:33:21
         compiled from /var/www/html/System/Applications/Pages/Presentation/getPageAssets.advanced.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/var/www/html/System/Applications/Pages/Presentation/getPageAssets.advanced.tpl', 69, false),array('modifier', 'lower', '/var/www/html/System/Applications/Pages/Presentation/getPageAssets.advanced.tpl', 69, false),)), $this); ?>
<h3>Elements used on page: <?php echo $this->_tpl_vars['page']['title']; ?>
</h3>

<a name="top"></a>
<div class="instruction">Double click a placeholder to set its content, or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assetclass_id" id="item_id_input" value="" />
  <input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page']['webid']; ?>
" />
</form>
    

<div id="options-view-chooser">
<form id="templateSelect" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/setPageTemplate" method="get" style="margin:0px">

Viewing mode:
<?php if ($this->_tpl_vars['version'] == 'draft'): ?>
<b>Edit</b> - <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/pageAssets?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
&amp;version=live">Switch to live mode</a>
<?php else: ?>
<b>Live</b> - <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/pageAssets?page_id=<?php echo $this->_tpl_vars['page']['webid']; ?>
&amp;version=draft">Switch to draft mode</a>
<?php endif; ?>
  
<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page']['webid']; ?>
" />
<input type="hidden" name="site_id" value="<?php echo $this->_tpl_vars['site_id']; ?>
" />
<input type="hidden" name="version" value="<?php echo $this->_tpl_vars['version']; ?>
" />
  	  
<?php if ($this->_tpl_vars['version'] == 'draft'): ?>
      Master Template:
      <select name="template_name" onchange="document.getElementById('templateSelect').submit();">
        <option value="">Not Selected</option>
        <?php $_from = $this->_tpl_vars['templates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['template']):
?>
          <option value="<?php echo $this->_tpl_vars['template']['filename']; ?>
"<?php if ($this->_tpl_vars['templateMenuField'] == $this->_tpl_vars['template']['filename']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['template']['filename']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
      <?php if ($this->_tpl_vars['template']['filename']): ?><?php endif; ?>
<?php else: ?>
	Master Template: <b title="Changing this value may affect which placeholders need to be defined on this page"><?php echo $this->_tpl_vars['templateMenuField']; ?>
</b>
<?php endif; ?>
    </form>
</div>

<div class="preference-pane" id="assets_draft" style="display:block">

<?php if (! empty ( $this->_tpl_vars['assets'] )): ?>

<ul class="tree-parent-node-open" id="tree-root">
  <?php if (!function_exists('smarty_fun_menurecursion')) { function smarty_fun_menurecursion(&$smarty, $params) { $_fun_tpl_vars = $smarty->_tpl_vars; $smarty->assign($params);  ?>
    
    <?php ob_start(); ?>list_<?php if ($smarty->_tpl_vars['assetclass']['info']['assetclass_id']): ?><?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_id']; ?>
<?php else: ?>0<?php endif; ?><?php $smarty->_smarty_vars['capture']['foreach_name'] = ob_get_contents();  $smarty->assign('foreach_name', ob_get_contents());ob_end_clean(); ?>
    <?php ob_start(); ?><?php if ($smarty->_tpl_vars['assetclass']['info']['assetclass_id']): ?><?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_id']; ?>
<?php else: ?>0<?php endif; ?><?php $smarty->_smarty_vars['capture']['foreach_id'] = ob_get_contents();  $smarty->assign('foreach_id', ob_get_contents());ob_end_clean(); ?>
    
    <?php $_from = $smarty->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$smarty->_foreach[$smarty->_tpl_vars['foreach_name']] = array('total' => count($_from), 'iteration' => 0);
if ($smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['total'] > 0):
    foreach ($_from as $smarty->_tpl_vars['assetclass']):
        $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']++;
?>
    
    <?php if ($smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration'] == 1 && $smarty->_tpl_vars['foreach_id'] == 0): ?>
    <li><img border="0" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/page.png" />Current Page: <?php echo $smarty->_tpl_vars['page']['title']; ?>
</li>
    <?php endif; ?>
    
    <li <?php if (($smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration'] == $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['total'])): ?>class="last"<?php elseif (($smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration'] <= 1)): ?>class="first"<?php else: ?>class="middle"<?php endif; ?>>
    <?php if (( $smarty->_tpl_vars['assetclass']['info']['defined'] == 'PUBLISHED' || $smarty->_tpl_vars['assetclass']['info']['defined'] == 'DRAFT' ) && in_array ( $smarty->_tpl_vars['assetclass']['info']['assetclass_type'] , array ( 'SM_ASSETTYPE_JAVASCRIPT' , 'SM_ASSETTYPE_STYLESHEET' , 'SM_ASSETTYPE_RICH_TEXT' , 'SM_ASSETTYPE_PLAIN_TEXT' , 'SM_ASSETTYPE_SL_TEXT' ) ) && $smarty->_tpl_vars['version'] == 'draft'): ?><a href="<?php echo $smarty->_tpl_vars['domain']; ?>
assets/editAsset?asset_id=<?php echo $smarty->_tpl_vars['assetclass']['info']['asset_id']; ?>
&amp;from=pageAssets" style="float:right;display:block;margin-right:5px;">Edit This File</a><?php endif; ?>
      <?php if (! empty ( $smarty->_tpl_vars['assetclass']['children'] )): ?>
      <a href="#" onclick="toggleParentNodeFromOpenState('<?php echo $smarty->_tpl_vars['foreach_id']; ?>
_<?php echo $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']; ?>
')"><img src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Images/open.gif" alt="" border="0" id="toggle_<?php echo $smarty->_tpl_vars['foreach_id']; ?>
_<?php echo $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']; ?>
" /></a>
      <?php else: ?>
      <img src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Images/blank.gif" alt="" border="0" />
      <?php endif; ?>
      <a id="item_<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['assetclass_name'])) ? $smarty->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
" class="option" href="<?php if ($smarty->_tpl_vars['version'] == 'draft'): ?>javascript:setSelectedItem('<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['assetclass_name'])) ? $smarty->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
', '<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['assetclass_name'])) ? $smarty->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
', '<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['type'])) ? $smarty->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
');<?php else: ?>javascript:nothing();<?php endif; ?>">		 
    <?php if ($smarty->_tpl_vars['assetclass']['info']['exists'] == 'true'): ?>
        

		<?php if ($smarty->_tpl_vars['assetclass']['info']['defined'] == 'PUBLISHED'): ?>
		  <img border="0" style="width:16px;height:16px;" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/published_<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['type'])) ? $smarty->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.gif" />
		<?php elseif ($smarty->_tpl_vars['assetclass']['info']['defined'] == 'DRAFT'): ?>
		  <?php if ($smarty->_tpl_vars['version'] == 'draft'): ?>
		    <img border="0" style="width:16px;height:16px;" title="This <?php echo $smarty->_tpl_vars['assetclass']['info']['type']; ?>
 is only defined in the draft version of the page" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/draftonly_<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['type'])) ? $smarty->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.gif" />
		  <?php else: ?>
		    <img border="0" style="width:16px;height:16px;" title="This <?php echo $smarty->_tpl_vars['assetclass']['info']['type']; ?>
 is only defined in the draft version of the page" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/undefined_<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['type'])) ? $smarty->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.gif" />
		  <?php endif; ?>
		<?php else: ?>
		  <img border="0" style="width:16px;height:16px;" title="This <?php echo $smarty->_tpl_vars['assetclass']['info']['type']; ?>
 has not yet been defined" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/undefined_<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['type'])) ? $smarty->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
.gif" />
		<?php endif; ?>
	
	  <b><?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_name']; ?>
</b>
	  <?php if ($smarty->_tpl_vars['assetclass']['info']['type'] == 'placeholder'): ?> (<?php echo ((is_array($_tmp=$smarty->_tpl_vars['assetclass']['info']['assetclass_type_code'])) ? $smarty->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
)<?php endif; ?>
	  <?php if ($smarty->_tpl_vars['assetclass']['info']['filename'] != ""): ?> : 
	    <?php if ($smarty->_tpl_vars['assetclass']['info']['assetclass_type_code'] == 'JPEG' || $smarty->_tpl_vars['assetclass']['info']['assetclass_type_code'] == 'PNG' || $smarty->_tpl_vars['assetclass']['info']['assetclass_type_code'] == 'GIF'): ?>
	      <img src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/picture.png" style="border:0px" />
	    <?php elseif ($smarty->_tpl_vars['assetclass']['info']['assetclass_type_code'] == 'TEXT'): ?>
	      <img src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/page_white_text.png" style="border:0px" />
	    <?php elseif ($smarty->_tpl_vars['assetclass']['info']['assetclass_type_code'] == 'HTML'): ?>
	      <img src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png" style="border:0px" />
	    <?php else: ?>
	      
	    <?php endif; ?>
	  <?php echo $smarty->_tpl_vars['assetclass']['info']['filename']; ?>

	  <?php else: ?>
	    	  <?php endif; ?>
	  
	<?php else: ?>
		
	<?php if ($smarty->_tpl_vars['assetclass']['info']['type'] == 'list'): ?>
	<img border="0" style="width:16px;height:16px;" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/notexist_list.gif" />
	<?php elseif ($smarty->_tpl_vars['assetclass']['info']['type'] == 'field'): ?>
	<img border="0" style="width:16px;height:16px;" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/notexist_field.gif" />
	<?php else: ?>
	<img border="0" style="width:16px;height:16px;" src="<?php echo $smarty->_tpl_vars['domain']; ?>
Resources/Icons/notexist.gif" />
	<?php endif; ?>
	
	<b><?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_name']; ?>
</b> This <?php echo $smarty->_tpl_vars['assetclass']['info']['type']; ?>
 doesn't exist.&nbsp;
	  <?php if ($smarty->_tpl_vars['assetclass']['info']['type'] == 'container'): ?>
	    <a href="<?php echo $smarty->_tpl_vars['domain']; ?>
assets/addContainer?name=<?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_name']; ?>
&amp;type=<?php echo $smarty->_tpl_vars['assetclass']['info']['type']; ?>
">Add it</a>
	  <?php elseif ($smarty->_tpl_vars['assetclass']['info']['type'] == 'placeholder'): ?>
	    <a href="<?php echo $smarty->_tpl_vars['domain']; ?>
assets/addPlaceholder?name=<?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_name']; ?>
&amp;type=<?php echo $smarty->_tpl_vars['assetclass']['info']['type']; ?>
">Add it</a>
	  <?php elseif ($smarty->_tpl_vars['assetclass']['info']['type'] == 'list'): ?>
	    <a href="<?php echo $smarty->_tpl_vars['domain']; ?>
<?php echo $smarty->_tpl_vars['section']; ?>
/addList?name=<?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_name']; ?>
">Add it</a>
	  <?php elseif ($smarty->_tpl_vars['assetclass']['info']['type'] == 'field'): ?>
	    <a href="<?php echo $smarty->_tpl_vars['domain']; ?>
metadata/addPageProperty?site_id=<?php echo $smarty->_tpl_vars['site_id']; ?>
&amp;name=<?php echo $smarty->_tpl_vars['assetclass']['info']['assetclass_name']; ?>
">Add it</a>
	  <?php endif; ?>
	  
	<?php endif; ?>
      </a>
      <?php if (! empty ( $smarty->_tpl_vars['assetclass']['children'] )): ?>
      <ul class="tree-parent-node-open" id="<?php echo $smarty->_tpl_vars['foreach_name']; ?>
_<?php echo $smarty->_foreach[$smarty->_tpl_vars['foreach_name']]['iteration']; ?>
">
        <?php smarty_fun_menurecursion($smarty, array('list'=>$smarty->_tpl_vars['assetclass']['children']));  ?>
      </ul>
      <?php endif; ?>
    </li>
    <?php endforeach; endif; unset($_from); ?>
    
  <?php  $smarty->_tpl_vars = $_fun_tpl_vars; }} smarty_fun_menurecursion($this, array('list'=>$this->_tpl_vars['assets']));  ?>
</ul>
<?php endif; ?>
</div>

<!-- Key: <div style="display:inline"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/flag_green.png" alt="" />Published&nbsp;&nbsp;
<img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/flag_yellow.png" alt="" />Draft Only&nbsp;&nbsp;
<img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/flag_red.png" alt="" />Undefined</div>-->