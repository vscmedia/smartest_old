<?php

class SmartestUrl implements SmartestBasicType, ArrayAccess{
    
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
    
    public function offsetExists($offset){
        return in_array($offset, array('host', 'request', 'protocol'));
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "host":
            return $this->getValue();
            case 'request':
            return $this->getValue();
            case 'protocol':
            return $this->getValue();
        }
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
    
}