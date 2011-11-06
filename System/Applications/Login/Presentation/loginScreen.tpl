<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Smartest Web Platform&trade; | Log In</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" href="{$domain}Resources/System/Stylesheets/sm_login.css" />
    
    <style type="text/css">
      /* {literal}div#login{{/literal}
        background-image:url('{$domain}Resources/System/Images/login_form_bg_hgrad.gif');
        background-repeat:repeat-x;
      {literal}}{/literal} */
      
    </style>
    
    <script type="text/javascript" language="javascript">

       var sm_domain = '{$domain}';
       var sm_section = '{$section}';
       var sm_method = '{$method}';
       var sm_user_agent = {$sm_user_agent_json};
       
    </script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/nakajima/event_hash_changed.js"></script>
    <script type="text/javascript">
    {literal}
    
      document.observe('hash:changed', function(){
        
        var hash = document.location.hash.substring(1);
        var messageId = 'message-'+hash;
        
        if($(messageId)){
          
          $$('p.login-message.notify').each(function(p){
            p.hide();
          });
          
          $(messageId).appear();
          
        }
        
      });
      
      var loginSubmit = function(){
        
        new Effect.Opacity('username-holder',{
          duration: 0.1, transition: Effect.Transitions.linear, from: 1.0, to: 0.01 });
        
        new Effect.Opacity('password-holder',{
          duration: 0.1, transition: Effect.Transitions.linear, from: 1.0, to: 0.01 });
          
        $('footer').fade({duration: 0.150});
        
        var timeout0 = window.setTimeout(function(){
          new Effect.BlindUp('loginform_container', { duration: 0.6 });
        }, 170);
        
        var timeout1 = window.setTimeout(function(){
          new Effect.BlindDown('login-message-holder', { duration: 0.5 });
        }, 600);
        
        var timeout2 = window.setTimeout(function(){
          $('loginform').submit();
        }, 2000);
        
      }
      
      document.observe('dom:loaded', function(){
        
        $('loginform').observe('keypress', function(e){
          
          if(e.keyCode == 13){
            
            loginSubmit();
            
          }
          
        });
        
        $('submit-button').observe('click', function(e){
          
          loginSubmit();
          e.stop();
          
        });
        
        $('logo').observe('click', function(){
          
          window.open('http://sma.rte.st/?ref=login');
          
        });
        
      });
      
    {/literal}
    </script>

</head>

<body>

<div id="login">
  
  <!--<img src="{$domain}Resources/System/Images/login_box_top_corners.png" alt="" style="display:block" />-->

  <div id="login-inner">

    <img src="{$domain}Resources/System/Images/login_logo.png" alt="Smartest" border="0" id="logo" />
    
    <div id="login-message-holder" style="display:none;">
      <p class="login-message">Please wait...</p>
    </div>

    <div id="loginform_container">

      <p class="login-message notify" id="message-logout" style="display:none">You have been safely logged out of Smartest.</p>
      <p class="login-message notify" id="message-badauth" style="display:none">The username or password you provided were wrong.</p>
      <p class="login-message notify" id="message-session" style="display:none">Your session has timed out. Please log back into Smartest</p>

      <form name="loginform" id="loginform" action="{$domain}smartest/login/check" method="post">

        <p id="username-holder">
          <label>
            Username:<br />
            <input type="text" name="user" id="username" value="" size="20" tabindex="1" class="textInput" />
          </label>
        </p>
        
        <p id="password-holder">
          <label>
            Password:<br />
            <input type="password" name="passwd" id="password" value="" size="20" tabindex="2" class="textInput" />
          </label>
        </p>

        <input type="hidden" name="from" value="{$from}" />
        <input type="hidden" name="refer" value="{$refer}" />
        <input type="hidden" name="service" value="smartest" />

        <p class="submit">
          <a href="#" id="submit-button"><img src="/Resources/System/Images/login_button.png" alt="Log In" /></a>
        </p>

      </form>
  
    </div>

  </div>
  
  <!--<img src="{$domain}Resources/System/Images/login_box_bottom_corners.png" alt="" style="display:block" />-->

</div>

<p id="footer">© VSC Creative Ltd. {$now.Y}</p>

{if $sm_user_agent.platform == "Windows" && $sm_user_agent.appName == "Explorer" && $sm_user_agent.appVersionInteger < 7}
<script language="javascript" src="{$domain}Resources/System/Javascript/supersleight/supersleight.js"></script>
<script language="javascript">supersleight.init();</script>
{/if}

</body>
</html>
