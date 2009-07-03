<p>Step 1 of 4: Permissions</p>

<p>Welcome to Smartest! Before you start, Smartest needs to be able to write files into the following places:</p>

<ul>

<?php

$errors = $stage->g('perms')->g('errors');

foreach($errors as $file){
	echo "<li>".$file."</li>";
}

?>

</ul>

<?php if ($stage->g('perms')->g('script_created')): ?>
<p>Smartest has helpfully created a bash script to do all this for you. To run it, type the following as root.</p>
<p><code>bash <?php echo $stage->g('perms')->g('script_name'); ?></code></p>
<?php else: ?>
<p>To make a directory writable, log into your server with a terminal and type this:</p>
<p><code>chmod 777 <?php echo $errors[0]; ?></code></p>
<?php endif; ?>

<p>Alternatively if you're not comfortable using a terminal, get a friend or your server administrator to do it.</p>

<p>Once you've done this for each of the folders listed above, click "Next"</p>

<div class="button normal-button"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Next</a></div>