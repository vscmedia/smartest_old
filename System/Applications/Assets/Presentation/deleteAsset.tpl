<script language="javascript">
{literal}

{/literal}
</script>

<div id="work-area">

<h3>Delete Asset</h3>
<form action="{$domain}{$section}/" method="post" name="deleteForm" enctype="multipart/form-data">
  <input type="hidden" name="assettype_code" value="{$content.assettype_code}" />
  <input type="hidden" name="asset_id" value="{$content.asset_id}" />
  <div style="width:100%" id="editCss">
	{if $draft_count==0 && $live_count==0}
	<script>
	confirm("Are you sure you want to delete this Asset ? ");
	</script>
	{/if}	
  </div>
 </form>
 
</div>