<ul class="tabset">
    <li{if $method == "editPageGroup"} class="current"{/if}><a href="{$domain}{$section}/editPageGroup?group_id={$group.id}">Edit page group</a></li>
    {if count($members) > 1}<li{if $method == "editPageGroupOrder"} class="current"{/if}><a href="{$domain}{$section}/editPageGroupOrder?group_id={$group.id}">Change order</a></li>{/if}
</ul>