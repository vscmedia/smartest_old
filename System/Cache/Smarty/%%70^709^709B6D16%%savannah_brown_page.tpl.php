<?php /* Smarty version 2.6.18, created on 2007-12-02 07:15:54
         compiled from /var/www/html/Presentation/Masters/savannah_brown_page.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'placeholder', '/var/www/html/Presentation/Masters/savannah_brown_page.tpl', 41, false),array('function', 'container', '/var/www/html/Presentation/Masters/savannah_brown_page.tpl', 47, false),array('function', 'field', '/var/www/html/Presentation/Masters/savannah_brown_page.tpl', 49, false),)), $this); ?>
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
Resources/Stylesheets/savannah_layout.css" />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/savannah_global_style.css" />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Stylesheets/savannah_brown_colors.css" />
    
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Javascript/menus.js"></script>
    
  </head>
  
  <body>  
  
    <div id="page-width-container">
      <div id="fixed-width-container">
        
        <div id="top-bar">
          <form action="<?php echo $this->_tpl_vars['domain']; ?>
search" method="get">
            <div class="form-element">
              <input type="search" name="q" />
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
          <table cellpadding="0" cellspacing="0" border="0" style="width:100%">
            <tr>
              <td><a href="/"><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/savannah_logo.gif" alt="Savannah Diamonds Ltd" border="0" id="logo" /></a></td>
              <td><?php echo smarty_function_placeholder(array('name' => 'static_banner_image'), $this);?>
</td>
              <td><img src="<?php echo $this->_tpl_vars['domain']; ?>
Resources/Images/diamond.jpg" alt="image of diamond" /></td>
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