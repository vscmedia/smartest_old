<?php /* Smarty version 2.6.18, created on 2007-11-25 23:57:53
         compiled from /var/vsc/clients/claritycapital/smartest/System/Applications/Desktop/Presentation/editSite.tpl */ ?>
<script language="javascript">
var domain = '<?php echo $this->_tpl_vars['domain']; ?>
';
var section = '<?php echo $this->_tpl_vars['section']; ?>
';
<?php echo '
 function updatePageName(newName){
  	document.getElementById(\'pageName\').innerHTML="Page Details: "+newName;
 }
 
 function hideNotify(){
 	// alert(\'one\');
 	// var hnot = setTimeout("alert(\'two\')",4000); 
	var hnot = setTimeout("document.getElementById(\'notify\').style.display=\'none\'",3500); 
 }
}

'; ?>
</script>

<div id="work-area">

<h3 id="siteName">Edit Site Parameters</h3>
<form id="updateSiteDetails" name="updateSiteDetails" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/updateSiteDetails" method="POST" style="margin:0px">

<input type="hidden" name="site_id" value="<?php echo $this->_tpl_vars['site']['id']; ?>
">

<div id="edit-form-layout">

<div class="edit-form-row">
  <div class="form-section-label">Site Title</div>
  <input type="text" style="width:200px" name="site_name" value="<?php echo $this->_tpl_vars['site']['name']; ?>
"/>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Page Title Format</div>
  <input type="text" style="width:200px" name="site_title_format" value="<?php echo $this->_tpl_vars['site']['title_format']; ?>
" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Hostname</div>
  <input type="text" style="width:200px" name="site_domain" value="<?php echo $this->_tpl_vars['site']['domain']; ?>
" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Root Directory</div>
  <input type="text" style="width:200px" name="site_root" value="<?php echo $this->_tpl_vars['site']['root']; ?>
" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin Email</div>
  <input type="text" style="width:200px" name="site_admin_email" value="<?php echo $this->_tpl_vars['site']['admin_email']; ?>
" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Select Home Page (Advanced)</div>
  <select name="site_top_page">
    <?php $_from = $this->_tpl_vars['pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
      <?php if ($this->_tpl_vars['page']['info']['id'] != $this->_tpl_vars['site']['error_page_id']): ?>
      <option value="<?php echo $this->_tpl_vars['page']['info']['id']; ?>
"<?php if ($this->_tpl_vars['site']['top_page_id'] == $this->_tpl_vars['page']['info']['id']): ?> selected="selected"<?php endif; ?>>+<?php unset($this->_sections['dashes']);
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

<div class="edit-form-row">
  <div class="form-section-label">Select Search Page (Advanced)</div>
  <select name="site_search_page">
    <?php $_from = $this->_tpl_vars['pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
      <?php if ($this->_tpl_vars['page']['info']['id'] != $this->_tpl_vars['site']['error_page_id'] && $this->_tpl_vars['page']['info']['id'] != $this->_tpl_vars['site']['top_page_id']): ?>
      <option value="<?php echo $this->_tpl_vars['page']['info']['id']; ?>
"<?php if ($this->_tpl_vars['site']['top_page_id'] == $this->_tpl_vars['page']['info']['id']): ?> selected="selected"<?php endif; ?>>+<?php unset($this->_sections['dashes']);
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

<div class="edit-form-row">
  <div class="form-section-label">Select Tag Page (Advanced)</div>
  <select name="site_tag_page">
    <?php $_from = $this->_tpl_vars['pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
      <?php if ($this->_tpl_vars['page']['info']['id'] != $this->_tpl_vars['site']['error_page_id'] && $this->_tpl_vars['page']['info']['id'] != $this->_tpl_vars['site']['top_page_id']): ?>
      <option value="<?php echo $this->_tpl_vars['page']['info']['id']; ?>
"<?php if ($this->_tpl_vars['site']['top_page_id'] == $this->_tpl_vars['page']['info']['id']): ?> selected="selected"<?php endif; ?>>+<?php unset($this->_sections['dashes']);
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

<div class="edit-form-row">
  <div class="form-section-label">Select Error Page (Advanced)</div>
  <select name="site_error_page">
    <?php $_from = $this->_tpl_vars['pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page']):
?>
      <?php if ($this->_tpl_vars['page']['info']['id'] != $this->_tpl_vars['site']['top_page_id']): ?>
      <option value="<?php echo $this->_tpl_vars['page']['info']['id']; ?>
"<?php if ($this->_tpl_vars['site']['error_page_id'] == $this->_tpl_vars['page']['info']['id']): ?> selected="selected"<?php endif; ?>>+<?php unset($this->_sections['dashes']);
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

<div class="buttons-bar">
  <input type="button" value="Cancel" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
smartest'" />
  <input type="submit" name="action" value="Save Changes" />
</div>

</div>
 
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/users" class="right-nav-link"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user.png" border="0" alt="">Users &amp; Permissions</a></li>
  </ul>
</div>

</form>