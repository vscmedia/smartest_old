<?php
// var_dump($system_data);
$writable_files = array_merge($system_data->g('system')->g('writable_locations')->g('always')->toArray(), $system_data->g('system')->g('writable_locations')->g('installation')->toArray());
/* $writable_files = array(
    SM_ROOT_DIR."Public/",
    SM_ROOT_DIR."Sites/",
    SM_ROOT_DIR."Configuration/",
	SM_ROOT_DIR."System/Core/Info/",
	SM_ROOT_DIR."System/Cache/SmartestEngine/",
	SM_ROOT_DIR."System/Cache/Smarty/",
	SM_ROOT_DIR."System/Cache/Pages/",
	SM_ROOT_DIR."System/Cache/Data/",
	SM_ROOT_DIR."System/Cache/Includes/",
	SM_ROOT_DIR."System/Cache/ObjectModel/Models/",
	SM_ROOT_DIR."System/Cache/ObjectModel/DataObjects/",
	SM_ROOT_DIR."System/Cache/Settings/",
	SM_ROOT_DIR."System/Cache/Controller/",
	SM_ROOT_DIR."Library/ObjectModel/",
	SM_ROOT_DIR."Documents/",
	SM_ROOT_DIR."Documents/Deleted/",
	SM_ROOT_DIR."System/Temporary/",
	SM_ROOT_DIR."System/Cache/TextFragments/Previews/",
	SM_ROOT_DIR."System/Cache/TextFragments/Live/",
	SM_ROOT_DIR."Logs/",
	SM_ROOT_DIR."System/Logs/"
); */ ?>

<p>Step 1 of 4: Permissions</p>

<p>Welcome to Smartest! Before you start, Smartest needs to be able to write files into the following places:</p>

<ul>

<?php

$errors = array();

foreach($writable_files as $file){
	if(!is_writable($file)){
		$errors[] = SM_ROOT_DIR.$file;
	}
}

foreach($errors as $file){
	echo "<li>".$file."</li>";
}

?>

</ul>

<p>To make a directory writable, log into your server with a terminal and type this:</p>

<p><code>chmod 777 <?php echo $errors[0]; ?></code></p>

<p>Alternatively if you're not comfortable using a terminal, get a friend or your server administrator to do it.</p>

<p>Once you've done this for each of the folders listed above, click "Next"</p>

<div class="button normal-button"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Next</a></div>