<?php /* Smarty version 2.6.18, created on 2007-11-26 03:45:29
         compiled from /var/www/html/System/Applications/Settings/Presentation/listUsers.tpl */ ?>
<script language="javascript" type="text/javascript">

</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/settings">Control Panel</a> &gt; Users</h3>
<a name="top"></a>

<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="user_id" id="item_id_input" value="" />
</form>

<ul class="<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options-list<?php else: ?>options-grid<?php endif; ?>" id="<?php if ($this->_tpl_vars['content']['count'] > 10): ?>options_list<?php else: ?>options_grid<?php endif; ?>">
<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['user']):
?>
  <li style="list-style:none;" ondblclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editUser?user_id=<?php echo $this->_tpl_vars['user']['user_id']; ?>
'">
  <a class="option" id="item_<?php echo $this->_tpl_vars['user']['user_id']; ?>
" onclick="setSelectedItem('<?php echo $this->_tpl_vars['user']['user_id']; ?>
');" >
  <img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user.png">
  <?php echo $this->_tpl_vars['user']['username']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
</ul>

</div>

<div id="actions-area">

<td valign="top" style="width:250px">
<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li class="permanent-action"><b>Selection Options</b></li>
  <li class="permanent-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage){workWithItem(\'editUser\');}'; ?>
" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> Edit User Details</a></li>

<li class="permanent-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage){workWithItem(\'editUserTokens\');}'; ?>
" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/page_code.png"> Edit User Tokens</a></li>

 <li class="permanent-action"><a href="javascript:nothing()" onclick="<?php echo 'if(selectedPage && confirm(\'Are you sure you want to delete this page?\')){workWithItem(\'deleteUser\');}'; ?>
"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/package_delete.png"> Delete User</a></li>


</ul>
<ul class="actions-list">
   <li class="permanent-action"><b>Users &amp; Permissions</b></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addUser'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user_add.png"> Add User</a></li>
   <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/listRoles'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user_add.png"> Role Editor</a></li>
</ul></td>

</tr>
</table>

</div>