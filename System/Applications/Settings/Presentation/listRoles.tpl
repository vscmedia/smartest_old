<div id="work-area">

<h3><a href="{$domain}smartest/settings">Control Panel</a> &gt; Users</h3>
<a name="top"></a>

<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="role_id" id="item_id_input" value="" />
</form>

<ul class="{if $content.count > 10}options-list{else}options-grid{/if}" id="{if $content.count > 10}options_list{else}options_grid{/if}">
{foreach from=$roles key=key item="role"}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editRoleTokens?user_id={$role.role_id}'">
  <a class="option" id="item_{$role.role_id}" onclick="setSelectedItem('{$role.role_id}');" >
  <img border="0" src="{$domain}Resources/Icons/user.png">
  {$role.role_label}</a></li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="invisible-actions-list" id="item-specific-actions" style="display:none">
  <li class="permanent-action"><strong>Selected Role</strong></li>

<li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){workWithItem('editRoleTokens');}{/literal}" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit Role Tokens</a></li>

 <li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteRole');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete Role</a></li>

</ul>

<ul class="actions-list">
   <li class="permanent-action"><strong>Users &amp; Permissions</strong></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addUser'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add Role</a></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/listUsers'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> List Users</a></li>
</ul>

</div>