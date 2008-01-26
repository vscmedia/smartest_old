<?php /* Smarty version 2.6.18, created on 2007-11-26 03:35:59
         compiled from /var/www/html/Presentation/Masters/home_page.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'container', '/var/www/html/Presentation/Masters/home_page.tpl', 51, false),array('function', 'field', '/var/www/html/Presentation/Masters/home_page.tpl', 53, false),)), $this); ?>
<html>
  
  <head>
    
    <title><?php echo $this->_tpl_vars['this']['page']['formatted_title']; ?>
</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <meta name="description" content="<?php echo $this->_tpl_vars['this']['page']['meta_description']; ?>
" />
    <meta name="keywords" content="<?php echo $this->_tpl_vars['this']['page']['keywords']; ?>
" />
    
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/layout.css" />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/global_style.css" />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/home_colors.css" />

    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Javascript/menus.js"></script>
    
  </head>
  
  <body>
  
    <div id="page-width-container">
      <div id="fixed-width-container">
        
        <div id="top-bar">
          <form action="<?php echo $this->_tpl_vars['domain']; ?>
search" method="get">
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
          <table cellpadding="0" cellspacing="0" border="0" style="width:100%;border:0px:height:100px;">
            <tr>
              <td style="width:201px" width="201"><a href="/"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/savannah_logo.gif" alt="Savannah Diamonds Ltd" border="0" id="logo" height="101" width="201" /></a></td>
              <td style="width:544px" width="544"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="544" height="101" id="movie" align="" border="0">
                 <param name="movie" value="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Assets/top_slides.swf" />
                 <embed src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Assets/top_slides.swf" quality="high" width="544" height="101" name="movie" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
              </object></td>
              <td style="width:207px" width="207"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/diamond.jpg" alt="image of diamond" border="0" height="101" width="207" /></td>
            </tr>
          </table>
        </div>
        
        <?php echo smarty_function_container(array('name' => 'menus'), $this);?>

        
        <!--<?php echo smarty_function_field(array('name' => 'category'), $this);?>
-->
        
        <?php echo smarty_function_container(array('name' => 'menu_bar'), $this);?>

        
        <div id="page-content">
          <?php echo smarty_function_container(array('name' => 'column_layout'), $this);?>

        </div>
      </div>
    </div>
  </body>
  
</html>