<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  
    <title>Smartest&trade; Web Platform{if $_interface_title} | {$_interface_title}{/if}</title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_help.css" />
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
       var sm_cancel_uri = '{$sm_cancel_uri}';
       
    </script>

    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/controls.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/jscolor/jscolor.js"></script>
                                                                                                  
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/interface.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/treeview.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/help.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/prefs.js"></script>
    
    <script type="text/javascript">
      var HELP = new Smartest.HelpViewer();
      var PREFS = new Smartest.PreferencesBridge();
    </script>
    
    <style type="text/css">
      img{ldelim} behavior:url({$domain}Resources/System/Javascript/iepngfix/iepngfix.htc); {rdelim}
    </style>
		
  </head>
  <body>
  
    <div id="top-strip" style="background-image:url({$domain}Resources/System/Images/admin_top_bar_bg.gif)">
      <img src="{$domain}Resources/System/Images/admin_top_bar_logo.gif" alt="Smartest" border="0" />
    </div>
    
    <div id="user-info">
      {if $show_left_nav_options}Signed into {if $_user_allow_site_edit}<a href="{$domain}smartest/settings">{$sm_currentSite.internal_label|summary:"20"}</a>{else}<strong>{$sm_currentSite.internal_label|summary:"20"}</strong>{/if}{else}Signed in{/if} as <strong>{$_user.firstname} {$_user.lastname}</strong>{* if $show_left_nav_options} | <a href="{$domain}smartest/todo" id="sm-signout-link">To-do list</a>{/if *}&nbsp;&nbsp;<a href="{$domain}smartest/profile" id="sm-button-profile" class="sm-top-bar-button">&nbsp;</a>{if $show_left_nav_options && ($_user.num_allowed_sites > 1 || $_user_allow_site_create)}<a href="{$domain}smartest/close" id="sm-button-close" class="sm-top-bar-button">&nbsp;</a>{/if}<a href="{$domain}smartest/logout" id="sm-button-exit" class="sm-top-bar-button">&nbsp;</a>&nbsp;&nbsp;
    </div>
    
    <div id="help" style="display:none">
      <div id="help-viewer">
        <div id="help-title-bar">
          Smartest Help Viewer
          <a id="help-closer" href="#close-help"></a>
          <script type="text/javascript">
          {literal}$('help-closer').observe('click', function(e){
            HELP.hideViewer();
            Event.stop(e);
          });{/literal}
          </script>
        </div>
        <div id="help-updater">
          
        </div>
      </div>
    </div>