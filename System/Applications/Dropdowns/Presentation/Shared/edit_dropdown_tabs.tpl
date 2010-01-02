<ul class="tabset">
    <li{if $method == "dropdownInfo"} class="current"{/if}><a href="{$domain}{$section}/dropdownInfo?dropdown_id={$dropdown.id}">Dropdown info</a></li>
    <li{if $method == "editValues"} class="current"{/if}><a href="{$domain}{$section}/editValues?dropdown_id={$dropdown.id}">Values</a></li>
</ul>