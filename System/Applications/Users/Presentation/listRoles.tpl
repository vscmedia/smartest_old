<div id="work-area">

<h3><a href="{$domain}smartest/users">User accounts</a> &gt; Roles</h3>
<a name="top"></a>

<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="role_id" id="item_id_input" value="" />
</form>

<ul class="{if $num_roles > 10}options-list{else}options-grid{/if}" id="{if $num_roles > 10}options_list{else}options_grid{/if}">
{foreach from=$roles key=key item="role"}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editRoleTokens?user_id={$role.id}'">
  <a class="option" id="item_{$role.id}" onclick="setSelectedItem('{$role.id}');" >
  <img border="0" src="{$domain}Resources/Icons/user.png">
  {$role.label}</a></li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="invisible-actions-list" id="item-specific-actions" style="display:none">
  <li><strong>Selected Role</strong></li>
  <li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){workWithItem('editRoleTokens');}{/literal}" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit Role Tokens</a></li>
  <li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteRole');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete Role</a></li>
</ul>

<ul class="actions-list">
   <li><strong>Users &amp; Permissions</strong></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/user_roles/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add role</a></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add user</a></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> List users</a></li>
</ul>

</div>