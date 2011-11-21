<?php

// A class that combines Integer and Double/Float type values. There is no need to separate these in Smartest's case.

class SmartestNumeric implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{

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
    
    public function isPresent(){
        return is_numeric($this->_value);
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_value;
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function offsetExists($offset){
        return false;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "currency":
            return number_format($this->_value, 2, '.', ',');
            case "currency_eur":
            return number_format($this->_value, 2, ',', '.');
        }
        
        // if(preg_match('/d([\d_]+)/', $offset, $matches)){} // Divide the value by x
        // if(preg_match('/b([\d_]+)/', $offset, $matches)){} // Divide x by the value
        // if(preg_match('/m([\d_]+)/', $offset, $matches)){} // Multiply the value from x
        // if(preg_match('/s([\d_]+)/', $offset, $matches)){} // Subtract x from the value
        // if(preg_match('/f([\d_]+)/', $offset, $matches)){} // Subtract the value from x
        // if(preg_match('/a([\d_]+)/', $offset, $matches)){} // Add x to the value
        
        return null;
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}

}