<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  
  <head>
    
    <title>{$this.page.formatted_title}</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    
    <link rel="stylesheet" href="{$domain}Resources/Stylesheets/saponin_layout.css" />
    <link rel="stylesheet" href="{$domain}Resources/Stylesheets/saponin_global_style.css" />
    <link rel="stylesheet" href="{$domain}Resources/Stylesheets/saponin_article_colors.css" />
    <!--[if IE 6]>
    <link rel="stylesheet" href="{$domain}Resources/Stylesheets/saponin_ie6_tweaks.css" />
    <![endif]-->

    <script type="text/javascript" language="javascript" src="{$domain}Resources/Javascript/saponin_menus.js"></script>
    
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
                <td><a href="/"><img src="{$domain}Resources/Images/saponin_logo_article.gif" alt="Saponin, Inc" border="0" id="logo" height="116" width="279" /></a></td>
                <td>
                  <img src="{$domain}Resources/Images/prairie_carnation_banner.jpg" alt="image of prairie carnations" style="display:block" />
                </td>
              </tr>
            </table>
          </div>
        
          {container name="menus"}
        
          <img id="pink-strip" src="{$domain}Resources/Images/pink-strip.gif" alt="" />
        
          {container name="menu_bar"}
        
          <div id="page-content">
            <!--{field name="category"}-->
            {container name="column_layout"}
          </div>
        
        </div>
        
      </div>
      <div id="shadow-bottom"></div>
    </div>
  </body>
  
</html>