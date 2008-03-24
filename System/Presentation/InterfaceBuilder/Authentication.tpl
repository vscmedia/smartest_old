<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Smartest Content Management System &rsaquo; Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style>
  {literal}
    
    body {
      border: none;
    }

    body	{
      background: #f9fcfe;
      color: #000;
      margin: 0;
      padding: 0;
    }
    
    body, td {
      font: 13px "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana;
    }
    
    textarea, input, select {
      background: #f4f4f4;
      border: 1px solid #b2b2b2;
      color: #000;
      font:  13px Verdana, Arial, Helvetica, sans-serif;
      margin: 1px;
      padding: 3px;
    }
     
    .submit input, .submit input:focus, .button {
      border: 3px double #999;
      border-left-color: #ccc;
      border-top-color: #ccc;
      color: #333;
      padding: 0.25em;
    }
    
    .submit input:active, .button:active {
      background: #f4f4f4;
      border: 3px double #ccc;
      border-left-color: #999;
      border-top-color: #999;
    }
    
    .submit, .editform th, #postcustomsubmit {
      text-align: right;
    }
    
    .optiontable {
      width: 100%;
    }
    
    .optiontable td, .optiontable th {
      padding: .5em;
    }
    
    .optiontable th {
      width: 33%;
      text-align: right;
    }
    

    
    
    #login {
      background: #fff;
      border: 1px solid #a2a2a2;
      margin: 5em auto;
      padding: 1.5em;
      width: 25em;
    }
    
    #login #login_error {
      background: #c00;
      border: 1px solid #a40000;
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      padding: .5em;
      text-align: center;
    }
    
    #login h1 {
      margin-top: 0;
    }
    
    #login h1 a {
      display: block;
      text-indent: -1000px;
      height: 20px;
      border-bottom: none;
    }
    
    #login input {
      padding: 3px;
    }
    
    
    #login #username, #password {
      font-size: 1.7em;
      width: 80%;
    }
    
    #login #submit {
      font-size: 1.7em;
    }
  </style>
  {/literal}
  </head>
<body>

<div id="login">

<h1><a href="http://wordpress.org/">Smartest</a></h1>

<form name="loginform" id="loginform" action="{$domain}authentication/login" method="post">
<p><label>Username:<br /><input type="text" name="username" id="username" value="" size="20" tabindex="1" /></label></p>
<p><label>Password:<br /> <input type="password" name="password" id="password" value="" size="20" tabindex="2" /></label></p>
<p>

</p>
<p class="submit">
	<input type="submit" name="submit" id="submit" value="Login &raquo;" tabindex="4" />
</p>
</form>
</ul>
</div>

</body>
</html>




<!--
<div width="100%" align="center">

<h2>Administrator</h2>
<table width="300px" border="1" cellspacing="0" cellpadding="0" id="a">
	<form action="{$domain}authentication/login" method=post>
        <tr style="background-color:#EEEEEE">
          <td>Username</td><td><input type="text" name="username"></td>
	</tr>
	<tr>
          <td>Password</td><td><input type="password" name="password"></td>
        </tr>
	<tr>
 	  <td> </td>
          <td><input type="submit" value="login"></td>
        </tr>
	</form>
</table>
</div>-->
