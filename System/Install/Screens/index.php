<?php

/** $e is the exception thrown by SmartestInstallationStatusHelper. It is defined in SmartestResponse **/

require_once SM_ROOT_DIR.'System/Install/SmartestInstaller.class.php';

$installer = new SmartestInstaller();
$stage = $installer->getStage($e);
$screen = $stage->getParameter('screen');
$message = $stage->getParameter('message');

?>

<!DOCTYPE html>

<html lang="en">
  <head>
    
    <title>Install Smartest</title>
    <link rel="stylesheet" href="Resources/System/Stylesheets/sm_installer.css" />
    
  </head>
  <body>
  	<div id="pagewidth">
 
  	  <div id="top-strip">
        <a href="http://sma.rte.st/" target="_blank"><img src="Resources/System/Images/smartest-ui-logo-topleft.png" alt="Smartest" border="0" /></a>
      </div>
      
      <div id="container">
        <h1>Install Smartest</h1>
        <?php if($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php include SM_ROOT_DIR.'System/Install/Screens/'.$screen; ?>
      </div>
      
      <div class="v-spacer"></div>

    </div>
  </body>
</html>    