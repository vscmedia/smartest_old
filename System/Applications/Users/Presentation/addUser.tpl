<script type="text/javascript">

{literal}

  /* function check(){
  
  var editForm = document.getElementById('addUser');
  
  if(editForm.username.value==''){
    alert ('Please enter the username!');
    editForm.username.focus();
    return false;
  }if(editForm.user_role.value==''){
    alert ('Please enter the role!');
    editForm.user_role.focus();
    return false;
  }if(editForm.user_email.value==''){
    alert ('Please enter the email id! ');
    editForm.user_email.focus();
    return false;
  }if(editForm.password.value==''){
    alert ('Please enter the password!');
    editForm.password.focus();
    return false;
  }else if (editForm.passwordconfirm.value==''){
    alert ('Please re enter the password!');
    editForm.passwordconfirm.focus();
    return false;
  }else if (editForm.password.value!=editForm.passwordconfirm.value){
    alert('You did not enter the same password twice. Please re-enter your password');
    editForm.passwordconfirm.focus();
    return false;
  }
  
  return true; */
  
  var firstName, firstNameEntered, lastName, lastNameEntered, usernameSuggested;
  
  function finishFirstName(){
      if(!firstNameEntered){
          firstNameEntered = true;
          firstName = $('ifn').value;
      }
  }
  
  function finishLastName(){
      if(firstNameEntered && !lastNameEntered){
          lastNameEntered = true;
          lastName = $('iln').value;
          if(!usernameSuggested){
              $('username').value = firstName.toLowerCase()+'.'+lastName.toLowerCase();
              usernameSuggested = true;
          }
      }
  }

{/literal}

</script>

<div id="work-area">

<h3 id="user">Add new User</h3>

<form id="addUser" name="addUser" action="{$domain}{$section}/insertUser" method="post" style="margin:0px">
  
  <div class="edit-form-row">
    <div class="form-section-label">First name </div>
    <input type="text" name="user_firstname" id="ifn" />
    {literal}<script type="text/javascript">
      $('ifn').observe('blur', function(event){
        if(!firstNameEntered){
            firstNameEntered = true;
            firstName = $('ifn').value;
        }
      });
    </script>{/literal}
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Last name </div>
    <input type="text" name="user_lastname" id="iln" />
    {literal}<script type="text/javascript">
      $('iln').observe('blur', function(event){
        if(firstNameEntered && !lastNameEntered){
            lastNameEntered = true;
            lastName = $('iln').value;
            if(!usernameSuggested){
                $('username').value = firstName.toUserName()+'.'+lastName.toUserName();
                usernameSuggested = true;
            }
        }
      });
    </script>{/literal}
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">Username </div>
    <input type="text" style="width:200px" name="username" id="username" /><span class="form-hint">letters, numbers and underscores only please</span>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Password </div>
    <input type="password" style="width:200px" name="password" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Role </div>
    <select name="user_role">
      {foreach from=$roles item="role"}
      <option value="{if $role.type == 'nondb'}system:{/if}{$role.id}">{$role.label}</option>
      {/foreach}
    </select>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Email address </div>
    <input type="text" name="user_email" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Website address </div>
    http://<input type="text" style="width:278px" name="user_website" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">About the user </div>
    <textarea name="user_bio" style="width:500px;height:60px">Share a little biographical information to fill out your profile. This may be shown publicly.</textarea>
  </div>

{* <table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="2" >
  
  <tr>
    <td class="text" valign="top">Role </td>
    <td align="left">
      <select name="user_role" style="width:200px">
        {foreach from=$roles item="role"}
        <option value="{$role.id}">{$role.label}</option>
        {/foreach}
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
    	 http://<input type="text" style="width:200px" name="user_website" />
    	</td>
  </tr>
  
  <tr>
	  <td class="text" style="width:100px" valign="top">About the user</td>
    <td align="left">
    	<textarea name="user_bio" style="width:500px;height:60px">Share a little biographical information to fill out your profile. This may be shown publicly.</textarea></td>
  </tr>
  
</table> *}

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="submit" value="Create new user" />
  </div>
</div>    

</form>

</div>

<div id="actions-area">
  <ul class="actions-list">
     <li><b>Users &amp; Tokens</b></li>
     {* <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addRole'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add Role</a></li> *}
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> Go back to users</a></li>
  </ul>
</div>