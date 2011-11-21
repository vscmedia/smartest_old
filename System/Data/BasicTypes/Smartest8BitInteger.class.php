<?php

class Smartest8BitInteger implements SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_value;
    
    public function __construct($v){
        $this->setValue($v);
    }
    
    public function setValue($v){
        if(strlen($v)){
            if(is_numeric($v)){
                if($v > -1 && $v < 256){
                    $this->_value = (int) $v;
                }else{
                    throw new SmartestException("Smartest8BitInteger expects a value between 0 and 255.");
                }
            }else{
                if(preg_match('/[0-9a-f]{1,2}/i', $v)){
                    // bit is being provided as hex
                    if(isset($v{1})){
                        $this->_value = (int) hexdec($v);
                    }else{
                        $this->_value = (int) hexdec($v.$v);
                    }
                }else{
                    throw new SmartestException("Smartest8BitInteger given a non-numeric, non-hex value: ".$v.'.');
                }
            }
        }else{
            $this->_value = 0;
        }
    }
    
    public function isPresent(){
        return !is_null($this->_value);
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_value;
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // The next two are part of SmartestSubmittableValue
    
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function renderInput($params){
        return "Smartest8BitInteger does not have a direct input.";
    }
    
    public function getHtmlFormFormat(){
        return $this->_value;
    }
    
    public function __toString(){
        return ''.$this->_value;
    }
    
    public function toPercentage(){
        return new SmartestNumeric($this->_value/255*100);
    }
    
    public function toHex(){
        return str_pad(dechex($this->_value), 2, '0', STR_PAD_LEFT);
    }
    
    public function toBinary(){
        return str_pad(decbin($this->_value), 8, '0', STR_PAD_LEFT);
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('_int', '_integer', '_binary', '_bin', '_hex', '_pc', '_percent'));
    }
    
    public function offsetGet($offset){
        switch($offset){
            case '_int':
            case '_integer':
            return $this->_value;
            case '_bin':
            case '_binary':
            return $this->toBinary();
            case '_hex':
            return $this->toHex();
            case '_pc':
            case '_percent':
            return $this->toPercentage();
        }
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}

}