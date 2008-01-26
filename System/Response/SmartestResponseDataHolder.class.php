<?php

/**
 * undocumented class
 *
 * @package Smartest
 * @author Marcus Gilroy-Ware
 **/
 
class SmartestResponseDataHolder{
    
    private $_data = array();
    
    public function __construct(){
        
    }
    
    final public function get($object_name){
		
		if(strlen($object_name)){
			
			// $key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key = $name;
			
			if(array_key_exists($key, $this->_data)){
				return $this->_data[$key];
			}else{
				return null;
			}
		}
	}
	
	final public function set($object_name, $data){
		
		if(strlen($object_name)){
			
			// $key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key = $name;
			
			$this->_data[$key] = $data;
		}
	}
	
	final public function clear($object_name){
	    
	    if(strlen($object_name)){
			
			// $key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key = $name;
			
			unset($this->_data[$key]);
		}
	}
	
	final public function has(){
	    
	    if(strlen($object_name)){
			
			// $key = 'smartest/';
			
			$parts = explode(':', $object_name);
			$name = implode('/', $parts);
			
			$key = $name;
			
			if(array_key_exists($key, $this->data)){
				return true;
			}else{
				return false;
			}
		}
	    
	}

} // END class 