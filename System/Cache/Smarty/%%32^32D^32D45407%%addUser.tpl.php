<?php /* Smarty version 2.6.18, created on 2007-11-26 03:45:31
         compiled from /var/www/html/System/Applications/Settings/Presentation/addUser.tpl */ ?>
<script language="javascript">

<?php echo '

function check(){
  
  var editForm = document.getElementById(\'addUser\');
  
  if(editForm.username.value==\'\'){
    alert (\'Please enter the username!\');
    editForm.username.focus();
    return false;
  }if(editForm.user_role.value==\'\'){
    alert (\'Please enter the role!\');
    editForm.user_role.focus();
    return false;
  }if(editForm.user_email.value==\'\'){
    alert (\'Please enter the email id! \');
    editForm.user_email.focus();
    return false;
  }if(editForm.password.value==\'\'){
    alert (\'Please enter the password!\');
    editForm.password.focus();
    return false;
  }else if (editForm.passwordconfirm.value==\'\'){
    alert (\'Please re enter the password!\');
    editForm.passwordconfirm.focus();
    return false;
  }else if (editForm.password.value!=editForm.passwordconfirm.value){
    alert(\'You did not enter the same password twice. Please re-enter your password\');
    editForm.passwordconfirm.focus();
    return false;
  }
  
  return true;

}

'; ?>


</script>

<div id="work-area">

<h3 id="user">Add new User</h3>

<form id="addUser" name="addUser" action="<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addUserAction" method="post" style="margin:0px">

<table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="2" >
  
  <tr>
    <td class="text" valign="top">Role </td>
    <td align="left">
      <select name="user_role" style="width:200px">
        <?php $_from = $this->_tpl_vars['roles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['role']):
?>
        <option value="<?php echo $this->_tpl_vars['role']['id']; ?>
"><?php echo $this->_tpl_vars['role']['label']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
    </td>
  </tr>
  
  <tr>
    <td class="text" valign="top">Initial Site (you may add others later) </td>
    <td align="left">
      <select name="site_id" style="width:200px">
        <?php $_from = $this->_tpl_vars['sites']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['site']):
?>
        <option value="<?php echo $this->_tpl_vars['site']['id']; ?>
"><?php echo $this->_tpl_vars['site']['name']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
    </td>
  </tr>
  
  <tr>
    <td class="text" valign="top">Username </td>
    <td align="left"><input type="text" style="width:200px" name="username" />
      </td>
  </tr>
  
  <tr>
    <td class="text" valign="top">Password </td>
    <td align="left"><input type="password" style="width:200px" name="password" />
      </td>
  </tr>
	
	<tr>
	<td class="text" style="width:100px" valign="top">First name </td>
    <td align="left">
    	<input type="text" style="width:200px" name="user_firstname" />
    	</td>
  </tr>
  
  <tr>
	  <td class="text" style="width:100px" valign="top">Last name </td>
    <td align="left">
    	<input type="text"  style="width:200px" name="user_lastname" />
    	</td>
  </tr>

  <tr>
	<td class="text" style="width:100px" valign="top">E-mail </td>
    <td align="left">
     <input type="text"  style="width:200px" name="user_email" />
    	</td>
  </tr>
  
  <tr>
	<td class="text" style="width:100px" valign="top">Website </td>
    <td align="left">
    	 <input type="text" style="width:200px" name="user_website" />
    	</td>
  </tr>
  
  <tr>
	  <td class="text" style="width:100px" valign="top">About the user</td>
    <td align="left">
    	<textarea name="user_bio" style="width:500px;height:60px">Share a little biographical information to fill out your profile. This may be shown publicly.</textarea></td>
  </tr>
  
</table>

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="submit" value="Save" />
  </div>
</div>    

</form>

</div>

<div id="actions-area">
  <ul class="actions-list">
     <li><b>Users &amp; Permissions</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/addRole'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user_add.png"> Add Role</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='<?php echo $this->_tpl_vars['domain']; ?>
<?php echo $this->_tpl_vars['section']; ?>
/listUsers'" class="right-nav-link"><img border="0" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Icons/user.png"> Go back to users</a></li>
  </ul>
</div>