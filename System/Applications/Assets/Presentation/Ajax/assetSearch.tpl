<ul>
{foreach from=$assets item="asset"}
  <li id="assetOption-{$asset.id}">{$asset.label|summary:"50"}<span class="informal"> {$asset.type_info.label}</span></li>
{/foreach}
</ul>