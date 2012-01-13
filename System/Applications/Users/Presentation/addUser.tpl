<style type="text/css">
{literal}
  #select_two_container {  
      position:relative;  
  } 

  .select_multiple_submit {  
      background-image:url("/stylesheets/popup_footer.gif");  
      background-image:top center;  
      background-repeat:repeat-x;  
      padding:10px;  
      height:22px;  
      text-align:rightright;  
  } 

  .select_multiple_label {  
      margin-left:5px;  
      font-family:"Lucida Grande",Verdana;  
      font-size:11px;  
  } 

  .select_multiple_container {  
      width:300px;  
      position:absolute;  
      top:0;  
      left:0;  
      z-index:500;  
      border:1px solid #222;  
      border-top:none;  
  } 

  .select_multiple_container .select_multiple_header {  
      background-image:url("/stylesheets/black_background.gif");  
      background-repeat:repeat-x;  
      background-position:top center;  
      color:#eee;  
      font-family:"Lucida Grande",Verdana;  
      font-weight:bold;  
      font-size:12px;  
      margin:0;  
      padding:7px 0 8px 10px;  
      background-color:#000;  
  } 

  table.select_multiple_table td {  
      height:27px;  
      border-bottom:1px solid #ddd;  
      font-family:"Lucida Grande",Verdana;  
      color:#333;  
      font-size:11px;  
  } 

  table.select_multiple_table tr.even {  
      background-color:#FCFCFC;  
  } 

  table.select_multiple_table tr.odd {  
      background-color:#F7F7F7;  
  } 

  table.select_multiple_table tr.selected {  
      background-image:none;  
      background-color:#D9E9FE;  
  } 

  .select_multiple_name {  
      padding-left:15px;  
      font-weight:bold;  
  } 

  .select_multiple_checkbox {  
      text-align:rightright;  
  } 

  .select_multiple_checkbox input {  
      margin-right:15px;  
  }
{/literal}
</style>

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

{if $sites._count > 1}
  <div class="edit-form-row">
    <div class="form-section-label">Site</div>
{foreach from=$sites item="site"}
    
    <input type="checkbox" name="user_sites[]" value="{$site.id}" id="site-checkbox-{$site.id}" style="display:none" class="site-checkbox" />
    <label for="site-checkbox-{$site.id}" class="checkbox-array" id="site-checkbox-{$site.id}-label">{$site.internal_label}</label>
    <script type="text/javascript">
    
    $('site-checkbox-{$site.id}').observe('click', function(e){literal}{
      
      if($(Event.element(e).id).checked){
        $(Event.element(e).id+'-label').addClassName('selected');
      }else{
        $(Event.element(e).id+'-label').removeClassName('selected');
      }
      
    }{/literal});
    
    </script>
{/foreach}
    
    <input type="checkbox" name="global_site_access" id="site-checkbox-global" value="1" style="display:none" />
    <label for="site-checkbox-global" class="checkbox-array" id="site-checkbox-global-label">Global</label>
    
    <script type="text/javascript">
    {literal}
    
    $('site-checkbox-global').observe('click', function(e){
      
      if($(Event.element(e).id).checked){
        $(Event.element(e).id+'-label').addClassName('selected');
        $$('input.site-checkbox').find(function(e){
          e.checked = true;
          $(e.id+'-label').addClassName('selected');
          $(e.id+'-label').fade({duration:0.5});
        });
      }else{
        $(Event.element(e).id+'-label').removeClassName('selected');
        $$('input.site-checkbox').find(function(e){
          e.checked = false;
          $(e.id+'-label').removeClassName('selected');
          $(e.id+'-label').appear({duration:0.5});
        }); 
      }
    
    });
    
    {/literal}
    </script>
    
  </div>
{/if}
  
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

<script type="text/javascript">
{* {literal}
//complex example, note how we need to pass in different CSS selectors because of the complex HTML structure  
var select_multiple_two = new Control.SelectMultiple('select_multiple_two','select_multiple_two_options',{  
    checkboxSelector: 'table.select_multiple_table tr td input[type=checkbox]',  
    nameSelector: 'table.select_multiple_table tr td.select_multiple_name',  
    afterChange: function(){  
        if(select_multiple_two && select_multiple_two.setSelectedRows)  
            select_multiple_two.setSelectedRows();  
    }  
});  
  
//adds and removes highlighting from table rows  
select_multiple_two.setSelectedRows = function(){  
    this.checkboxes.each(function(checkbox){  
        var tr = $(checkbox.parentNode.parentNode);  
        tr.removeClassName('selected');  
        if(checkbox.checked)  
            tr.addClassName('selected');  
    });  
}.bind(select_multiple_two);  
select_multiple_two.checkboxes.each(function(checkbox){  
    $(checkbox).observe('click',select_multiple_two.setSelectedRows);  
});  
select_multiple_two.setSelectedRows();  
  
//link open and closing  
$('select_multiple_two_open').observe('click',function(event){  
    $(this.select).style.visibility = 'hidden';  
    new Effect.BlindDown(this.container,{  
        duration: 0.3  
    });  
    Event.stop(event);  
    return false;  
}.bindAsEventListener(select_multiple_two));  
$('select_multiple_two_close').observe('click',function(event){  
    $(this.select).style.visibility = 'visible';  
    new Effect.BlindUp(this.container,{  
        duration: 0.3  
    });  
    Event.stop(event);  
    return false;  
}.bindAsEventListener(select_multiple_two));
{/literal} *}
</script>