<?php

// set up required paths and environmental constants

function debug_time(){
    return number_format(microtime(true)*1000, 0, ".", "");
}

ini_set('session.gc_maxlifetime', 30*60);

// error reporting control
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

// set the debug level for the controller
define("SM_CONTROLLER_DEBUG_LEVEL", 0);
define("SM_DEVELOPER_MODE", true);

class SmartestInit{

	static function setRootDir(){
	
		if(!defined('SM_ROOT_DIR')){
		
			chdir('../');
			define("SM_ROOT_DIR", getcwd().DIRECTORY_SEPARATOR);
		
		}
	}
	
	static function setIncludePaths(){
		
		$existing_include_path = get_include_path();
		
		if(!defined('PATH_SEPARATOR')){
			define('PATH_SEPARATOR', ':');
		}
		
		$ip_array = explode(constant('PATH_SEPARATOR'), $existing_include_path);
		
		$new_array = array('.');
		
		$new_array[] = SM_ROOT_DIR."Library/Smarty/";
		$new_array[] = SM_ROOT_DIR."Library/Pear/";
		$new_array[] = SM_ROOT_DIR."Library/";
		
		foreach($ip_array as $path){
			if($path != '.'){
				$new_array[] = $path;
			}
		}
		
		$new_include_path = implode(constant('PATH_SEPARATOR'), $new_array);
		
		set_include_path($new_include_path);
		
	}
	
	static function go(){
	    
	    self::setRootDir();
		self::setIncludePaths();
	    
	    require SM_ROOT_DIR.'System/Base/constants.php';
		require SM_ROOT_DIR.'System/Response/SmartestResponse-new.class.php';
        
        // as the Donny Hathaway song says, $everything is everything
		$everything = new SmartestResponse;
		$everything->init();
		$everything->build();
		$everything->finish();
		
	}

}