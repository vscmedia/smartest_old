<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Smartest Web Platform&trade; | Log In</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" href="{$domain}Resources/System/Stylesheets/sm_login.css" />
    
    <style type="text/css">
      {literal}div#login{{/literal}
        background-image:url('{$domain}Resources/System/Images/login_form_bg_hgrad.gif');
        background-repeat:repeat-x;
      {literal}}{/literal}
      
    </style>
    
    <script type="text/javascript" language="javascript">

       var sm_domain = '{$domain}';
       var sm_section = '{$section}';
       var sm_method = '{$method}';
       var sm_user_agent = {$sm_user_agent_json};
       
    </script>
    <script language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
    
    <script language="javascript">
        {literal}
        function loginSubmit(){
            // $('username-holder').style.display='none';
            // $('password-holder').style.display='none';
            new Effect.Opacity('username-holder',{ duration: 0.1, transition: Effect.Transitions.linear, from: 1.0, to: 0.1 });
            new Effect.Opacity('password-holder',{ duration: 0.1, transition: Effect.Transitions.linear, from: 1.0, to: 0.1 });
            var timeout0 = window.setTimeout("new Effect.BlindUp('loginform_container', { duration: 0.5 })", 150);
            var timeout1 = window.setTimeout("new Effect.BlindDown('logging_in', { duration: 0.5 })", 500);
            var timeout2 = window.setTimeout("document.getElementById('loginform').submit()", 2000);
            // document.loginform.submit();
            // alert(document.loginform);
        }
        {/literal}
    </script>
</head>

{if $sm_user_agent.platform == "Windows" && $sm_user_agent.appName == "Explorer" && $sm_user_agent.appVersionInteger < 7}<body>{else}{literal}<body onload="new Effect.Appear('login', { duration: 2.0 });">{/literal}{/if}

<div id="login"{if $sm_user_agent.platform == "Windows" && $sm_user_agent.appName == "Explorer" && $sm_user_agent.appVersionInteger < 7} style="display: block;"{else} style="display: none;"{/if}>
  
  <img src="{$domain}Resources/System/Images/login_box_top_corners.png" alt="" style="display:block" />

  <div id="login-inner">

    <img src="{$domain}Resources/System/Images/login_logo.png" alt="Smartest" border="0" id="logo" />

    <div id="logging_in" style="display:none">
    	<p>Please wait...</p>
    </div>

    <div id="loginform_container">

      {if $smarty.get.reason == "badauth"}<p id="login_error">There was a problem with your username and/or password.</p>{/if}

      <form name="loginform" id="loginform" action="{$domain}loginscreen/doAuth" method="post">

        <p id="username-holder"><label>Username:<br /><input type="text" name="user" id="username" value="" size="20" tabindex="1" class="textInput" /></label></p>
        <p id="password-holder"><label>Password:<br /><input type="password" name="passwd" id="password" value="" size="20" tabindex="2" class="textInput" /></label></p>

        <input type="hidden" name="from" value="{$from}" />
        <input type="hidden" name="refer" value="{$refer}" />

        <p class="submit">
            <a href="javascript:loginSubmit()"><img src="{$domain}Resources/System/Images/login_button.png" alt="Log In" /></a>
        </p>

      </form>
  
    </div>

  </div>
  
  <img src="{$domain}Resources/System/Images/login_box_bottom_corners.png" alt="" style="display:block" />

</div>
<script language="javascript" src="{$domain}Resources/System/Javascript/supersleight/supersleight.js"></script>
</body>
</html>
