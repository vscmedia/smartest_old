<ul class="tabset">
    <li{if $method == "editPlaceholder"} class="current"{/if}><a href="{$domain}{$section}/editPlaceholder?placeholder_id={$placeholder.id}">Edit placeholder</a></li>
    <li{if $method == "placeholderDefinitions"} class="current"{/if}><a href="{$domain}{$section}/placeholderDefinitions?placeholder_id={$placeholder.id}">Definitions</a></li>
</ul>