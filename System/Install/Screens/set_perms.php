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
  <?php $u = posix_getpwuid(fileowner(SM_ROOT_DIR.$errors[0].'.')); ?>
<p>Smartest has helpfully created a bash script to do all this for you.</p>

<p>To check the script before you run it, type:
  <code>
    less <?php echo $stage->g('perms')->g('script_name'); ?>
  </code>
</p>

<p>To run it, type the following (preferably as user <strong><?php echo $u['name']?></strong> or <strong>root</strong>).
  <code>
    bash <?php echo $stage->g('perms')->g('script_name'); ?>
  </code>
</p>

<p>Alternatively if you're not comfortable using a terminal, get a friend or your server administrator to do it.</p>
<p>Once you've done this, click "Next"</p>
<?php else: ?>
<p>To make a directory writable, log into your server with a terminal and type this:</p>
<p><code>chmod 777 <?php echo $errors[0]; ?></code></p>
<p>Alternatively if you're not comfortable using a terminal, get a friend or your server administrator to do it.</p>
<p>Once you've done this for each of the folders listed above, click "Next"</p>
<?php endif; ?>

<div class="button normal-button"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Next</a></div>