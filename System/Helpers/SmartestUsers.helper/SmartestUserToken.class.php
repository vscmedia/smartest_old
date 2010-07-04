<?php

class SmartestUserToken_New implements ArrayAccess{
    
    protected $_id;
    protected $_code;
    protected $_type;
    protected $_scope;
    protected $_category;
    protected $_description;
    
    const PERMISSION = 32;
    const SETTING = 64;
    
    const GLOBAL_SCOPE = 32;
    const SITE_SCOPE = 64;
    
    public function __construct($data){
        
        $this->_id = $data['id'];
        $this->_code = $data['code'];
        $this->_type = $data['type'];
        $this->_scope = $data['scope'];
        $this->_category = $data['category'];
        $this->_description = $data['description'];
        
    }
    
    public function __toString(){
        return $this->_code;
    }
    
    public function getId(){
        return $this->_id;
    }
    
    public function getCode(){
        return $this->_code;
    }
    
    public function getType(){
        switch($this->_type){
            case "p":
            return self::PERMISSION;
            case "s":
            return self::SETTING;
        }
    }
    
    public function getScope(){
        switch($this->_scope){
            case "g":
            return self::GLOBAL_SCOPE;
            case "s":
            return self::SITE_SCOPE;
        }
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "id":
            case "token_id":
            return $this->_id;
            
            case "code":
            case "token_code":
            return $this->_code;
            
            case "type":
            case "token_type":
            return $this->getType();
            
            case "scope":
            case "token_scope":
            return $this->getScope();
            
            case "description":
            case "desc":
            case "token_description":
            return $this->_description;
            
        }
        
    }
    
    // Needed only for interface conformity:
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}

}