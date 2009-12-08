<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  
    <title>Smartest&trade; Web Platform{if $sectionName} | {$sectionName}{/if}</title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_style.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_layout.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_admin_menu.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_actions_menu.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_itemsview.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_treeview.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_dropdown_menu.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_columns.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_tabs.css" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_buttons.css" />
    
    <script type="text/javascript" language="javascript">

       var sm_domain = '{$domain}';
       var sm_section = '{$section}';
       var sm_method = '{$method}';
       var sm_user_agent = {$sm_user_agent_json};
       
    </script>

    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
                                                                                                  
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/interface.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/treeview.js"></script>
    
    <style type="text/css">
      img{ldelim} behavior:url({$domain}Resources/System/Javascript/iepngfix/iepngfix.htc); {rdelim}
    </style>
		
  </head>
  <body>
  
    <div id="top-strip" style="background-image:url({$domain}Resources/System/Images/admin_top_bar_bg.gif)">
      <img src="{$domain}Resources/System/Images/admin_top_bar_logo.gif" alt="Smartest" border="0" />
    </div>
    
    <div id="user-info">
      Signed in as: <strong>{$_user.firstname} {$_user.lastname}</strong>{if $show_left_nav_options} | <a href="{$domain}smartest/todo" id="sm-signout-link">To-do list</a>{/if} | <a href="{$domain}smartest/logout" id="sm-signout-link">Sign Out</a>&nbsp;&nbsp;
    </div>