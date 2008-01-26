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

<h3 id="user">User Details: {$content.pageInfo.page_title}</h3>
<form id="updateSiteDetails" name="updateSiteDetails" action="{$domain}{$section}/updateSiteDetails" method="POST" style="margin:0px">

<div id="work-area">
{foreach from=$sitedetails item=sitedetails}
<input type="hidden" name="site_id" value="{$sitedetails.site_id}">
<table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="2" style="width:100%">
  <tr>
    <td class="text" valign="top">Username : </td>
    <td align="left"><input type="text" style="width:200px" name="site_name" value="{$sitedetails.site_name}">
      </td>
  </tr>
<tr>
    <td class="text" style="width:100px" valign="top"> Role : </td>
    <td align="left">
    	<input type="text"  style="width:200px" name="site_title_format" value="{$sitedetails.site_title_format}" >
    	</td>
  </tr>
	<tr>
	<td class="text" style="width:100px" valign="top"> First name :</td>
    <td align="left">
    	<input type="text" style="width:200px" name="site_domain" value="{$sitedetails.site_domain}" >
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Last name : </td>
    <td align="left">
    	<input type="text"  style="width:200px" name="site_root" value="{$sitedetails.site_root}" >
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Nickname : </td>
    <td align="left">
    <input type="text" style="width:200px" name="site_error_title" value="{$sitedetails.site_error_title}" >
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Display name publicly as : </td>
    <td align="left">
    	<input type="text"  style="width:200px" name="site_error_tpl" value="{$sitedetails.site_error_tpl}" >
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> E-mail : </td>
    <td align="left">
     <input type="text"  style="width:200px" name="site_admin_email" value="{$sitedetails.site_admin_email}" >
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Website : </td>
    <td align="left">
    	 <input type="text" style="width:200px" name="site_top_page_id" value="{$sitedetails.site_top_page_id}" >
    	</td>
  </tr>

<tr>
	<td class="text" style="width:100px" valign="top"> AIM : </td>
    <td align="left">
    	 <input type="text" style="width:200px" name="site_top_page_id" value="{$sitedetails.site_top_page_id}" >
    	</td>
  </tr>

<tr>
	<td class="text" style="width:100px" valign="top"> Yahoo IM : </td>
    <td align="left">
    	 <input type="text" style="width:200px" name="site_top_page_id" value="{$sitedetails.site_top_page_id}" >
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> About the user : </td>
    <td align="left">
    	<textarea name="page_description" style="width:500px;height:60px">Share a little biographical information to fill out your profile. This may be shown publicly.</textarea>
    	</td>
 </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Update User's Password </td>
    <td align="left">
    	 <input type="text" style="width:200px" name="site_top_page_id" value="" >
    	</td>
  </tr>

<tr>
    <td colspan="2" class="submit" align="right">
    	<input type="button" value="Cancel" onclick="cancelForm();" />
    	<input type="submit" name="action" value="Save" />
    	{if $content.saved == 'true'}
		<input type="button" value="Done" onclick="window.location='{$domain}{$section}/getSitePages?site_id={$content.pageInfo.site_id}'" /></div>
		{else}
		<input type="button" value="Done" onclick="window.location='{$domain}{$section}/getSitePages?site_id={$content.pageInfo.site_id}'" disabled />
		{/if}
    	<input type="submit" name="action" value="Publish" disabled /></td>
  </tr>
</table>{/foreach}
</div>
<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="{$domain}{$section}/getSite" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working </a></li>
  </ul>
</div>
</form>