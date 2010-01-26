<?php

// A class that combines Integer and Double/Float type values. There is no need to separate these in Smartest's case.

class SmartestNumeric implements SmartestBasicType, ArrayAccess{

    protected $_value;
	
    public function __construct($v=''){
        if(strlen($v)){
            $this->setValue($v);
        }else{
            $this->_value = 0;
        }
    }
    
    public function __toString(){
        return ''.$this->_value;
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function setValue($v){
        
        if(strlen($v)){
            $pos = strpos($v, '.');
        }else{
            $pos = false;
        }
        
        if($pos === false){
            $value = (int) $v;
        }else{
            $value = $v*1;
        }
        
        $this->_value = $value;
        
    }
    
    public function offsetExists($offset){
        return false;
    }
    
    public function offsetGet($offset){
        return null;
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}

}