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
    
    public static function getSmartestVersionInfo(){
        
        $sys = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/system.yml');
        
        $info = new SmartestParameterHolder("Smartest Version Information");
        $info->setParameter('revision', $sys['system']['info']['revision']);
        $info->setParameter('version', $sys['system']['info']['version']);
        $info->setParameter('minimum_database_version', $sys['system']['info']['minimum_database_version']);
        $info->setParameter('minimum_php_version', $sys['system']['info']['minimum_php_version']);
        
        // calculate build
        $revision = (int) $sys['system']['info']['revision'];
        $lastversion_last_revision = (int) $sys['system']['info']['lastversion_last_revision'];
        $build_int = $revision-$lastversion_last_revision;
        $info->setParameter('build_int', $build_int);
        $build = ($sys['system']['info']['version']*10).'.'.$build_int;
        $info->setParameter('build', $build);
        
        return $info;
        
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
	
	public function checkRequiredExtensionsLoaded(){
		
		$extensions = get_loaded_extensions();
		
		$dependencies = array(
		    "dom",
		    "json",
			"curl",
			"xmlreader",
			"xml",
			"mysql",
		);
		
		foreach($dependencies as $dep){	
		    $missing_extensions = array();
			if(!in_array($dep, $extensions)){
			    $missing_extensions[] = $dep;
				// $this->error("The PHP extension \"".$dep."\" is not installed or failed to load.", SM_ERROR_PHP);
			}
		}
		
		if(count($missing_extensions)){
		    $message = "Smartest cannot function without the following missing PHP extensions: ".implode(', ', $missing_extensions);
		    throw new SmartestException($message);
		}
		
	}
	
	public function checkRequiredFilesExist(){
		
		$needed_files = array(
			"System Information File" => SM_ROOT_DIR."System/Core/Info/system.yml",
			"Database Configuration File" => SM_ROOT_DIR."Configuration/database.ini"
		);
		
		$errors = array();
		
		foreach($needed_files as $label=>$file){
			if(!is_file($file) || !is_readable($file)){
				$errors[] = array("label"=>$label, "file"=>$file);
			}
		}
		
		if(count($errors) > 0){
			$this->missingFiles = $errors;
			
			foreach($this->missingFiles as $missing_file){
				// $this->error("The required file \"".$missing_file['file']."\" doesn't exist or isn't readable.", SM_ERROR_FILES);
				throw new SmartestException("The required file \"".$missing_file['file']."\" doesn't exist or isn't readable.", SM_ERROR_FILES);
			}
			
			return false;
		}else{
			return true;
		}
	}
	
	public function checkWritablePermissions(){
		
		$system_data = SmartestYamlHelper::toParameterHolder(SM_ROOT_DIR.'System/Core/Info/system.yml');
		$writable_files = $system_data->g('system')->g('writable_locations')->g('always')->getParameters();
		
		$errors = array();
		
		foreach($writable_files as $label=>$file){
			if(!is_writable($file)){
				$errors[] = SM_ROOT_DIR.$file;
			}
		}
		
		if(count($errors) > 0){
			$this->unwritableFiles = $errors;
			
			foreach($this->unwritableFiles as $unwritable_file){
			    
				if(is_file($unwritable_file)){
					// $this->error("The file \"".$unwritable_file."\" needs to be writable.", SM_ERROR_PERMISSIONS);
					throw new SmartestException("The file \"".$unwritable_file."\" needs to be writable.", SM_ERROR_PERMISSIONS);
				}else{
					// $this->error("The directory \"".$unwritable_file."\" needs to be writable.", SM_ERROR_PERMISSIONS);
					throw new SmartestException("The directory \"".$unwritable_file."\" needs to be writable.", SM_ERROR_PERMISSIONS);
				}
				
			}
			
			return false;
		}else{
			return true;
		}
		
	}
    
}