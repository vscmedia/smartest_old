<script type="text/javascript">

{literal}

  var firstName, firstNameEntered, lastName, lastNameEntered, usernameSuggested;
  
  var defaultFirstName = 'Enter first name';
  var defaultUserName = 'Enter a username';
  var defaultEmailAddress = 'Enter an email address';
  
  var emailAddressRegex = /^[\w\._-]+@[\w-]+(\.[\w]+)+$/;

{/literal}

</script>

<div id="work-area">

{load_interface file="edit_user_tabs.tpl"}

<h3 id="user">Edit user: {if $user.id == $_user.id}You{else}{$user.fullname}{/if}</h3>

<form id="addUser" name="addUser" action="{$domain}{$section}/updateUser" method="post">
  
  <input type="hidden"  name="user_id" value="{$user.id}" >
  
  <div class="edit-form-row">
    <div class="form-section-label">First name </div>
    <input type="text" name="user_firstname" id="ifn" value="{$user.firstname}" />
    <div class="form-hint">A first name is required</div>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Last name </div>
    <input type="text" name="user_lastname" id="iln" value="{$user.lastname}" />
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">Username </div>
    {if $allow_username_change}
    <input type="text" style="width:200px" name="username" id="username" autocomplete="off" value="{$user.username}" /><div class="form-hint">Letters, numbers, dots and underscores only please</div>
    {else}{$user.username}{/if}
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Password </div>
    <input type="password" style="width:200px" name="password" id="password" autocomplete="off" /><div class="form-hint">Try to make this at least eight characters, and include letters, mixed uppercase and lowercase, and punctuation</div>  
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Re-type password </div>
    <input type="password" style="width:200px" name="passwordconfirm" id="passwordconfirm" autocomplete="off" /><div class="form-hint">Type the password again if you are changing it, just to be sure you typed it right.</div>  
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Email address </div>
    <input type="text" name="user_email" id="user_email" value="{$user.email}" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">User's website address, if not this website </div>
    <input type="text" name="user_website" value="{$user.website}" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">About the user </div>
    <textarea name="user_bio" style="width:500px;height:60px">Share a little biographical information to fill out your profile. This may be shown publicly.</textarea>
  </div>
  
{if $require_password_changes && $user.id != $_user.id}
  <div class="edit-form-row">
    <div class="form-section-label">Require password change </div>
    <input type="checkbox" name="require_password_change" value="1"{if $user.password_change_required} checked="checked"{/if} /><span class="form-hint">Roadblocks the user until they change their password. Takes effect next time they log in.</span>
  </div>
{/if}

  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save" />
    </div>
  </div>  

  </form>

</div>

<div id="actions-area">
  <ul class="actions-list">
     <li><b>Users &amp; Tokens</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addRole'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/vcard_add.png"> Add Role</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> Go back to users</a></li>
  </ul>
</div>

{if $sites._count > 1}
<script type="text/javascript">
{literal}
  
  $('addUser').observe('submit', function(e){
    
    if($('ifn').value == '' || $('ifn').value == defaultFirstName){
        $('ifn').addClassName('error');
        e.stop();
    }
    
    if($('user_email').value == '' || $('user_email').value == defaultEmailAddress || !$('user_email').value.match(emailAddressRegex)){
        $('user_email').addClassName('error');
        e.stop();
    }
    
    if($('password').value != $('passwordconfirm').value){
        $('password').addClassName('error');
        $('passwordconfirm').addClassName('error');
        e.stop();
    }
    
  });
  
{/literal}

</script>
{/if}