<?php

class SmartestSystemHelper{
    
    public static function getOperatingSystem(){
        
        $linux = `head -n 1 /etc/issue`;
        $linux = trim(str_replace('\n', '', $linux));
        $linux = trim(str_replace('\l', '', $linux));
        
        if(strlen($linux)){
            return $linux;
        }else if(is_file('/Applications/Utilities/Terminal.app/Contents/version.plist')){
            // sw_vers | grep 'ProductVersion:' | grep -o '[0-9]*\.[0-9]*\.[0-9]*'
            $linux = "Mac OS X ".`sw_vers | grep 'ProductVersion:' | grep -o '[0-9]*\.[0-9]*\.[0-9]*'`;
            return $linux;
        }else{
            return "Unknown";
        }
        
    }
    
    public static function getInstallDate($refresh=false){
        
        if(SmartestSystemSettingHelper::hasData('_system_installed_timestamp') && !$refresh){
            $system_installed_timestamp = SmartestSystemSettingHelper::load('_system_installed_timestamp');
        }else{
            
            // Attempt to figure out when the system was set up by looking at the oldest page
            $sql = "SELECT page_created FROM Pages ORDER BY page_id ASC LIMIT 1";
            $db = SmartestDatabase::getInstance('SMARTEST');
            $r = $db->queryToArray($sql);
            $system_installed_timestamp = $r[0]['page_created'];
            
            if($system_installed_timestamp > 0){
                SmartestSystemSettingHelper::save('_system_installed_timestamp', $system_installed_timestamp);
            }
        }
        
        return $system_installed_timestamp;
        
    }
    
    public static function getWebServerSoftware(){
        // TODO: When Smartest has been tested on other web serers, this function will need to be amended
        preg_match('/(Apache\/(1|2.\d.\d))/', $_SERVER['SERVER_SOFTWARE'], $matches);
        $server = str_replace('/', ' ', $matches[1]);
        return $server;
    }
    
    public static function getPhpMemoryLimit($int_only=false){
        if($int_only){
            preg_match('/^(\d+)M/', ini_get('memory_limit'), $matches);
            return (int) $matches[1];
        }else{
            $memory = str_replace('M', ' MB', ini_get('memory_limit'));
            return $memory;
        }
        
    }
    
    public static function getPhpVersion(){
        $v = phpversion();
        preg_match('/^([456]\.\d+(\.\d+)?)/', $v, $matches);
        return $matches[1];
    }
    
    public static function getConstants($keys=false){
		$all_constants = get_defined_constants();
		
		$smartest_constants = array();
		
		foreach ($all_constants as $constant_name=>$constant_value){
			if(substr($constant_name, 0, 3) == 'SM_'){
				$smartest_constants[$constant_name] = $constant_value;
			}
		}
		
		if($keys == true){
			return array_keys($smartest_constants);
		}else{
			return $smartest_constants;
		}
	}
	
	public static function getClasses($keys=false){
		
		$all_classes = get_declared_classes();
		
		$smartest_classes = array();
		
		foreach ($all_classes as $class_name=>$class_value){
			if(substr($class_value, 0, 8) == 'Smartest'){
				$smartest_classes[] = $class_value;
			}
		}
		
		// $smartest_classes[] = $this->controller->getClassName();
		
		if($keys == true){
			return array_keys($smartest_classes);
		}else{
			return $smartest_classes;
		}
	}
    
}