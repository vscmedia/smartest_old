<?php

/** $e is the exception thrown by SmartestInstallationStatusHelper. It is defined in SmartestResponse **/

require_once SM_ROOT_DIR.'System/Install/SmartestInstaller.class.php';

$installer = new SmartestInstaller();
$stage = $installer->getStage($e);
$screen = $stage->getParameter('screen');
$message = $stage->getParameter('message');

?>
<?php echo "<"."?xml version=\"1.0\"?".">\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    
    <title>Install Smartest</title>
    <link rel="stylesheet" href="Resources/System/Stylesheets/sm_installer.css" />
    
  </head>
  <body>
  	<div id="pagewidth">
 
  	  <div id="top-strip" style="background-image:url('Resources/System/Images/admin_top_bar_bg.gif')">
        <img src="Resources/System/Images/admin_top_bar_logo.gif" alt="Smartest" border="0" />
      </div>
      
      <div id="container">
        <h1>Install Smartest</h1>
        <?php if($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php include SM_ROOT_DIR.'System/Install/Screens/'.$screen; ?>
        <?php /* echo $e->getInstallationStatus(); */ ?>
      </div>

    </div>
  </body>
</html>    