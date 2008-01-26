
<script language="javascript">
{literal}
function cancelForm(){
window.location={$domain}{$section}/getAssetTypeMembers?assettype_code={$content.assettype_code};
}
{/literal}
</script>

<h3>Delete Asset</h3>
<form action="{$domain}{$section}/" method="post" name="deleteForm" enctype="multipart/form-data">
  <input type="hidden" name="assettype_code" value="{$content.assettype_code}" />
  <input type="hidden" name="asset_id" value="{$content.asset_id}" />
  <div style="width:100%" id="del">
	
  </div>
 </form> 
	{elseif $draftpage_count neq 0 && $livepage_count eq 0}
	This asset is in use on the draft versions of the following pages.<br />
	
	
	{foreach item=draftasset from=$draft_assets}
		{$draftasset.page_title} ({$draftasset.page_name})<br />
	{/foreach}
	
	Are you sure you want to delete it?<br />
	<a href="#" onclick="javascript:cancelForm()">[Cancel]</a><a href="#" onclick="javascript:document.deleteForm.submit();">  [OK]  </a><br /><br />
	{/if}
	{elseif $draftpage_count eq 0 && $livepage_count neq 0}
	This asset is in use on the following live pages, and cannot be deleted.<br />
	 {foreach item=liveasset from=$live_assets}
		{$liveasset.page_title} ({$liveasset.page_name})<br />
	{/foreach}
	<a href="#" onclick="javascript:deleteForm.submit();">[OK]  </a><br /><br />
	{else}
	This asset is in use on the live versions of the following pages.<br />
	{foreach item=draftasset from=$draft_assets}
		{$draftasset.page_title} ({$draftasset.page_name})<br />
		{/foreach}
	And also is in use on the draft versions of the following pages <br />
		{foreach item=draftasset from=$draft_assets}
		{$draftasset.page_title} ({$draftasset.page_name})<br />
		{/foreach}
			so cannot be deleted.
	<a href="#" onclick="javascript:cancelForm()">	[OK]</a>
	{/if}