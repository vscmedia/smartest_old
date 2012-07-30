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
        return ''.$this->_value;
    }
    
    public function isPresent(){
        return (bool) strlen($this->_value);
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
    
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function renderInput($params){
        
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('_host', '_request', '_protocol', 'encoded'));
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "_host":
            return $this->getValue();
            case '_request':
            return $this->getValue();
            case '_protocol':
            return $this->getValue();
            case "encoded":
            case "urlencoded":
            return urlencode($this->getValue());
            case 'qr_code_url':
            return $this->getQrCodeUri();
            case 'qr_code_image':
            return $this->getQrCodeImage();
            case 'empty':
            return !strlen($this->getValue());
            case 'string':
            return $this->__toString();
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
    
    public function getQrCodeUri($encode=true, $size=200){
        if($encode){
            return 'http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&cht=qr&chld='.urlencode('L|0').'&chl='.urlencode($this->_value);
        }else{
            return 'http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&cht=qr&chld=L|0&chl='.$this->_value;
        }
    }
    
    public function getQrCodeImage($size=200){
        
        $local_filename = SM_ROOT_DIR.'Public/Resources/System/Cache/Images/qr_code_'.md5($this->_value).'.png';
        
        if(!is_file($local_filename)){
        
            try{
                SmartestFileSystemHelper::saveRemoteBinaryFile($this->getQrCodeUri(true, $size), $local_filename);
            }catch(SmartestException $e){
                SmartestLog::getInstance('system')->log('Remote PNG file could not be saved: '.$e->getMessage());
                return false;
            }
        
        }
        
        $img = new SmartestImage;
        $img->loadFile($local_filename);
        
        return $img;
        
    }
    
}