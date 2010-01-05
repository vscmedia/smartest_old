<?php

class SmartestBoolean implements SmartestBasicType, ArrayAccess{
    
    protected $_value;
    
    public function __construct($v=''){
        $this->setValue($v);
    }
    
    public function setValue($v){
        $this->_value = SmartestStringHelper::toRealBool($v);
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function __toString(){
        return $this->_value ? 'TRUE' : 'FALSE';
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('value', 'storedValue', 'int', 'bool', 'string', 'cssdisplayblock', 'cssdisplayinline', 'english'));
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "value":
            case "bool":
            return $this->getValue();
            case 'storedValue':
            case 'string':
            return $this->__toString();
            case 'english':
            return $this->getValue() ? 'Yes' : 'No';
            case 'int':
            return (int) $this->_value;
            case 'cssdisplayblock':
            return 'display:'.$this->_value ? 'block' : 'none';
            case 'cssdisplayinline':
            return 'display:'.$this->_value ? 'inline' : 'none';
        }
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
    
}