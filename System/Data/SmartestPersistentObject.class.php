<?php

class SmartestPersistentObject{

	private static $_objects = array();
	
	final public static function get($object_name){
		
		if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			if(array_key_exists($key, $GLOBALS)){
				return $GLOBALS[$key];
			}else{
				return null;
			}
		}
	}
	
	final public static function set($object_name, $data){
		
		if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			// echo $key." \n";
			
			if(isset($GLOBALS)){
			    $GLOBALS[$key] = &$data;
			}else{
			    throw new SmartestException('SmartestSession or SmartestPersistentObject used while session was not active');
			}
		}
		
		// print_r($GLOBALS);
		
	}
	
	final public static function clear($object_name){
	    if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			if(array_key_exists($key, $GLOBALS)){
			    unset($GLOBALS[$key]);
			    return true;
		    }else{
		        return false;
		    }
		}
	}
	
	final public static function hasData($object_name){
	    if(strlen($object_name)){
			
			$key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key .= $name;
			
			if(array_key_exists($key, $GLOBALS)){
			    return true;
		    }else{
		        return false;
		    }
		}
	}

}