<script language="javascript">
{literal}

function cancelForm(){
    window.location={$domain}{$section}/getAssetTypeMembers?assettype_code={$content.$assettype_code};
}

{/literal}
</script>


<h3>Edit Text Asset</h3>

<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

    <input type="hidden" name="asset_id" value="{$content.asset_id}" />
    <input type="hidden" name="assettype_code" value="{$content.assettype_code}" />
    
    Name of the Asset:  {$content.asset_details.asset_stringid}<br />
    <div id="textarea-holder" style="width:100%">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0">{$content}</textarea>
        <div class="buttons-bar">
            <input type="submit" value="Save Changes" />
            <input type="button" onclick="cancelForm();" value="Cancel" />
        </div>
    <div>
        
</form>