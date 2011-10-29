<?php

class SmartestParameterHolderValuePresenceChecker implements ArrayAccess{
    
    protected $_keys;
    
    function __construct($keys){
        $this->_keys = $keys;
    }
    
    public function offsetGet($offset){
        
        return in_array($offset, $this->_keys);
        
    }
    
    public function offsetExists($offset){}
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
    
}