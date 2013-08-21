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

{* <div style="height:100px;border-radius:10px;-moz-border-radius:10px;background-color:#222;overflow:hidden">
  {foreach from=$site_images item="asset"}
  {$asset.image.height_33}
  {/foreach}
</div> *}

<form id="updateSiteDetails" name="updateSiteDetails" action="{$domain}{$section}/updateSiteDetails" method="POST" style="margin:0px" enctype="multipart/form-data">

<input type="hidden" name="site_id" value="{$site.id}">

<div id="edit-form-layout">

<div class="edit-form-row">
  <div class="form-section-label">Public title</div>
  <input type="text" name="site_name" value="{$site.name}"/><div class="form-hint">This will take effect on your pages the next time they are published</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Internal label</div>
  <input type="text" name="site_internal_label" value="{$site.internal_label}"/>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Page title format</div>
  <input type="text" name="site_title_format" value="{$site.title_format}" /><div class="form-hint">This will take effect on your pages the next time they are published</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Hostname</div>
  <input type="text" name="site_domain" value="{$site.domain}" /><div class="form-hint">Please be careful. The wrong value here will make your site inaccessible.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Logo</div>
  <select name="site_logo_image_asset_id">
    <option value="">None</option>
{foreach from=$logo_assets item="logo_asset"}
    <option value="{$logo_asset.id}"{if $site.logo_image_asset_id == $logo_asset.id} selected="selected"{/if}>{$logo_asset.label}</option>
{/foreach}
  </select><br />
  <input type="file" name="site_logo" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin email</div>
  <input type="text" name="site_admin_email" value="{$site.admin_email}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Site ID</div>
  <code>{$site.unique_id}</code> {help id="desktop:install_ids" buttonize="true"}What's this?{/help}
</div>

<div class="edit-form-row">
  <div class="form-section-label">Site language</div>
  <select name="site_language">
  {foreach from=$_languages item="lang" key="langcode"}
    {if $langcode != "zxx"}<option value="{$langcode}"{if $site.language_code == $langcode} selected="selected"{/if}>{$lang.label}</option>{/if}
  {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Site status</div>
  <label for="enable-site">Enabled</label> <input type="radio" id="enable-site" name="site_is_enabled" value="1"{if $site.is_enabled == 1} checked="checked"{/if} />
  <label for="disable-site">Disabled</label> <input type="radio" id="disable-site" name="site_is_enabled" value="0"{if $site.is_enabled == 0} checked="checked"{/if} /><span class="form-hint">This will take effect immediately</span>
</div>

{* <div class="edit-form-row">
  <div class="form-section-label">Select Home Page (Advanced)</div>
  <select name="site_top_page">
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.error_page_id}
      <option value="{$page.info.id}"{if $site.top_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
    {/foreach}
  </select>
</div> *}

<div class="edit-form-row">
  <div class="form-section-label">Search Page (Advanced)</div>
  <select name="site_search_page">
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.error_page_id && $page.info.id != $site.top_page_id}
      <option value="{$page.info.id}"{if $site.search_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
    {/foreach}
  </select>
  <br /><div class="form-hint">This page will handle search queries made to http://{$site.domain}{$domain}search.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Tag Page (Advanced)</div>
  <select name="site_tag_page">
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.error_page_id && $page.info.id != $site.top_page_id}
      <option value="{$page.info.id}"{if $site.tag_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
    {/foreach}
  </select>
  <br /><div class="form-hint">This page will be loaded when a tag is requested, eg: http://{$site.domain}{$domain}tag/elephants.html.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">User Profile Page (Advanced)</div>
  <select name="site_user_page">
    {if !$site.user_page_id}<option value="NEW">Create a new page for this purpose</option>{/if}
    {foreach from=$pages item="page"}
      {if $page.info.id != $site.top_page_id}
      <option value="{$page.info.id}"{if $site.user_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {/if}
    {/foreach}
  </select>
  <br /><div class="form-hint">This page will be loaded when a user profile is requested.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">404 Error Page (Advanced)</div>
  <select name="site_error_page">
    {foreach from=$pages item="page"}
      {* Sergiy: Allow top page (why not?), I use this approach on my website already many years *}
      {* if $page.info.id != $site.top_page_id *}
      <option value="{$page.info.id}"{if $site.error_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
      {* /if *}
    {/foreach}
  </select>
  <br /><div class="form-hint">This page will be loaded when an unknown or unpublished page is requested, eg: http://{$site.domain}{$domain}kigsdfkjhg.</div>
</div>

<div class="breaker"></div>

<div class="buttons-bar">
  <input type="button" value="Cancel" onclick="window.location='{$domain}smartest'" />
  <input type="submit" name="action" value="Save Changes" />
</div>

</div>
 
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/users" class="right-nav-link"><img src="{$domain}Resources/Icons/user.png" border="0" alt=""> Users &amp; Permissions</a></li>
    <li class="permanent-action"><a href="{$domain}desktop/twitterSettings" class="right-nav-link"><img src="{$domain}Resources/Icons/cog.png" border="0" alt=""> Twitter settings</a></li>
  </ul>
</div>

</form>