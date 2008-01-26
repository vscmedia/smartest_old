<?php /* Smarty version 2.6.18, created on 2007-11-26 03:46:22
         compiled from /var/www/html/System/Applications/Settings/Presentation/editUserTokens.tpl */ ?>
<script language="javascript">
<?php echo '

function setMode(mode){

	document.getElementById(\'transferAction\').value=mode;

	if(mode == "add"){
		document.getElementById(\'add_button\').disabled=false;
		document.getElementById(\'remove_button\').disabled=true;
		
	}else if(mode == "remove"){
		document.getElementById(\'add_button\').disabled=true;
		document.getElementById(\'remove_button\').disabled=false;		
	}	
	
}

function executeTransfer(){
	document.transferForm.submit();
}

'; ?>

</script>

<div id="work-area">

<h3><a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
">Settings</a> &gt; <a href="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/users">Users</a> &gt; Edit User Tokens</h3>

<div class="instruction">Modifying User: <strong><?php echo $this->_tpl_vars['user']['username']; ?>
</strong></div>

<form action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/transferTokens" method="post" name="transferForm">
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
<input type="hidden" name="user_id" value="<?php echo $this->_tpl_vars['user']['id']; ?>
" />

<table border="0" cellspacing="0" cellpadding="0" style="width:550px">
  
  <tr>
    <td colspan="3">Granting permissions on which site?<br />
      <select name="site_id" onchange="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/editUserTokens?user_id=<?php echo $this->_tpl_vars['user']['id']; ?>
&amp;site_id='+this.value;">
        <?php if ($this->_tpl_vars['allow_global']): ?><option value="GLOBAL">All Sites</option><?php endif; ?>
        <?php $_from = $this->_tpl_vars['sites']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['site']):
?>
        <option value="<?php echo $this->_tpl_vars['site']['id']; ?>
"<?php if ($this->_tpl_vars['site']['id'] == $this->_tpl_vars['site_id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['site']['name']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select><br /><br />
    </td>
  </tr>
  
  <tr>
    
    <td valign="top">
      Permissions not granted:<br />
      <select name="tokens[]" size="2" multiple style="width:300px;height:200px;" onclick="setMode('add')">
        <?php $_from = $this->_tpl_vars['tokens']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['values']):
?>
        <option value="<?php echo $this->_tpl_vars['values']['token_id']; ?>
" ><?php echo $this->_tpl_vars['values']['token_description']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
    </td>
    
    <td valign="middle">
      <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
      <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
    </td>
    
    <td valign="top">
      Permissions granted:<br />
      <select name="sel_tokens[]"  id='sel_roles' size="4" multiple style="width:300px;height:200px" onclick="setMode('remove')" >	
        <?php $_from = $this->_tpl_vars['utokens']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
?>
        <option value="<?php echo $this->_tpl_vars['value']['token_id']; ?>
"  ><?php echo $this->_tpl_vars['value']['token_description']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
    </td>
    
  </tr>
</table>

</form>

</div>

<div id="actions-area">
  <ul class="actions-list">
     <li><b>Users &amp; Permissions</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addUser'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user_add.png"> Add User</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addRole'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user_add.png"> Add Role</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/listUsers'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user.png"> List Users</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/listRoles'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user.png"> List Roles</a></li>
  </ul>
</div>