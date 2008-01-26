<script language="javascript">
var customAssetClassName = false;

{literal}

function updateAssetClassName(){
	if(!customAssetClassName){
		// alert('test');
		document.getElementById("assetclass_name").value = document.getElementById("assetclass_label").value.toSlug();
	}
}
{/literal}
</script>

<div id="work-area">

<h3>Website Manager &gt; Assets &gt; Add a new container</h3>

<div class="instruction">Add your new asset class</div>

<form action="{$domain}assets/insertAssetClass" method="get" style="margin:0px">
<input type="hidden" name="assetclass_assettype_id" value="{$content.type}" />
{if $content.name}
  <input type="hidden" name="assetclass_name" value="{$content.name}" />
{/if}
  <table style="width:750px;margin-left:10px;" border="0" cellspacing="2" cellpadding="1">
    <tr>
      <td style="width:150px" valign="top">Label:</td>
      <td><input type="text" name="assetclass_label" id="assetclass_label" {if !$content.name}onkeyup="updateAssetClassName();"{/if} /></td></tr>
    <tr>
    <tr>
      <td style="width:150px" valign="top">Markup/tag name: </td>
      <td>{if $content.name}{ldelim}container name="{$content.name}"{rdelim}{else}<input type="text" name="assetclass_name" id="assetclass_name" value="{$content.name}" />{/if}</td>
    </tr>
  <tr>
    <td style="width:150px" valign="top" colspan="2" align="right">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save" /></td>
    </tr>
  </table>
</form>

</div>