<script language="javascript" type="text/javascript">

</script>

<div id="work-area">

<h3>User accounts</h3>
<a name="top"></a>

<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="user_id" id="item_id_input" value="" />
</form>

<ul class="options-list" id="options_list">
{foreach from=$users key=key item=user}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editUser?user_id={$user.id}'">
    <a class="option" id="item_{$user.id}" onclick="setSelectedItem('{$user.id}');" >
      <img border="0" src="{$domain}Resources/Icons/user.png">
  {$user.fullname}</a></li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<td valign="top" style="width:250px">
<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selection Options</b></li>
  <li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){workWithItem('editUser');}{/literal}" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit User Details</a></li>

<li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){workWithItem('editUserTokens');}{/literal}" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Edit User Tokens</a></li>

 <li class="permanent-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteUser');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete User</a></li>


</ul>
<ul class="actions-list">
   <li><b>Users &amp; Tokens</b></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add User</a></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/user_roles'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> Roles</a></li>
</ul></td>

</tr>
</table>

</div>