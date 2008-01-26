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

<h3 id="siteName">Edit Site Parameters</h3>
<form id="updateSiteDetails" name="updateSiteDetails" action="{$domain}{$section}/updateSiteDetails" method="POST" style="margin:0px">

<input type="hidden" name="site_id" value="{$site.id}">

<div id="edit-form-layout">

<div class="edit-form-row">
  <div class="form-section-label">Site Title</div>
  <input type="text" style="width:200px" name="site_name" value="{$site.name}"/>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Page Title Format</div>
  <input type="text" style="width:200px" name="site_title_format" value="{$site.title_format}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Hostname</div>
  <input type="text" style="width:200px" name="site_domain" value="{$site.domain}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Root Directory</div>
  <input type="text" style="width:200px" name="site_root" value="{$site.root}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin Email</div>
  <input type="text" style="width:200px" name="site_admin_email" value="{$site.admin_email}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Select Home Page (Advanced)</div>
  <select name="site_top_page">
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.error_page_id}
      <option value="{$page.info.id}"{if $site.top_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
    {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Select Search Page (Advanced)</div>
  <select name="site_search_page">
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.error_page_id && $page.info.id != $site.top_page_id}
      <option value="{$page.info.id}"{if $site.top_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
    {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Select Tag Page (Advanced)</div>
  <select name="site_tag_page">
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.error_page_id && $page.info.id != $site.top_page_id}
      <option value="{$page.info.id}"{if $site.top_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
    {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Select Error Page (Advanced)</div>
  <select name="site_error_page">
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.top_page_id}
      <option value="{$page.info.id}"{if $site.error_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
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
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/users" class="right-nav-link"><img src="{$domain}Resources/Icons/user.png" border="0" alt="">Users &amp; Permissions</a></li>
  </ul>
</div>

</form>