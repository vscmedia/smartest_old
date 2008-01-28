#!/usr/bin/env php
<?php

if($argc < 2){
    fwrite(STDOUT, "You cannot call the Smartest command without any arguments.\n");
}else{
    
    $arguments = $argv;
    array_shift($arguments);

    $args_string = implode(" ", $arguments);
    
    $task = $arguments[0];
    array_shift($arguments);
    
    $action = $arguments[0];                                                                                                                              
    array_shift($arguments);
    
    // fwrite(STDOUT, $args_string."\n");
    if(is_file(getcwd().DIRECTORY_SEPARATOR.'System'.DIRECTORY_SEPARATOR.'CoreInfo'.DIRECTORY_SEPARATOR.'package.xml')){
	
	define('SM_ROOT_DIR', getcwd().DIRECTORY_SEPARATOR);
	define('SM_ENV', 'CLI');
	
	include SM_ROOT_DIR.'System/Data/SmartestCache.class.php';
	include SM_ROOT_DIR.'System/Helpers/SmartestHelper.class.php';
	
	fwrite(STDOUT, SM_ROOT_DIR."\n");
	fwrite(STDOUT, "Starting $task...\n");
	
	// file_put_contents("testmarcus.php", "testmarcus.php");
    }else{
	fwrite(STDOUT, "ERROR: You must be in a Smartest project directory\n");
	// fwrite(STDOUT, "You are in: ".getcwd().DIRECTORY_SEPARATOR."\n");
	//	file_put_contents("testmarcus.php", "testmarcus.php");
    }
}

// Exit correctly
exit(0);
?>
