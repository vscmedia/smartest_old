<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  
    <title>Smartest&trade; Web Platform{if $sectionName} | {$sectionName}{/if}</title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_style.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_layout.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_itemsview.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_treeview.css" />
    
    <script type="text/javascript" language="javascript">

       var sm_domain = '{$domain}';
       var sm_section = '{$section}';
       var sm_method = '{$method}';
       var sm_user_agent = {$sm_user_agent};
       
    </script>

    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
                                                                                                  
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/interface.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/interface-obj.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/treeview.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/server.js"></script>
    
    {* <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/yui/build/yahoo/yahoo.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/yui/build/dom/dom.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/yui/build/dragdrop/dragdrop.js"></script> *}
    
    <script type="text/javascript" language="javascript">
      // var dd1 = new YAHOO.util.DD("actions-area");
    </script>

    
    
    <style type="text/css">
    
      img{ldelim} behavior:url({$domain}Resources/System/Javascript/smartest/iepngfix.htc); {rdelim}
      
      ul.basic-list{ldelim}
        list-style-image:url('{$domain}Resources/System/Images/generic_bullet.gif');
      {rdelim}
      
      div#work-area h3{ldelim}
        background-image:url('{$domain}Resources/System/Images/interface_title_bg.gif');
      {rdelim}
    
    	div.form-section-label{ldelim}
    	{*	background-image:url('{$domain}Resources/System/Images/form_section_label_bg.gif'); *}
    		background-repeat:repeat-x;
    	{rdelim}
    	
    	div.buttons-bar{ldelim}
    		background-image:url('{$domain}Resources/System/Images/admin_button_bar_bg.gif');
    		background-repeat:repeat-x;
    	{rdelim}
    	
    	div#admin-menu ul li.off{ldelim}
		{*	background-image:url('{$domain}Resources/System/Images/admin_left_nav_bg_off.gif');
			background-repeat:repeat-x; *}
		{rdelim}
		
		div#admin-menu ul li.on{ldelim}
			background-image:url('{$domain}Resources/System/Images/admin_left_nav_bg_on.gif');
			background-repeat:repeat-x;
		{rdelim}
    	
    </style>
		
  </head>
  <body>
  
    <div id="top-strip" style="background-image:url({$domain}Resources/System/Images/admin_top_bar_bg.gif)">
      {image file="admin_top_bar_logo.gif"}
    </div>
    
    {*adminbutton type="url" object="smartest/logout" text="Log Out"}, <a href="{$domain}smartest/users">My Account</a>*}
    
    <div id="user-info">
      Signed in as: <strong>{$_user.firstname} {$_user.lastname}</strong> | <a href="{$domain}smartest/logout">Sign Out</a>&nbsp;&nbsp;
    </div>