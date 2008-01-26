<?php /* Smarty version 2.6.18, created on 2008-01-25 10:17:51
         compiled from /var/www/html/System/Presentation/Backend/header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'image', '/var/www/html/System/Presentation/Backend/header.tpl', 84, false),)), $this); ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="UTF-8" <?php echo '?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  
    <title>Smartest&trade; Web Platform<?php if ($this->_tpl_vars['sectionName']): ?> | <?php echo $this->_tpl_vars['sectionName']; ?>
<?php endif; ?></title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Stylesheets/sm_style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Stylesheets/sm_layout.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Stylesheets/sm_itemsview.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Stylesheets/sm_treeview.css" />
    
    <script type="text/javascript" language="javascript">

       var sm_domain = '<?php echo $this->_tpl_vars['domain']; ?>
';
       var sm_section = '<?php echo $this->_tpl_vars['section']; ?>
';
       var sm_method = '<?php echo $this->_tpl_vars['method']; ?>
';
       var sm_user_agent = <?php echo $this->_tpl_vars['sm_user_agent']; ?>
;
       
    </script>

    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Javascript/scriptaculous/src/effects.js"></script>
                                                                                                  
    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Javascript/smartest/interface.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Javascript/smartest/interface-obj.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Javascript/smartest/treeview.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Javascript/smartest/server.js"></script>
    
        
    <script type="text/javascript" language="javascript">
      // var dd1 = new YAHOO.util.DD("actions-area");
    </script>

    
    
    <style type="text/css">
    
      img{ behavior:url(<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Javascript/smartest/iepngfix.htc); }
      
      ul.basic-list{
        list-style-image:url('<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Images/generic_bullet.gif');
      }
      
      div#work-area h3{
        background-image:url('<?php echo $this->_tpl_vars['domain']; ?>
Resources/System/Images/interface_title_bg.gif');
      }
    
    	div.form-section-label{
    	    		background-repeat:repeat-x;
    	}
    	
    	div.buttons-bar{
    		background-image:url('<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/admin_button_bar_bg.gif');
    		background-repeat:repeat-x;
    	}
    	
    	div#admin-menu ul li.off{
				}
		
		div#admin-menu ul li.on{
			background-image:url('<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/admin_left_nav_bg_on.gif');
			background-repeat:repeat-x;
		}
    	
    </style>
		
  </head>
  <body>
  
    <div id="top-strip" style="background-image:url(<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/admin_top_bar_bg.gif)">
      <?php echo smarty_function_image(array('file' => "admin_top_bar_logo.gif"), $this);?>

    </div>
    
        
    <div id="user-info">
      Signed in as: <strong><?php echo $this->_tpl_vars['_user']['firstname']; ?>
 <?php echo $this->_tpl_vars['_user']['lastname']; ?>
</strong> | <a href="<?php echo $this->_tpl_vars['domain']; ?>
smartest/logout">Sign Out</a>&nbsp;&nbsp;
    </div>