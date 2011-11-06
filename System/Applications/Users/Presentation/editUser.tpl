<script language="javascript">
var domain = '{$domain}';
var section = '{$section}';
{literal}
 function updatePageName(newName){
  	document.getElementById('pageName').innerHTML="Page Details: "+newName;
 }
 
 function hideNotify(){
 	// alert('one');
 	// var hnot = setTimeout("alert('two')",4000); 
	var hnot = setTimeout("document.getElementById('notify').style.display='none'",3500); 
 }
function check(){
var editForm = document.getElementById('updatuserdetails');
// if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(editForm.user_email.value)){
if(editForm.user_email.value==''){
alert ('please enter the email');
editForm.user_email.focus();
return false;
}
if (editForm.password.value!='' && editForm.passwordconfirm.value==''){
alert ('Please re enter the password!');
editForm.passwordconfirm.focus();
return false;
}
else if (editForm.password.value!=editForm.passwordconfirm.value){
alert('You did not enter the same password twice. Please re-enter your password');
editForm.passwordconfirm.focus();
return false;
}
else{
return true;
}
}
{/literal}</script>

<div id="work-area">

{load_interface file="edit_user_tabs.tpl"}

<h3 id="user">Edit user: {if $user.id == $_user.id}You{else}{$user.fullname}{/if}</h3>

<form id="updatuserdetails" name="updatuserdetails" action="{$domain}{$section}/updateUser" method="post">

<input type="hidden"  name="user_id" value="{$user.id}" >

<table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="2">

  <tr>
    <td class="text" valign="top">Username : </td>
    <td align="left">{$user.username}
      </td>
  </tr>

	<tr>
	<td class="text" style="width:100px" valign="top"> First name :</td>
    <td align="left">
    	<input type="text" style="width:200px" name="user_firstname" value="{$user.firstname}" />
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Last name : </td>
    <td align="left">
    	<input type="text"  style="width:200px" name="user_lastname" value="{$user.lastname}" />
    	</td>
  </tr>

<tr>
	<td class="text" style="width:100px" valign="top"> E-mail : </td>
    <td align="left">
     <input type="text"  style="width:200px" name="user_email" value="{$user.email}" />
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Website : </td>
    <td align="left">
    	 <input type="text" style="width:200px" name="user_website" value="{$user.website}" />
    	</td>
  </tr>

<tr>
	<td class="text" style="width:100px" valign="top"> About the user: </td>
    <td align="left">
    	<textarea name="user_bio" style="width:500px;height:60px">{$user.bio}</textarea>
    	</td>
 </tr>
{if $require_password_changes && $user.id != $_user.id}
<tr>
	<td class="text" style="width:100px" valign="top"> Require password change </td>
    <td align="left">
    	<input type="checkbox" name="require_password_change" value="1"{if $user.password_change_required} checked="checked"{/if} /> (takes effect next time they log in)
    	</td>
 </tr>
{/if}
</table>

<h3 style="margin-top:10px">{if $user.id == $_user.id}Your password{else}Update user's password{/if}</h3>

<div class="instruction">(If you would like to change the user's password type a new one twice below. Otherwise leave this blank.)</div>
  
<table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="2" >
<tr>
	<td class="text" style="width:100px" valign="top">  Password </td>
    <td align="left">
    	 <input type="password" style="width:200px" name="password" value="" />
    	</td>
  </tr>
<tr>
	<td class="text" style="width:100px" valign="top"> Retype Password </td>
    <td align="left">
    	 <input type="password" style="width:200px" name="passwordconfirm" value="" />
    	</td>
  </tr>

{* <tr>
    <td colspan="2" class="submit" align="right">
    	<input type="button" value="Cancel" onclick="cancelForm();" />
    	<input type="submit" onclick="return check()" name="action" value="Save" />
    	</td>
  </tr> *}
</table>

  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();" />
    <input type="submit" value="Save" />
  </div>

</div>
<div id="actions-area">
  <ul class="actions-list">
     <li><b>Users &amp; Tokens</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> Go back to users</a></li>
     {if $show_tokens_edit_tab}<li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/editUserTokens?user_id={$user.id}'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> Edit this user's permissions</a></li>{/if}
  </ul>
</div>
</form>