<script language="javascript">
var domain = '{$domain}';
var section = '{$section}';
{literal}
 function updatePageName(newName){
  	document.getElementById('pageName').innerHTML="Page Details: "+newName;
 }
 
 function hideNotify(){
 	// alert('one');
 	// var hnot = setTimeout("alert('two')",4000); 
	var hnot = setTimeout("document.getElementById('notify').style.display='none'",3500); 
 }
}

{/literal}</script>

<div id="work-area">

<h3 id="siteName">Create a New Site</h3>

<form id="updateSiteDetails" name="buildSite" action="{$domain}{$section}/buildSite" method="post" style="margin:0px" enctype="multipart/form-data">

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
  <input type="text" style="width:200px" name="site_root" value="{$sm_root_dir}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin Email</div>
  <input type="text" style="width:200px" name="site_admin_email" value="{$user.email}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Home Page Title</div>
  <input type="text" style="width:200px" name="site_home_page_title" value="Home" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Home Page Template</div>
  <select name="site_home_page_template">
    {foreach from=$templates item="template"}
    <option value="{$template}">{$template}</option>
    {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Error Page Title</div>
  <input type="text" style="width:200px" name="site_error_page_title" value="Page Not Found" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Error Page Template</div>
  <select name="site_home_page_template">
    {foreach from=$templates item="template"}
    <option value="{$template}">{$template}</option>
    {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Logo (optional)</div>
  <input type="file" style="width:200px" name="site_logo" />
</div>

<div class="buttons-bar">
  <input type="button" value="Cancel" onclick="window.location='{$domain}smartest'" />
  <input type="submit" name="action" value="Save Changes" />
</div>

</div>
 
</div>

<div id="actions-area">

</div>

</form>