<div id="work-area">

<h3>Delete Asset</h3>
	
	<form action="{$domain}{$section}/deleteAsset" method="post">
	
	<input type="hidden" name="asset_id" value="{$asset.id}" />
	
	{if $num_live_instances == 0 && $num_draft_instances == 0}
	
	<!-- Asset can be deleted if permissions are ok -->
	
	<div class="instruction">Are you sure you want to delete this file?</div>
	
	<div class="buttons-bar">
	  <input type="button" value="Cancel" onclick="cancelForm()" />
	  <input type="submit" value="OK" />
	</div>
	
	{elseif $num_live_instances == 0 && $num_draft_instances > 0}
	
	<!-- Asset can still be deleted, providing user has permission, but warning is displayed-->
	
	<div class="instruction">Careful. This file is in use on one or more changed pages. You may still delete it, but those definitions will be reverted to their live definition</div>
	
	<table class="basic-table" cellspacing="1" cellpadding="2" border="0">
	  <tr class="head">
	   <td><b>Placeholder</b></td>
	   <td><b>Page</b></td>
	   <td><b>Site</b></td>
	   <td></td>
	  </tr>
	  {foreach from=$draft_instances item="instance"}
	  <tr class="{cycle values="odd,even"}">
	   <td>{$instance.assetclass.name}</td>
	   <td>{$instance.page.title}</td>
	   <td>{$instance.site.name}</td>
	   <td><a href="{$domain}websitemanager/definePlaceholder?page_id={$instance.page.webid}&amp;assetclass_id={$instance.assetclass.name}">Edit</a></td>
	  </tr>
	  {/foreach}
	</table>
	
	<div class="buttons-bar">
	  <input type="submit" value="Proceed" />
	  <input type="button" value="Go Back (Recommended)" onclick="cancelForm()" />
	</div>
	
	{else}
	
	<!-- Asset cannot be deleted because it is used on live pages -->
	
	<div class="instruction">This file can't be deleted because it is in use on one or more live pages:</div>
	
	<table class="basic-table" cellspacing="1" cellpadding="2" border="0">
	  <tr>
	   <td><b>Placeholder<b></td>
	   <td><b>Page<b></td>
	   <td><b>Site<b></td>
	   <td></td>
	  </tr>
	  {foreach from=$live_instances item="instance"}
	  <tr>
	   <td>{$instance.assetclass.name}</td>
	   <td>{$instance.page.title}</td>
	   <td>{$instance.site.name}</td>
	   <td><a href="{$domain}websitemanager/definePlaceholder?page_id={$instance.page.webid}&amp;assetclass_id={$instance.assetclass.name}">Edit</a></td>
	  </tr>
	  {/foreach}
	</table>
	
	<div class="buttons-bar"><input type="button" onclick="cancelForm()" value="OK" /></div>
	
	{/if}
	
	</form>

</div>