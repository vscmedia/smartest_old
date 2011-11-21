<?php

class SmartestBoolean implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
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
    
    public function isPresent(){
        return !is_null($this->_value);
    }
    
    public function __toString(){
        return $this->_value ? 'TRUE' : 'FALSE';
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->__toString();
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // And SmartestSubmittableValue
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function renderInput($params){
        
    }
    
    public function getHtmlFormFormat(){
        return $this->_value;
    }
    
    public function stdObjectOrScalar(){
        return $this->_value;
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
            case 'numeric':
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