<ul class="tabset">
    <li{if $method == "browseAssetGroup"} class="current"{/if}><a href="{$domain}{$section}/browseAssetGroup?group_id={$group.id}">Browse files</a></li>
    <li{if $method == "editAssetGroupContents"} class="current"{/if}><a href="{$domain}{$section}/editAssetGroupContents?group_id={$group.id}">Edit group contents</a></li>
    <li{if $method == "editAssetGroup"} class="current"{/if}><a href="{$domain}{$section}/editAssetGroup?group_id={$group.id}">Group info</a></li>
</ul>