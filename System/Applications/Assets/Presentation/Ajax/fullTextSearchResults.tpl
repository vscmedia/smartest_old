<ul>
{foreach from=$assets item="asset"}
  <li id="assetOption-{$asset.id}">{$asset.label|summary:"45"}</li>
{/foreach}
</ul>