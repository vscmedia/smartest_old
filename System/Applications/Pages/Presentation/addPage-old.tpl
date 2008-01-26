<h3>Add a New Page</h3>

<form id="insertPage" name="insertPage" action="{$domain}{$section}/addPage" method="POST" style="margin:0px">

<input type="hidden" name="stage" value="2" />
<input type="hidden" name="site_id" value="{$content.siteInfo.site_id}" />
<input type="hidden" name="user_id" value="{$content.user_id}" />
<input type="hidden" name="add_type" id="add_type" value="direct" />

<table id="addPageDetails" width="850" border="0" cellpadding="0" cellspacing="2" style="width:850px">
  <tr>
    <td class="text" style="width:100px">Title: </td>
    <td align="left">
    	{$content.siteInfo.site_name} | <input type="text" name="page_title" value="{$content.pageInfo.page_title}"></td>
  </tr>
  <tr>
    <td class="text" style="width:100px">Address: </td>
    <td align="left">
      http://{$content.siteInfo.site_domain}/<input type="text" name="page_url" value=""></td>
  </tr>
  <tr>
    <td class="text" style="width:100px">Parent: </td>
    <td align="left">
      <select name="page_parent" style="width:300px">
      	{foreach from=$content.pages item=page}
      		<option value="{$page.info.page_id}"{if $content.parentInfo.page_id == $page.info.page_id} selected{/if}>+{section name="spaces" loop=$page.treeLevel}-{/section} {$page.info.page_title}</option>
      	{/foreach}
      </select></td>
  </tr>
  <tr>
    <td class="text" style="width:100px">Main Template: </td>
    <td align="left">
      <select name="page_template" style="width:300px">
      	{foreach from=$content.templates item=template}
      		<option value="{$template.filename}">{$template.menuname}</option>
      	{/foreach}
      </select></td>
  </tr>
  <tr>
    <td class="text" style="width:100px">Page Type: </td>
    <td align="left">
      <select name="page_type" style="width:300px">
      		<option value="NORMAL">Normal Page</option>
      		<option value="ITEMCLASS">Data Model Leaf Page</option>
      		<option value="FORWARD">Forwarding Page</option>
      </select></td>
  </tr>
  <tr>
    <td class="text" style="width:100px">Select Preset: </td>
    <td align="left">
      <select name="page_preset" style="width:300px">
	<option value="">  --no preset--  </option>
      	{foreach from=$presets item=preset}
      		<option value="{$preset.plp_id}">{$preset.plp_label}</option>
      	{/foreach}
      </select></td>
  </tr>
  <tr>
    <td class="text" valign="top">Keywords: </td>
    <td align="left">
      <textarea name="page_keywords" style="width:500px;height:100px"></textarea>
    </td>
  </tr>
  <tr>
    <td class="text" valign="top">Description: </td>
    <td align="left">
      <textarea name="page_description" style="width:500px;height:60px"></textarea>
    </td>
  </tr>
  
  <tr>
    <td colspan="2" class="submit" align="right">
    	<input type="button" value="Cancel" onclick="window.location='{$domain}{$section}/getSite'" />
    	<input type="submit" onclick="return check()" name="action" value="Save" /></td>
  </tr>
</table>
 {* <div id="tplUploadShowButton">or, alternatively, <a href="javascript:showUploader();">upload a page</a>.</div>
  <div style="display:none;margin-top:8px;margin-bottom:8px" id="tplUploader">Name this Page:<input type="text" name="page_name" /><br />
Upload file: <input type="file" name="page_uploaded" /> <a href="javascript:hideUploader()">never mind</a></div>
  <input type="submit" value="Save" onclick=""/> *}
</form>