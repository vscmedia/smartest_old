<html>
  
  <head>
    <title>{$this.page.formatted_title}</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <meta name="description" content="{$this.page.meta_description}" />
    <meta name="keywords" content="{$this.page.keywords}" />
    
    <link rel="stylesheet" href="{$domain}Resources/Stylesheets/savannah_layout.css" />
    <link rel="stylesheet" href="{$domain}Resources/Stylesheets/savannah_global_style.css" />
    <link rel="stylesheet" href="{$domain}Resources/Stylesheets/savannah_pink_colors.css" />
    {if $this.page.is_tag_page}<link rel="alternate" type="application/rss+xml" title="{$this.page.formatted_title} - Feed" href="{$domain}tags/{$this.page.tag.name}/feed" />{/if}
    
    <script type="text/javascript" src="{$domain}Resources/Javascript/menus.js"></script>
    
  </head>
  
  <body>
    
    <div id="page-width-container">
      <div id="fixed-width-container">
        
        <div id="top-bar">
          <form action="{$domain}search" method="get">
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
              <td><a href="/"><img src="{$domain}Resources/Images/savannah_logo.gif" alt="Savannah Diamonds Ltd" border="0" id="logo" /></a></td>
              <td>{placeholder name="static_banner_image"}</td>
              <td><img src="{$domain}Resources/Images/diamond.jpg" alt="image of diamond" /></td>
            </tr>
          </table>
        </div>
        
        {container name="menus"}
        
        <!--{field name="category"}-->
        
        {container name="menu_bar"}
        
        <div id="page-content">
          {container name="column_layout"}
        </div>
      </div>
    </div>
  </body>
  
</html>
