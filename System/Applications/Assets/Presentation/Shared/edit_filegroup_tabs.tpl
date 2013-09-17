<ul class="tabset">
    <li{if $method == "browseAssetGroup"} class="current"{/if}><a href="{$domain}{$section}/browseAssetGroup?group_id={$group.id}">{if $group.is_gallery}{$_l10n_strings.groups.group_tabs.browse_reorder}{else}{$_l10n_strings.groups.group_tabs.browse}{/if}</a></li>
    <li{if $method == "editAssetGroupContents"} class="current"{/if}><a href="{$domain}{$section}/editAssetGroupContents?group_id={$group.id}">{$_l10n_strings.groups.group_tabs.add_remove_files}</a></li>
    <li{if $method == "editAssetGroup"} class="current"{/if}><a href="{$domain}{$section}/editAssetGroup?group_id={$group.id}">{if $group.is_gallery}{$_l10n_strings.groups.group_tabs.gallery_info}{else}{$_l10n_strings.groups.group_tabs.group_info}{/if}</a></li>
</ul>