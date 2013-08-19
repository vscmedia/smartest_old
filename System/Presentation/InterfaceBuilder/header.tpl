<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  
    <title>Smartest&trade; Web Platform{if $_interface_title} | {$_interface_title}{/if}</title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" href="{$domain}Resources/System/Images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="{$domain}Resources/System/Images/favicon.ico" type="image/x-icon">
    
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
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_modals.css" />
    
    
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
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/slider.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/syntacticx-livepipe-ui/src/livepipe.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/syntacticx-livepipe-ui/src/scrollbar.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/jscolor/jscolor.js"></script>
                                                                                                  
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/interface.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/treeview.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/help.js"></script>
    <script type="text/javascript" language="javascript" src="{$domain}Resources/System/Javascript/smartest/prefs.js"></script>
    
    <script type="text/javascript">
      {literal}
      var HELP = new Smartest.HelpViewer();
      var PREFS = new Smartest.PreferencesBridge();
      var MODALS = new Smartest.AjaxModalViewer();
      Smartest.createNew = function(){MODALS.load('desktop/createDialog', 'Create something new');}
      
      {/literal}
    </script>
    
    <style type="text/css">
      img{ldelim} behavior:url({$domain}Resources/System/Javascript/iepngfix/iepngfix.htc); {rdelim}
    </style>
		
  </head>
  <body>
    
    <div id="fb-root"></div>
    <script>(function(d, s, id) {ldelim}
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=416379105107063";
      fjs.parentNode.insertBefore(js, fjs);
    {rdelim}(document, 'script', 'facebook-jssdk'));</script>
    
    <div id="top-strip" style="background-image:url({$domain}Resources/System/Images/admin_top_bar_bg.gif)">
      <img src="{$domain}Resources/System/Images/admin_top_bar_logo.gif" alt="Smartest" border="0" />
    </div>
    
    <div id="user-info">
      {if $show_left_nav_options}Signed into {if $_user_allow_site_edit}<a href="{$domain}smartest/settings">{$_site.internal_label|summary:"20"}</a>{else}<strong>{$_site.internal_label|summary:"20"}</strong>{/if}{else}Signed in{/if} as <strong>{$_user.firstname} {$_user.lastname}</strong>{* if $show_left_nav_options} | <a href="{$domain}smartest/todo" id="sm-signout-link">To-do list</a>{/if *}&nbsp;&nbsp;{if $show_left_nav_options || $show_create_button}<a href="#create" id="sm-button-create" title="Create something new" class="sm-top-bar-button">&nbsp;</a>{/if}<a href="{$domain}smartest/profile" id="sm-button-profile" title="Edit your user account" class="sm-top-bar-button">&nbsp;</a>{if $show_left_nav_options && ($_user.num_allowed_sites > 1 || $_user_allow_site_create)}<a href="{$domain}smartest/close" id="sm-button-close" title="Close this site" class="sm-top-bar-button">&nbsp;</a>{/if}<a href="{$domain}smartest/logout" id="sm-button-exit" title="Sign out" class="sm-top-bar-button">&nbsp;</a>&nbsp;&nbsp;
    </div>
    
    {if $show_left_nav_options || $show_create_button}
    <script type="text/javascript">
    {literal}
    $('sm-button-create').observe('click', function(e){
        Smartest.createNew();
        e.stop();
    });
    {/literal}
    </script>
    {/if}
    
    <div id="help" style="display:none" class="modal-outer">
      <div id="help-viewer" class="modal">
        <div class="modal-scrollbar-track" id="help-scrollbar-track"><div class="modal-scrollbar-handle" id="help-scrollbar-handle"></div></div>
        <div id="help-updater" class="modal-updater">
          
        </div>
        <div id="help-title-bar" class="modal-title-bar">
          <a class="modal-closer" id="help-closer" href="#close-help"></a>
          <script type="text/javascript">
          {literal}$('help-closer').observe('click', function(e){
            HELP.hideViewer();
            e.stop();
          });{/literal}
          </script>
          <h2 id="help-title">Smartest Help Viewer</h2>
        </div>
      </div>
    </div>
    
    <div id="modal-outer" style="display:none" class="modal-outer">
      <div id="modal-inner" class="modal">
        <div class="modal-scrollbar-track" id="modal-scrollbar-track"><div class="modal-scrollbar-handle" id="modal-scrollbar-handle"></div></div>
        <div id="modal-updater" class="modal-updater"></div>
        <div class="modal-title-bar">
          <a class="modal-closer" id="modal-closer" href="#close-modal"></a>
          <script type="text/javascript">
          {literal}$('modal-closer').observe('click', function(e){
            MODALS.hideViewer();
            e.stop();
          });{/literal}
          </script>
          <h2 id="modal-title"></h2>
        </div>
      </div>
    </div>
    
    <script type="text/javascript">
      {literal}
      /* $('modal-outer').observe('click', function(e){
        MODALS.hideViewer();
        e.stop();
      });
      $('modal-inner').observe('click', function(e){
        // e.stop();
      });
      $('help').observe('click', function(e){
        HELP.hideViewer();
        e.stop();
      });
      $('help-viewer').observe('click', function(e){
        e.stop();
      }); */
      {/literal}
    </script>