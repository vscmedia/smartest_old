<script language="javascript">
{literal}
function cancelForm(){
window.location={$domain}{$section}/getAssetTypeMembers?assettype_code=TMPL;
}
{/literal}
</script>


<h3>Edit Template Asset</h3>
<form action="{$domain}{$section}/updateAsset" method="post" name="newTemplate" enctype="multipart/form-data">
  <input type="hidden" name="assettype_code" value="{$content.assettype_code}" />
  <input type="hidden" name="asset_id" value="{$content.asset_id}" />

  <div style="width:100%" id="editTMPL">
    Template Filename :  {$content.asset_details.asset_stringid}<br />
    <textarea name="asset_content" id="tpl_textArea" wrap="virtual" >{$details}</textarea>
  </div>
  <div class="buttons-bar"><input type="submit" value="Save Changes" />
  <input type="button" onclick="cancelForm();" value="Cancel" /></div>
</form>