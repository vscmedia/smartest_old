<?php

SmartestHelper::register('Configuration');

class SmartestConfigurationHelper extends SmartestHelper{

	var $options;

	function __construct(){
	
	}
	
	function getUserOptions(){
		
		// Load general user options
		if(SmartestCache::hasData('main_user_options', true) && !(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE'))){
			$useroptions = SmartestCache::load('main_user_options', true);
		}else{
			if($useroptions = @parse_ini_file(SM_ROOT_DIR."Configuration/options.ini")){
				if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
					SmartestCache::save('main_user_options', $useroptions, -1, true);
				}
			}else{
				throw new SmartestException('Error parsing configuration file: '.SM_ROOT_DIR."Configuration/options.ini");
			}
		}
		
		// define constants
		foreach($useroptions as $varname => $value){
			$constant_name = "SM_OPTIONS_".strtoupper($varname);
			
			if(!defined($constant_name)){
				define($constant_name, $value);
			}
		}
		
		return $useroptions;
	}
	
	function getSystemOptions(){
		
		// Load system options
		if(SmartestCache::hasData('system_options', true) && !(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE'))){
			$systemoptions = SmartestCache::load('system_options', true);
		}else{
			if($systemoptions = @parse_ini_file(SM_ROOT_DIR."Configuration/system.ini")){
				if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
					SmartestCache::save('system_options', $systemoptions, -1, true);
				}
			}else{
				throw new SmartestException('Error parsing configuration file: '.SM_ROOT_DIR."Configuration/system.ini");
			}
		}
		
		// define constants
		foreach($systemoptions as $varname => $value){
			$constant_name = "SM_SYSTEM_".strtoupper($varname);

			if(!defined($constant_name)){
				define($constant_name, $value);
			}
		}
		
		return $systemoptions;
	}
	
	function getMeasuringUnits(){
		$unit_groups = array();
		if(is_file("Configuration/units.ini")){
			$raw_units = @parse_ini_file(SM_ROOT_DIR."Configuration/units.ini", true);
			if(is_array($raw_units)){
				$unit_groups = $raw_units;
			}
		}
	}
	
	function flushAll(){
		
		SmartestCache::clear('main_user_options', true);
		SmartestCache::clear('system_options', true);
		
		/*$_SESSION["useroptions"] = parse_ini_file(SM_ROOT_DIR."Configuration/options.ini");
		$useroptions =& $_SESSION["useroptions"];
		foreach($useroptions as $varname=>$value){
			$constant_name = "SM_OPTIONS_".strtoupper($varname);
			// echo $constant_name;
			if(!defined($constant_name)){
				define($constant_name, $value);
			}
		}
		
		$_SESSION["systemoptions"] = parse_ini_file(SM_ROOT_DIR."Configuration/system.ini");
		$systemoptions =& $_SESSION["systemoptions"];
		foreach($systemoptions as $varname=>$value){
			$constant_name = "SM_SYSTEM_".strtoupper($varname);
			// echo $constant_name;
			if(!defined($constant_name)){
				define($constant_name, $value);
			}
		} */
	}
	
	function getRegistrationCode(){
		if(is_file("System/CoreInfo/registration.ini")){
			$reg_file_contents = parse_ini_file(SM_ROOT_DIR."System/CoreInfo/registration.ini");
			if($reg_file_contents['reg_key']){
				return $reg_file_contents['reg_key'];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function setRegistrationCode($code){
		
	}
	
	function getRegistrationCodeIsValid($code){
		
	}
	
	public static function parseConfigDataArray($data, $prefix){
	    
	    $final_array = array();
	    
	    if(is_array($data)){
	        
	        // $final_array[$prefix] = $data;
	        
	        foreach($data as $key => $value){
	            
	            $new_prefix = $prefix.'/'.$key;
	            $new_data = SmartestConfigurationHelper::parseConfigDataArray($value, $new_prefix);
	            
	            if(is_array($new_data)){
	                $final_array[$new_prefix] = $new_data;
	            }else{
	                $final_array[$key] = $new_data;
	            }
	            
	            echo '$new_prefix is '.$new_prefix.' and $key is '.$key.'. ';
	            
	            echo "<br />\n";
	            // $final_array[$key] = SmartestConfigurationHelper::parseConfigDataArray($value, $new_prefix);
	            
	        }
	        
	        if(is_array($final_array[$new_prefix])){
	            foreach($final_array[$new_prefix] as $key => $value){
	                $next_prefix = $new_prefix.'/'.$key;
	                $final_array[$next_prefix] = $new_data[$key];
	            }
            }
	        
	    }else{
	        // return array($prefix=>$data);
	        return $data;
	    }
	    
	    return $final_array;
	}

}