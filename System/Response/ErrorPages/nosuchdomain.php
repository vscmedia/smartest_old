<!DOCTYPE html>

<html lang="en">
  <head>
    <title>No such hostname</title>
    <link rel="stylesheet" href="/Resources/System/Stylesheets/sm_error.css" />
    <meta charset="UTF-8" />
  </head>
  <body>
    <div id="fixed-width">
      <h1>No such hostname</h1>
      <p>There isn't a website here!</p>
      <p class="technical">Technical info: No website is currently configured at this domain. Smartest can serve more than one website per installation, and recognises which site you want based on the hostname. <strong><?php echo $_SERVER['HTTP_HOST']; ?></strong> didn't match any records.</p>
    </div>
  </body>
</html>