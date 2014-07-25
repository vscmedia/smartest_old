<?php

$urlp = explode('?', $_SERVER['REQUEST_URI']);
$request = $urlp[0];

if($request == '/'){
    $controller_domain = '';
    $show_cd = false;
}else{
    $controller_domain = substr($request, 1);
    $controller_domain = substr($controller_domain, -1);
    $show_cd = true;
}

if($stage->getParameter('db_connection_parameters')){
    $db_username = $stage->getParameter('db_connection_parameters')->getParameter('username');
    $db_database = $stage->getParameter('db_connection_parameters')->getParameter('database');
    $db_host = $stage->getParameter('db_connection_parameters')->getParameter('host');
}else{
    $db_username = 'username';
    $db_database = '';
    $db_host = 'localhost';
}

?>

<?php if($stage->hasParameter('errors') && $stage->getParameter('errors')->hasData()): ?>
<ul class="errors-list">
    <?php foreach($stage->getParameter('errors')->getParameters() as $error): ?>
    <li><?php echo $error ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<p>Step 2 of 4: Basic configuration</p>

<form action="" method="post" id="installerForm">

<input type="hidden" name="execute" value="1" />
<input type="hidden" name="action" value="createConfigs" />

<div class="form-section-label">Database</div>

<p>Please enter connection details for an empty database that you have created specifically for your new Smartest installation. If you haven't created one yet, do that first, then come back and do this.</p>

<div class="form-row">
    <div class="form-row-label">Username</div>
    <input type="text" name="db_username" value="<?php echo $db_username; ?>" />
</div>

<div class="form-row">
    <div class="form-row-label">Password</div>
    <input type="text" name="db_password" />
</div>

<div class="form-row">
    <div class="form-row-label">Database Name</div>
    <input type="text" name="db_database" value="<?php echo $db_database; ?>" />
</div>

<div class="form-row">
    <div class="form-row-label">Host</div>
    <input type="text" name="db_host" value="<?php echo $db_host; ?>" />
</div>

<?php if($show_cd): ?>
    
<div class="form-section-label">Address</div>

<div class="form-row">
    <div class="form-row-label">URL Path</div>
    http://<?php echo $_SERVER['HTTP_HOST']; ?>/<input type="text" name="controller_domain" style="width:150px" value="<?php echo substr($_SERVER['REQUEST_URI'], 1, -1); ?>" />/smartest
    <div class="hint">You only need to put something in here if you are not running Smartest with its own host name, for example http://<?php echo $_SERVER['HTTP_HOST']; ?><strong>/running/in/a/folder/</strong>smartest</div>
</div>

<?php else: ?>



<?php endif; ?>

<div class="button normal-button"><a href="javascript:document.getElementById('installerForm').submit();">Next</a></div>

</form>