<?php

class SmartestExternalUrl implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_value;
    protected $_curl_handle;
    
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
    
    public function getCurlHandle(){
        if(!$this->_curl_handle){
            $this->_curl_handle = curl_init($this->_value);
        }
        return $this->_curl_handle;
    }
    
    public function getCurlInfo(){
        $p = new SmartestParameterHolder("Curl Info for ".$this->_value);
        ob_start();
        $this->getCurlHandle();
        curl_exec($this->_curl_handle);
        ob_end_clean();
        $info = curl_getinfo($this->_curl_handle);
        curl_close($this->_curl_handle);
        $p->loadArray($info);
        return $p;
    }
    
    public function getHttpStatusCode(){
        $info = $this->getCurlInfo();
        return $info->g('http_code');
    }
    
}