<ul class="tabset">
    <li{if $method == 'editUser'} class="current"{/if}><a href="{$domain}{$section}/editUser?user_id={$user.id}">Details</a></li>
    <li{if $method == 'editUserTokens'} class="current"{/if}><a href="{$domain}{$section}/editUserTokens?user_id={$user.id}">Tokens</a></li>
</ul>