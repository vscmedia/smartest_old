<div id="work-area">

<h3>Publish {if $item}Meta-{/if}Page</h3>

{if $site_enabled != '1'}

<div class="warning"><strong>Warning</strong>: This site is not currently enabled. You can give this page the 'published' status, but it will not be available until the site is enabled.</div>

{/if}

<form action="{$domain}{$section}/publishPage" method="post">

<input type="hidden" name="page_id" value="{$page_id}" />
{if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}

{if $allow_publish}

{if $count < 1}

<div class="instruction">Are you sure you want to publish this page?</div>

{elseif $count == 1}

<div class="warning"><strong>Warning</strong>: The {$undefined_asset_classes[0].info.type} <strong>{$undefined_asset_classes[0].info.assetclass_name}</strong> is not defined.</div>
	
{elseif $count > 1}

<div class="warning"><strong>Warning</strong>: The following elements are not defined in the draft version of this page:</div>

<ul class="basic-list">

	{foreach from=$undefined_asset_classes item="undefinedAssetClass"}
	<li>{$undefinedAssetClass.info.type} <b>{$undefinedAssetClass.info.assetclass_name}</b></li>
	{/foreach}

</ul>

<div class="special-box">Publishing this page will cause undefined placeholders and containers to be included on a live page.<br />Are you sure you want to continue?</div>

{/if}{* number of undefined elements *}

{if $item}

<div class="edit-form-row">
  {if $item.is_published}
  <div class="form-section-label">Would you like to re-publish this {$item._model.name|strtolower}?</div>
  <select name="publish_item">
    <option value="IGNORE">No, I'll do that manually. Just update page elements.</option>
    <option value="PUBLISH" selected="selected">Yes, re-publish the {$item._model.name|strtolower} '{$item.name}'</option>
  </select>
  {else}
  <div class="form-section-label">This {$item._model.name|strtolower} is currently not published. Would you like to publish it?</div>
  <select name="publish_item">
    <option value="IGNORE" selected="selected">No, I'll do that manually. Just update page elements.</option>
    <option value="PUBLISH">Yes, publish the {$item._model.name|strtolower} '{$item.name}'</option>
  </select>
  {/if}{* whether the item is published *}
</div>

{/if}{* whether this is an item page *}

{else}

<div class="instruction">You can't publish this page at the moment</div>

{/if}

<div class="buttons-bar">
  <input type="button" onclick="cancelForm();" value="Cancel" />
  {if $allow_publish}<input type="submit" value="Publish" />{/if}
</div>
	
</div>

</form>