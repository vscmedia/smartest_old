<?php

class SmartestExternalUrl implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_value;
    
    public function __construct($v=''){
        if(strlen($v)){
            $this->_value = $v;
        }
    }
    
    public function setValue($v){
        $this->_value = $v;
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function __toString(){
        return $this->_value;
    }
    
    // The next three methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_value;
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('_host', '_request', '_protocol'));
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "_host":
            return $this->getValue();
            case '_request':
            return $this->getValue();
            case '_protocol':
            return $this->getValue();
        }
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
    
}