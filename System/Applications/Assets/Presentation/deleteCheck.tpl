<script language="javascript">
{literal}

function cancelForm(){
	window.location={$domain}{$section}/getAssetTypeMembers?assettype_code=CSS;
}

function confirmDelete(){
	confirm("Are you sure you want to delete this Asset? ");
}

{/literal}
</script>

<div id="work-area">

<h3>Delete Asset</h3>
<form action="{$domain}{$section}/updateAsset" method="post" name="newCss" enctype="multipart/form-data">
  <input type="hidden" name="assettype_code" value="{$content.assettype_code}" />
  <input type="hidden" name="asset_id" value="{$content.asset_id}" />
<input type="text" name="draft_count" value="{$draft_count}" />
  <div style="width:100%" id="editCss">
	
  </div>
  <input type="submit" value="Save Changes" />
  <input type="button" onclick="cancelForm();" value="Cancel" />
</form>

</div>