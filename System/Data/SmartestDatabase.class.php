<?php

class SmartestDatabase{
    
    public static function getInstance($connection_name){
        
        $config = self::readConfiguration($connection_name);
        $class = $config['class'];
        
        if(strlen($class)){
            $object = new $class($config);
            return $object;
        }else{
            throw new SmartestException("Database connection '".$connection_name."' does not have a valid class, e.g. SmartestMysql");
        }
        
    }
    
    public static function readConfiguration($connection_name){
        
        $dbconfig = parse_ini_file(SM_ROOT_DIR."Configuration/database.ini", true);
        
        if(isset($dbconfig[$connection_name])){
        
		    $ph = new SmartestParameterHolder($dbconfig[$connection_name]['name']);
		
		    foreach($dbconfig[$connection_name] as $key => $value){
		        $ph->setParameter($key, $value);
		    }
		    
		    return $ph;
		
	    }else{
	        
	        throw new SmartestException("Unknown database connection name: ".$connection_name);
	        
	    }
    }
}