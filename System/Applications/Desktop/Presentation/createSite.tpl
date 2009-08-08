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
  <div class="form-section-label">Host name</div>
  <input type="text" style="width:200px" name="site_domain" value="" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin email</div>
  <input type="text" style="width:200px" name="site_admin_email" value="{$user.email}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Master template</div>
  <select name="site_master_template">
    <option value="_DEFAULT">None for now, I will create one later</option>
    <option value="_BLANK"{if !$allow_create_master_tpl} disabled="disabled"{/if}>Create a new, blank template{if !$allow_create_master_tpl} (directory is not writable){/if}</option>
    {foreach from=$templates item="template"}
    <option value="{$template}">Use {$template}</option>
    {/foreach}
  </select>
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