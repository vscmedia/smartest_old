<ul class="tabset">
    <li{if $method == "editAsset"} class="current"{/if}><a href="{$domain}{$section}/editAsset?asset_id={$asset.id}">Edit file</a></li>
    {if $asset.type_info.source_editable}<li{if $method == "editTextFragmentSource"} class="current"{/if}><a href="{$domain}{$section}/editTextFragmentSource?asset_id={$asset.id}">Edit file source</a></li>{/if}
    <li{if $method == "assetInfo"} class="current"{/if}><a href="{$domain}{$section}/assetInfo?asset_id={$asset.id}">Edit file info</a></li>
    {if $asset.type_info.parsable}<li{if $method == "textFragmentElements"} class="current"{/if}><a href="{$domain}{$section}/textFragmentElements?asset_id={$asset.id}">Attachments</a></li>{/if}
    <li{if $method == "previewAsset"} class="current"{/if}><a href="{$domain}{$section}/previewAsset?asset_id={$asset.id}">Preview</a></li>
</ul>