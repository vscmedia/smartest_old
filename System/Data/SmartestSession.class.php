<?php

class SmartestSession{
    
    const ALL = 100;
    const OBJECTS = 101;
    const NOTFALSE = 102;
    const NOT_FALSE = 102;
    
    final public static function isRegistered(){
        return isset($_SESSION);
    }
    
	final static function get($object_name){
		
		if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			if(array_key_exists($key, $_SESSION)){
				return $_SESSION[$key];
			}else{
				return null;
			}
		}
	}
	
	final static function set($object_name, $data){
		
		if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			if(isset($_SESSION)){
			    $_SESSION[$key] = &$data;
			}else{
			    throw new SmartestException('SmartestSession or SmartestPersistentObject used while session was not active');
			}
		}
	}
	
	function clear($object_name){
	    if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			if(array_key_exists($key, $_SESSION)){
			    unset($_SESSION[$key]);
			    return true;
		    }else{
		        return false;
		    }
		}
	}
	
	function hasData($object_name){
	    if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			if(array_key_exists($key, $_SESSION)){
			    return true;
		    }else{
		        return false;
		    }
		}
	}
	
	function clearAll($killNonSmartest=false){
	    
	    if($killNonSmartest){
	        $killed = array_keys($_SESSION);
	        session_destroy();
	    }else{
	        $killed = array();
	        foreach($_SESSION as $name=>$value){
	            if(substr($name, 0, 9) == 'smartest/'){
	                $killed[] = $name;
	                unset($_SESSION[$name]);
	            }
	        }
	    }
	    
	    return $killed;
	    
	}
	
	function getRegisteredNames($type = 100){
	    
	    $vars = array();
	    
	    switch($type){
	        case self::ALL:
	        foreach($_SESSION as $name=>$value){
	            if(substr($name, 0, 9) == 'smartest/'){
	                $vars[] = $name;
	            }
	        }
	        break;
	        
	        case self::OBJECTS:
	        foreach($_SESSION as $name=>$value){
	            if(substr($name, 0, 9) == 'smartest/' && is_object($value)){
	                $vars[] = $name;
	            }
	        }
	        break;
	        
	        case self::NOTFALSE:
	        foreach($_SESSION as $name=>$value){
	            if(substr($name, 0, 9) == 'smartest/' && $value){
	                $vars[] = $name;
	            }
	        }
	        break;
	    }
	    
	    return $vars;
	}
	
}