<?php

class SmartestString{
	
	protected $_string;
	
    public function __construct($string){
        $this->_string = $string;
    }
    
    public function __toString(){
        return $this->_string;
    }
    
    public function toSlug(){
    	return SmartestStringHelper::toSlug($this->_string);
    }
    
    public function toVarName(){
    	return SmartestStringHelper::toVarName($this->_string);
    }
    
    public function toConstantName(){
    	return SmartestStringHelper::toConstantName($this->_string);
    }
    
    public function toCamelCase(){
    	return SmartestStringHelper::toCamelCase($this->_string);
    }
    
    public function isMd5Hash(){
    	return SmartestStringHelper::isMd5Hash($this->_string);
    }
 
}