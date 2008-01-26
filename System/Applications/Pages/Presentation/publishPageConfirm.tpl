<div id="work-area">

<h3>Publish Page</h3>

<form action="{$domain}{$section}/publishPage" method="get">

<input type="hidden" name="page_id" value="{$page_id}" />

{if $allow_publish}

{if $count < 1}

<div class="instruction">Are you sure you want to publish this page?</div>

{elseif $count == 1}

<div class="instruction"><b>Warning</b>: The {$undefined_asset_classes[0].info.type} "{$undefined_asset_classes[0].info.assetclass_label}" is not defined.</div>
	
{elseif $count > 1}

<div class="instruction"><b>Warning</b>: The following elements are not defined in the draft version of this page:</div>

<ul class="basic-list">

	{foreach from=$undefined_asset_classes item="undefinedAssetClass"}
	<li>{$undefinedAssetClass.info.type} <b>{$undefinedAssetClass.info.assetclass_name}</b></li>
	{/foreach}

</ul>

<div class="instruction">Publishing this page will cause undefined placeholders and containers to be included on a live page.<br />Are you sure you want to continue?</div>

{/if}

{else}

<div class="instruction">You can't publish this page at the moment</div>

{/if}

<div class="buttons-bar">
  <input type="button" onclick="window.location='{$domain}{$section}/editPage?page_id={$page_id}'" value="Cancel" />
  {if $allow_publish}<input type="submit" value="Publish" />{/if}
</div>
	
</div>