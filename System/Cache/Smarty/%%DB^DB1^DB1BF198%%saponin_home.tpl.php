<?php /* Smarty version 2.6.18, created on 2007-12-03 12:12:13
         compiled from /var/www/html/Presentation/Masters/saponin_home.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'container', '/var/www/html/Presentation/Masters/saponin_home.tpl', 61, false),array('function', 'field', '/var/www/html/Presentation/Masters/saponin_home.tpl', 68, false),)), $this); ?>
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
Resources/Stylesheets/saponin_home_colors.css" />
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
Resources/Images/saponin_logo.gif" alt="Saponin, Inc" border="0" id="logo" height="116" width="279" /></a></td>
                <td>
                  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="619" height="116" id="movie" align="" border="0">
                    <param name="movie" value="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Assets/saponin_top_slides.swf" />
                    <embed src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Assets/saponin_top_slides.swf" quality="high" width="619" height="116" name="movie" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
                  </object>
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