<ul class="tabset">
    <li{if $method == "previewSet"} class="current"{/if}><a href="{$domain}{$section}/previewSet?set_id={$set.id}">Browse</a></li>
    <li{if $method == "editSet"} class="current"{/if}><a href="{$domain}{$section}/editSet?set_id={$set.id}">Edit set info</a></li>
    {if $set.type == "STATIC"}<li{if $method == "editStaticSetOrder"} class="current"{/if}><a href="{$domain}{$section}/editStaticSetOrder?set_id={$set.id}">Change order</a></li>{/if}
    {if $set.type == "DYNAMIC"}<li{if $method == "editDynamicSetConditions"} class="current"{/if}><a href="{$domain}{$section}/editDynamicSetConditions?set_id={$set.id}">Edit rules</a></li>{/if}
</ul>