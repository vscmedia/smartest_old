<ul class="tabset">
    <li{if $method == 'editUser'} class="current"{/if}><a href="{$domain}{$section}/editUser?user_id={$user.id}">Profile</a></li>
    {if $show_tokens_edit_tab}<li{if $method == 'editUserTokens'} class="current"{/if}><a href="{$domain}{$section}/editUserTokens?user_id={$user.id}">Permissions</a></li>{/if}
</ul>