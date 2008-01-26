<?php /* Smarty version 2.6.18, created on 2007-11-26 00:36:07
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/editPage.form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'convert_timestamp', '/var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/editPage.form.tpl', 45, false),array('function', 'cycle', '/var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/editPage.form.tpl', 82, false),array('modifier', 'truncate', '/var/vsc/clients/claritycapital/smartest/System/Applications/Pages/Presentation/editPage.form.tpl', 93, false),)), $this); ?>
<h3 id="pageName">Page Details: <?php echo $this->_tpl_vars['pageInfo']['title']; ?>
</h3>

<form id="getForm" method="get" action="">
  <input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['pageInfo']['id']; ?>
">
  <input type="hidden" name="page_webid" value="<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
">
  <input type="hidden" name="current_url" value="<?php echo $this->_tpl_vars['pageurl']['pageurl_url']; ?>
">
</form>

<div class="instruction">Edit page meta information.</div>

<form id="updatePage" name="updatePage" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updatePage" method="post" style="margin:0px">
  
  <input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['pageInfo']['id']; ?>
">
  <input type="hidden" name="page_webid" value="<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
">

<div id="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Title:</div>
    	<input type="text" name="page_title" value="<?php echo $this->_tpl_vars['pageInfo']['title']; ?>
" style="width:200px" />
    	<?php if (! $this->_tpl_vars['pageInfo']['title']): ?><div>You must have a title! </div><?php endif; ?>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Type</div>
    <?php if ($this->_tpl_vars['pageInfo']['type'] == 'ITEMCLASS'): ?>Object Meta-Page<?php else: ?>Regular Web-Page<?php endif; ?>
  </div>
  
  <?php if ($this->_tpl_vars['pageInfo']['type'] == 'ITEMCLASS'): ?>
  <div class="edit-form-row">
    <div class="form-section-label">Data Set</div>
    &quot;<?php echo $this->_tpl_vars['pageInfo']['set_name']; ?>
&quot;
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Object Model</div>
    &quot;<?php echo $this->_tpl_vars['pageInfo']['model_name']; ?>
&quot;
  </div>
  <?php endif; ?>
  
  <div class="edit-form-row">
    <div class="form-section-label">Status</div>
      <div style="display:inline" class="text">
    	<?php if ($this->_tpl_vars['pageInfo']['is_published'] == 'TRUE'): ?>
    	  <strong>Live</strong> - Last Published <?php echo smarty_function_convert_timestamp(array('format' => "h:i a, l jS F, Y",'time' => $this->_tpl_vars['pageInfo']['last_published']), $this);?>

    	<?php else: ?>
    	  <?php if ($this->_tpl_vars['pageInfo']['last_published'] == 0): ?>
    	  	<strong>Never Published</strong>
    	  <?php else: ?>
    	    <strong>Not Published</strong> <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/getPageAssets?page_id=<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
">Go To Page Tree</a>
    	  <?php endif; ?>
    	<?php endif; ?></div>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Cache as Static HTML</div>
    <input type="radio" name="page_cache_as_html" id="page_cache_as_html_on" value="TRUE"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'TRUE'): ?> checked="checked"<?php endif; ?> />&nbsp;<label for="page_cache_as_html_on">Yes please</label>
    <input type="radio" name="page_cache_as_html" id="page_cache_as_html_off" value="FALSE"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'FALSE'): ?> checked="checked"<?php endif; ?> />&nbsp;<label for="page_cache_as_html_off">No, thanks</label>
  </div>
  
  <?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'TRUE'): ?>
  <div class="edit-form-row">
    <div class="form-section-label">Cache How Often?</div>
    	<select name="page_cache_interval" style="width:300px">
    	  <option value="PERMANENT"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'PERMANENT'): ?> selected="selected"<?php endif; ?>>Stay Cached Until Re-Published</option>
    	  <option value="MONTHLY"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'MONTHLY'): ?> selected="selected"<?php endif; ?>>Every Month</option>
    	  <option value="DAILY"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'DAILY'): ?> selected="selected"<?php endif; ?>>Every Day</option>
    	  <option value="HOURLY"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'HOURLY'): ?> selected="selected"<?php endif; ?>>Every Hour</option>
    	  <option value="MINUTE"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'MINUTE'): ?> selected="selected"<?php endif; ?>>Every Minute</option>
    	  <option value="SECOND"<?php if ($this->_tpl_vars['pageInfo']['cache_as_html'] == 'SECOND'): ?> selected="selected"<?php endif; ?>>Every Second</option>
    	</select>
  </div>
  <?php endif; ?>
  
  <?php if ($this->_tpl_vars['pageInfo']['id'] > 0): ?>
  <div class="edit-form-row">
    <div class="form-section-label">Address</div>
		
	  <table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="0">
  	<?php if (! empty ( $this->_tpl_vars['pageurls'] )): ?>
  	<?php if ($this->_tpl_vars['ishomepage'] == 'true'): ?>
  	<tr style="background-color:#<?php echo smarty_function_cycle(array('values' => "ddd,fff"), $this);?>
;height:20px"><td>
  		<div style="display:inline" id="siteDomainField_0"><a href="http://<?php echo $this->_tpl_vars['site']['domain']; ?>
/" target="_blank">http://<?php echo $this->_tpl_vars['site']['domain']; ?>
/</a></div>
  	</td>
  	<td>&nbsp;</td>
    </tr><?php endif; ?>
	
	
  	<?php $_from = $this->_tpl_vars['pageurls']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pageurl']):
?>
  	<?php ob_start(); ?>http://<?php echo $this->_tpl_vars['site']['domain']; ?>
/<?php echo $this->_tpl_vars['pageurl']['url']; ?>
<?php $this->_smarty_vars['capture']['pageUrl'] = ob_get_contents();  $this->assign('pageUrl', ob_get_contents());ob_end_clean(); ?>
  	<tr style="background-color:#<?php echo smarty_function_cycle(array('values' => "ddd,fff"), $this);?>
;height:20px"><td>
  		<div style="display:inline" id="siteDomainField_<?php echo $this->_tpl_vars['pageurl']['id']; ?>
">
  		  <?php if ($this->_tpl_vars['pageInfo']['is_published'] == 'TRUE' && $this->_tpl_vars['pageInfo']['type'] != 'ITEMCLASS'): ?><a href="<?php echo $this->_tpl_vars['pageUrl']; ?>
" target="_blank"><?php echo ((is_array($_tmp=$this->_tpl_vars['pageUrl'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 72, "...") : smarty_modifier_truncate($_tmp, 72, "...")); ?>
</a><?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['pageUrl'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 72, "...") : smarty_modifier_truncate($_tmp, 72, "...")); ?>
<?php endif; ?></div>
  	</td>
  	<td>
  		<input type="button" name="edit" value="Edit" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editPageUrl?page_id=<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
&amp;url=<?php echo $this->_tpl_vars['pageurl']['id']; ?>
&amp;ishomepage=<?php echo $this->_tpl_vars['ishomepage']; ?>
'" />
  		<?php if ($this->_tpl_vars['count'] > 1 || $this->_tpl_vars['ishomepage'] == 'true'): ?><input type="button" name="delete" value="Delete" onclick="if(confirm('Are you sure you want to delete this URL?')) window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/deletePageUrl?page_id=<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
&amp;url=<?php echo $this->_tpl_vars['pageurl']['id']; ?>
&amp;ishomepage=<?php echo $this->_tpl_vars['ishomepage']; ?>
;'"/><?php endif; ?>
  	</td></tr> 
  	<?php endforeach; endif; unset($_from); ?>
	
	
  	<?php else: ?>
  	<?php ob_start(); ?>http://<?php echo $this->_tpl_vars['site']['domain']; ?>
/website/renderPageFromId?page_id=<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
<?php $this->_smarty_vars['capture']['defaultUrl'] = ob_get_contents();  $this->assign('defaultUrl', ob_get_contents());ob_end_clean(); ?>
  	<tr style="background-color:#<?php echo smarty_function_cycle(array('values' => "ddd,fff"), $this);?>
;height:20px"><td>
  		<div style="display:inline" id="siteDomainField">
  		<?php if ($this->_tpl_vars['pageInfo']['is_published'] == 'TRUE'): ?><a href="<?php echo $this->_tpl_vars['defaultUrl']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['defaultUrl'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 72, "...") : smarty_modifier_truncate($_tmp, 72, "...")); ?>
</a><?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['defaultUrl'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 72, "...") : smarty_modifier_truncate($_tmp, 72, "...")); ?>
<?php endif; ?></div>
  	</td>
  	<td></td></tr>
  	<?php endif; ?>
  	</table>
	
  	<a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addPageUrl?page_id=<?php echo $this->_tpl_vars['pageInfo']['webid']; ?>
&amp;ishomepage=<?php echo $this->_tpl_vars['ishomepage']; ?>
"><?php if (! empty ( $this->_tpl_vars['pageurls'] )): ?>Add Another Url<?php else: ?>Give This Page A Nicer Url<?php endif; ?></a><br />
  	<img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/spacer.gif" width="1" height="10" />
  </div>
  
  <?php if ($this->_tpl_vars['ishomepage'] != 'true'): ?>
  <div class="edit-form-row">
    <div class="form-section-label">Parent Page</div>
    <select name="page_parent" style="width:300px">
      <?php $_from = $this->_tpl_vars['parent_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
        <?php if ($this->_tpl_vars['page']['id'] != $this->_tpl_vars['pageInfo']['id']): ?>
        <option value="<?php echo $this->_tpl_vars['page']['info']['id']; ?>
"<?php if ($this->_tpl_vars['pageInfo']['parent'] == $this->_tpl_vars['page']['info']['id']): ?> selected="selected"<?php endif; ?>>+<?php unset($this->_sections['dashes']);
$this->_sections['dashes']['name'] = 'dashes';
$this->_sections['dashes']['loop'] = is_array($_loop=$this->_tpl_vars['page']['treeLevel']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['dashes']['show'] = true;
$this->_sections['dashes']['max'] = $this->_sections['dashes']['loop'];
$this->_sections['dashes']['step'] = 1;
$this->_sections['dashes']['start'] = $this->_sections['dashes']['step'] > 0 ? 0 : $this->_sections['dashes']['loop']-1;
if ($this->_sections['dashes']['show']) {
    $this->_sections['dashes']['total'] = $this->_sections['dashes']['loop'];
    if ($this->_sections['dashes']['total'] == 0)
        $this->_sections['dashes']['show'] = false;
} else
    $this->_sections['dashes']['total'] = 0;
if ($this->_sections['dashes']['show']):

            for ($this->_sections['dashes']['index'] = $this->_sections['dashes']['start'], $this->_sections['dashes']['iteration'] = 1;
                 $this->_sections['dashes']['iteration'] <= $this->_sections['dashes']['total'];
                 $this->_sections['dashes']['index'] += $this->_sections['dashes']['step'], $this->_sections['dashes']['iteration']++):
$this->_sections['dashes']['rownum'] = $this->_sections['dashes']['iteration'];
$this->_sections['dashes']['index_prev'] = $this->_sections['dashes']['index'] - $this->_sections['dashes']['step'];
$this->_sections['dashes']['index_next'] = $this->_sections['dashes']['index'] + $this->_sections['dashes']['step'];
$this->_sections['dashes']['first']      = ($this->_sections['dashes']['iteration'] == 1);
$this->_sections['dashes']['last']       = ($this->_sections['dashes']['iteration'] == $this->_sections['dashes']['total']);
?>-<?php endfor; endif; ?> <?php echo $this->_tpl_vars['page']['info']['title']; ?>
</option>
        <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
    </select>
  </div>
  <?php endif; ?>
  
  <?php endif; ?>
  
  <div class="edit-form-row">
    <div class="form-section-label">Search terms</div>
      <textarea name="page_search_field" style="width:500px;height:60px"><?php echo $this->_tpl_vars['pageInfo']['search_field']; ?>
</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Page Description</div>
      <textarea name="page_description" style="width:500px;height:60px"><?php echo $this->_tpl_vars['pageInfo']['description']; ?>
</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Meta Description</div>
      <textarea name="page_meta_description" style="width:500px;height:60px"><?php echo $this->_tpl_vars['pageInfo']['meta_description']; ?>
</textarea>
  </div>
    
  <div class="edit-form-row">
      <div class="form-section-label">Meta Keywords</div>
      <textarea name="page_keywords" style="width:500px;height:100px"><?php echo $this->_tpl_vars['pageInfo']['keywords']; ?>
</textarea>
    </div>
    
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" name="action" value="Save Changes" />
    	    </div>
  </div>
  
</div>

</form>