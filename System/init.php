<?php

// set up required paths and environmental constants

define('SM_INFO_REVISION_NUMBER', 171);
define('SM_INFO_VERSION_NUMBER', '0.3.5');

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
		require SM_ROOT_DIR.'System/Response/SmartestResponse.class.php';
        
        // $everything is everything
		$everything = new SmartestResponse;
		$everything->init();
		$everything->build();
		$everything->finish();
		
	}
	
	
}
