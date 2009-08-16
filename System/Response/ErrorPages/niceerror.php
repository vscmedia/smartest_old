<?php echo "<"."?xml version=\"1.0\"?".">\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>This Site Is Offline</title>
    <style type="text/css">
      
      body{
        font-family:lucida grande,verdana,sans-serif;
        font-size:12px;
      }
      
      div.container{
        width:600px;
        margin-left:auto;
        margin-right:auto;
      }
      
      table.error-table{
        width:600px;
        border:1px solid #999;
        margin-top:15px;
      }
      
      p{
        margin:0px;
        margin-top:15px;
      }
      
    </style>
  </head>
  <body>
    <div class="container">
      <br /><br /><img src="<?php echo defined('QUINCE_URL_DOMAIN') ? QUINCE_URL_DOMAIN : '/'; ?>Resources/System/Images/smartest.jpg" title="Smartest" alt="Smartest" />
      <p>Sorry, but this site is currently offline. Please try again later.</p>
    </div>
    <script language="javascript">if(parent){parent.showPreview();}</script>
  </body>
</html>
