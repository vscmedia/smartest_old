<?php

// set up required paths and environmental constants

function debug_time(){
    return number_format(microtime(true)*1000, 0, ".", "");
}

// set the debug level for the controller
define("SM_CONTROLLER_DEBUG_LEVEL", 0);
define("SM_DEVELOPER_MODE", true);

// If your host does not allow the use of ini_set(), comment out these three lines and see Public/.htaccess
ini_set('session.name', 'SMARTEST');
ini_set('session.auto_start', 0);

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
		
		// error reporting control
        error_reporting(E_ALL ^ E_NOTICE);
        
        if(is_writable(SM_ROOT_DIR.'System/Logs/')){
            // If PHP error messages can be logged, they should be.
            ini_set('error_log', SM_ROOT_DIR.'System/Logs/php_errors_no_date.log');
            ini_set('log_errors', true);
            //ini_set('display_errors', true);  // Sergiy: Totally breaks displaying of pages on PHP 5.4 when uncommented,
                                                // since fires so many E_STRICT and E_NOTICE and their details.
                                                // IMHO, it should be enabled only in optional debug/dev mode up to
                                                // admin or developer preference and probably better from php.ini only
                                                // Or to implement option whether set it here or use php.ini defaults


        }
	    
	    require SM_ROOT_DIR.'System/Base/constants.php';
		require SM_ROOT_DIR.'System/Response/SmartestResponse.class.php';
        
        // as the Donny Hathaway song says, $everything is everything
		$everything = new SmartestResponse;
		$everything->init();
		$everything->build();
		$everything->finish();
		
	}

}