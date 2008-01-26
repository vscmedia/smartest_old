<?php echo "<"."?xml version=\"1.0\"?".">\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Smartest Errors</title>
    <style type="text/css">
      
      body{
        font-family:lucida grande,verdana,sans-serif;
        font-size:11px;
      }
      
      div#pagewidth{
	    width:100%;
      }

      div#container{
        width:600px;
        margin-left:auto;
        margin-right:auto
      }
      
      table.error-table{
        width:600px;
        border:1px solid #999;
        margin-top:15px;
      }
      
      h3{
        margin:0px;
        margin-top:30px;
      }
      
    </style>
  </head>
  <body>
<?php if(is_array($errors)): ?>
	<div id="pagewidth">
    <div id="container">
      <h3>Sorry, but the following errors have occurred:</h3>
      <table class="error-table" cellpadding="2" cellspacing="0">
        <tr style="background-color:#ccc">
          <td>List of Errors:</td>
        </tr>
<?php foreach($errors as $error): ?>
        <tr style="height:25px">
          <td><?php echo $error->getVerboseType() ?> Error:</td>
        </tr>
        <tr>
          <td>
            <img src="<?php echo defined("SM_CONTROLLER_DOMAIN") ? SM_CONTROLLER_DOMAIN : "/"; ?>Resources/Icons/exclamation.png" alt="" style="display:block;float:left" />
            <div style="display:block;float:left;margin-left:5px"><?php echo $error->getMessage() ?></div></td>
        </tr>
<?php endforeach; ?>
      </table>
    </div>
    </div>
<?php endif; ?>
  </body>
</html>
