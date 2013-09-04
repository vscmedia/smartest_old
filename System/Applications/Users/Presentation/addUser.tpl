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
  
  var defaultFirstName = 'Enter first name';
  var defaultUserName = 'Enter a username';
  var defaultEmailAddress = 'Enter an email address';
  
  var emailAddressRegex = /^[\w\._-]+@[\w-]+(\.[\w]+)+$/;
  
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
    
    <div style="display:inline-block;width:auto">
{foreach from=$sites item="site"}
    
    <input type="checkbox" name="user_sites[]" value="{$site.id}" id="site-checkbox-{$site.id}" style="display:none" class="site-checkbox"{if $site.id == $_site.id} checked="checked"{/if} />
    <label for="site-checkbox-{$site.id}" class="checkbox-array{if $site.id == $_site.id} selected{/if}" id="site-checkbox-{$site.id}-label">{$site.internal_label}</label>
    <script type="text/javascript">
    
    $('site-checkbox-{$site.id}').observe('click', function(e){literal}{
      
      if($(Event.element(e).id).checked){
        $(Event.element(e).id+'-label').addClassName('selected');
      }else{
        $(Event.element(e).id+'-label').removeClassName('selected');
      }
      
      // alert($$('label.checkbox-array.selected').length);
      
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
  </div>
{else}
  <input type="checkbox" name="user_sites[]" value="{$_site.id}" id="site-checkbox-{$_site.id}" style="display:none" class="site-checkbox" checked="checked" />
{/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">First name </div>
    <input type="text" name="user_firstname" id="ifn" value="Enter first name" class="unfilled" />
    <div class="form-hint">A first name is required</div>
    <script type="text/javascript">
    {literal}
      
      $('ifn').observe('focus', function(event){
        if($('ifn').value == defaultFirstName){
          $('ifn').value = '';
          $('ifn').removeClassName('unfilled');
        }
      });
      
      $('ifn').observe('blur', function(event){
        if($('ifn').value && !firstNameEntered && $('ifn').value != defaultFirstName){
            
            firstNameEntered = true;
            firstName = $('ifn').value;
            $('ifn').removeClassName('error');
            
        }else if($('ifn').value == ''){
          $('ifn').addClassName('unfilled');
          $('ifn').value = defaultFirstName;
        }
        
      });
      
    {/literal}
    </script>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Last name </div>
    <input type="text" name="user_lastname" id="iln" />
    <script type="text/javascript">
    {literal}
      $('iln').observe('blur', function(event){
        
        if(firstNameEntered && !lastNameEntered){
            lastNameEntered = true;
            lastName = $('iln').value;
            if(!usernameSuggested && (!$('username').value || $('username').value == defaultUserName)){
                $('username').value = firstName.toUserName()+'.'+lastName.toUserName();
                $('username').removeClassName('unfilled');
                usernameSuggested = true;
            }
        }
        
      });
    {/literal}
    </script>
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">Username </div>
    <input type="text" style="width:200px" name="username" id="username" autocomplete="off" value="Enter a username" class="unfilled" /><div class="form-hint">Pick something cool. Letters, numbers, dots and underscores only please</div>
    <script type="text/javascript">
    {literal}
    
      $('username').observe('blur', function(e){
        if($('username').value && $('username').value != defaultUserName){
          $('username').removeClassName('error');
        }else if($('username').value == defaultUserName){
          $('username').addClassName('unfilled');
        }else if(!$('username').value){
          $('username').value = defaultUserName;
          $('username').addClassName('unfilled');
        }
      });
      
      $('username').observe('focus', function(e){
        if($('username').value == defaultUserName){
          $('username').value = '';
          $('username').removeClassName('unfilled');
        }
      });
      
    {/literal}
    </script>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Password </div>
    <input type="password" style="width:200px" name="password" id="password" autocomplete="off" /><div class="form-hint">Try to make this at least eight characters, and include letters, mixed uppercase and lowercase, and punctuation</div>
  
    <script type="text/javascript">
    {literal}
    
      $('password').observe('blur', function(e){
        if($('password').value){
          $('password').removeClassName('error');
        }
      });
      
      $('password').observe('focus', function(e){
        // if($('username').value == defaultUserName){
          $('password').removeClassName('error');
          $('password').removeClassName('unfilled');
        // }
      });
      
    {/literal}
    </script>
  
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
    <input type="text" name="user_email" id="user_email" value="Enter an email address" class="unfilled" />
    <script type="text/javascript">
    {literal}
    
      $('user_email').observe('blur', function(e){
        if($('user_email').value && $('user_email').value != defaultEmailAddress && $('user_email').value.match(emailAddressRegex)){
          $('user_email').removeClassName('error');
        }else if($('user_email').value == defaultEmailAddress){
          $('user_email').addClassName('unfilled');
        }else if(!$('user_email').value){
          $('user_email').value = defaultEmailAddress;
          $('user_email').addClassName('unfilled');
        }
        
      });
      
      $('user_email').observe('focus', function(e){
        if($('user_email').value == defaultEmailAddress){
          $('user_email').value = '';
          $('user_email').removeClassName('unfilled');
        }
      });
      
    {/literal}
    </script>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">User's website address, if not this website </div>
    http://<input type="text" style="width:360px" name="user_website" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">About the user </div>
    <textarea name="user_bio" style="width:500px;height:60px">Share a little biographical information to fill out your profile. This may be shown publicly.</textarea>
  </div>

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
    
    if($('username').value == '' || $('username').value == defaultUserName){
        $('username').addClassName('error');
        e.stop();
    }
    
    if($('user_email').value == '' || $('user_email').value == defaultEmailAddress || !$('user_email').value.match(emailAddressRegex)){
        $('user_email').addClassName('error');
        e.stop();
    }
    
    if($('password').value == ''){
        $('password').addClassName('error');
        e.stop();
    }
    
    if($$('label.checkbox-array.selected').length == 0){
        e.stop();
    }
    
  });
  
{/literal}
</script>
{/if}