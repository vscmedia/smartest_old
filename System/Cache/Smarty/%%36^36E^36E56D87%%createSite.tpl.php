<?php /* Smarty version 2.6.18, created on 2007-11-30 11:15:07
         compiled from /var/www/html/System/Applications/Desktop/Presentation/createSite.tpl */ ?>
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

<h3 id="siteName">Create a New Site</h3>

<form id="updateSiteDetails" name="buildSite" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/buildSite" method="post" style="margin:0px" enctype="multipart/form-data">

<div id="edit-form-layout">
  
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />

<div class="edit-form-row">
  <div class="form-section-label">Site Title</div>
  <input type="text" style="width:200px" name="site_name" value="My Smartest Web Site"/>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Page Title Format</div>
  <input type="text" style="width:200px" name="site_title_format" value="$site | $page" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Hostname</div>
  <input type="text" style="width:200px" name="site_domain" value="" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Root Directory</div>
  <input type="text" style="width:200px" name="site_root" value="<?php echo $this->_tpl_vars['sm_root_dir']; ?>
" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin Email</div>
  <input type="text" style="width:200px" name="site_admin_email" value="<?php echo $this->_tpl_vars['user']['email']; ?>
" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Home Page Title</div>
  <input type="text" style="width:200px" name="site_home_page_title" value="Home" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Home Page Template</div>
  <select name="site_home_page_template">
    <?php $_from = $this->_tpl_vars['templates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['template']):
?>
    <option value="<?php echo $this->_tpl_vars['template']; ?>
"><?php echo $this->_tpl_vars['template']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Error Page Title</div>
  <input type="text" style="width:200px" name="site_error_page_title" value="Page Not Found" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Error Page Template</div>
  <select name="site_home_page_template">
    <?php $_from = $this->_tpl_vars['templates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['template']):
?>
    <option value="<?php echo $this->_tpl_vars['template']; ?>
"><?php echo $this->_tpl_vars['template']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Logo (optional)</div>
  <input type="file" style="width:200px" name="site_logo" />
</div>

<div class="buttons-bar">
  <input type="button" value="Cancel" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
smartest'" />
  <input type="submit" name="action" value="Save Changes" />
</div>

</div>
 
</div>

<div id="actions-area">

</div>

</form>