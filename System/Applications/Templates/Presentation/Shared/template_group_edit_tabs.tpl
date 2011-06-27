<ul class="tabset">
    <li{if $method == "browseTemplateGroup"} class="current"{/if}><a href="{$domain}{$section}/browseTemplateGroup?group_id={$group.id}">Templates in this group</a></li>
    <li{if $method == "editTemplateGroupContents"} class="current"{/if}><a href="{$domain}{$section}/editTemplateGroupContents?group_id={$group.id}">Edit group contents</a></li>
    <li{if $method == "editTemplateGroup"} class="current"{/if}><a href="{$domain}{$section}/editTemplateGroup?group_id={$group.id}">Group info</a></li>
</ul>