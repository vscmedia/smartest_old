<?php /* Smarty version 2.6.18, created on 2007-12-03 12:13:03
         compiled from /var/www/html/Presentation/Masters/saponin_category.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'container', '/var/www/html/Presentation/Masters/saponin_category.tpl', 58, false),array('function', 'field', '/var/www/html/Presentation/Masters/saponin_category.tpl', 65, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  
  <head>
    
    <title><?php echo $this->_tpl_vars['this']['page']['formatted_title']; ?>
</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/saponin_layout.css" />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/saponin_global_style.css" />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/saponin_category_colors.css" />
    <!--[if IE 6]>
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/saponin_ie6_tweaks.css" />
    <![endif]-->

    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Javascript/saponin_menus.js"></script>
    
  </head>
  
  <body>
  
    <div id="page-width-container">
      <div id="fixed-width-container-outer">
        
        <div id="fixed-width-container-inner">
          
          <div id="top-bar">
            <form action="/search" method="get">

              <div class="form-element">
                <input type="text" name="q" />
              </div>
              <div class="link-container">
                <a href="">Search:</a>
              </div>
              <div class="link-container">
                <a href="">Contact Us</a>&nbsp;&nbsp;&nbsp;

              </div>
            </form>
          </div>
        
          <div id="logo-banner" onmouseover="hideAllMenus()">
            <table cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td><a href="/"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/saponin_logo_category.gif" alt="Saponin, Inc" border="0" id="logo" height="116" width="279" /></a></td>
                <td>
                  <img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/multicolor_banner.jpg" alt="" style="display:block">
                </td>
              </tr>
            </table>
          </div>
        
          <?php echo smarty_function_container(array('name' => 'menus'), $this);?>

        
          <img id="pink-strip" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/pink-strip.gif" alt="" />
        
          <?php echo smarty_function_container(array('name' => 'menu_bar'), $this);?>

        
          <div id="page-content">
            <!--<?php echo smarty_function_field(array('name' => 'category'), $this);?>
-->
            <?php echo smarty_function_container(array('name' => 'column_layout'), $this);?>

          </div>
        
        </div>
        
      </div>
      <div id="shadow-bottom"></div>
    </div>
  </body>
</html>