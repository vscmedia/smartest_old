<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Smartest Web Platform&trade; | Log In</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" href="{$domain}Resources/System/Stylesheets/sm_login.css" />
    
    <script language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
    <script language="javascript">
        {literal}
        function loginSubmit(){
            new Effect.BlindUp('loginform_container', { duration: 0.5 });
            var timeout1 = window.setTimeout("new Effect.BlindDown('logging_in', { duration: 0.5 })", 500);
            var timeout2 = window.setTimeout("document.getElementById('loginform').submit()", 2000);
            // document.loginform.submit();
            // alert(document.loginform);
        }
        {/literal}
    </script>
</head>

{literal}<body onload="new Effect.Appear('login', { duration: 2.0 });">{/literal}

<div id="login" style="display: none;">

<img src="{$domain}Resources/System/Images/smartest.jpg" alt="Smartest" border="0" />

<div id="logging_in" style="display:none">
	<p>Please wait...</p>
</div>

<div id="loginform_container">

{if $smarty.get.reason == "badauth"}<p id="login_error">There was a problem with your username and/or password.</p>{/if}

<form name="loginform" id="loginform" action="{$domain}loginscreen/doAuth" method="post">

<p><label>Username:<br /><input type="text" name="user" id="username" value="" size="20" tabindex="1" class="textInput" /></label></p>
<p><label>Password:<br /><input type="password" name="passwd" id="password" value="" size="20" tabindex="2" class="textInput" /></label></p>

<input type="hidden" name="from" value="{$from}" />
<input type="hidden" name="refer" value="{$refer}" />

<p class="submit">
    <a href="javascript:loginSubmit()">Log In</a>
</p>

</div>

</form>
</ul>
</div>

</body>
</html>
