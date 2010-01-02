<ul class="tabset">
  <li{if $method == "getAssetTypeMembers"} class="current"{/if}><a href="{$domain}{$section}/getAssetTypeMembers?asset_type={$type.id}">All files of this type</a></li>
  <li{if $method == "assetGroupsByType"} class="current"{/if}><a href="{$domain}{$section}/assetGroupsByType?asset_type={$type.id}">File groups</a></li>
</ul>