<script language="javascript" type="text/javascript">

</script>

<div id="work-area">

<h3><a href="{$domain}smartest/settings">Control Panel</a> &gt; Users</h3>
<a name="top"></a>

<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="user_id" id="item_id_input" value="" />
</form>

<ul class="{if $content.count > 10}options-list{else}options-grid{/if}" id="{if $content.count > 10}options_list{else}options_grid{/if}">
{foreach from=$users key=key item=user}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editUser?user_id={$user.user_id}'">
  <a class="option" id="item_{$user.user_id}" onclick="setSelectedItem('{$user.user_id}');" >
  <img border="0" src="{$domain}Resources/Icons/user.png">
  {$user.username}</a></li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<td valign="top" style="width:250px">
<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li class="permanent-action"><b>Selection Options</b></li>
  <li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){workWithItem('editUser');}{/literal}" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit User Details</a></li>

<li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){workWithItem('editUserTokens');}{/literal}" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit User Tokens</a></li>

 <li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteUser');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete User</a></li>


</ul>
<ul class="actions-list">
   <li><b>Users &amp; Tokens</b></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addUser'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add User</a></li>
   {* <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/listRoles'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Role Editor</a></li> *}
</ul></td>

</tr>
</table>

</div>