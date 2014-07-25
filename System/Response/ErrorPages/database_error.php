<!DOCTYPE html>

<html lang="en">
  <head>
    <title>Database Error</title>
    <link rel="stylesheet" href="/Resources/System/Stylesheets/sm_error.css" />
    <meta charset="UTF-8" />
  </head>
  <body>
    <div id="fixed-width">
      <h1>Database error</h1>
      <p>Smartest is sorry, but it hasn't been possible to connect to the database in order to retrieve the content you've requested.</p>
      <p class="technical">Technical info: <?php echo $error_message; ?></p>
    </div>
  </body>
</html>