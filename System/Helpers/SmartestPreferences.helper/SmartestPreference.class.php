<?php

class SmartestPreference{

    protected $_name;
    protected $_application_id;
    protected $_description;
    protected $_user_specific;
    protected $_site_specific;
    
    public function hydrate($array_data){
        
        if(isset($array_data['name'])){
            $this->_name = $array_data['name'];
        }
        
        if(isset($array_data['application_id'])){
            $this->_application_id = $array_data['application_id'];
        }
        
        $this->_user_specific = isset($array_data['user_specific']) ? (bool) $array_data['user_specific'] : true;
        $this->_site_specific = isset($array_data['site_specific']) ? (bool) $array_data['site_specific'] : true;
        
        if(isset($array_data['description'])){
            $this->_application_id = $array_data['description'];
        }
    }
    
    public function getName(){
        return $this->_name;
    }
    
    public function setName($name){
        $this->_name = $name;
    }
    
    public function getApplicationId(){
        return $this->_application_specific;
    }
    
    public function setApplicationId(){
        return $this->_application_specific;
    }
    
    public function isApplicationSpecific(){
        return $this->_application == '_GLOBAL' ? false : true;
    }
    
    public function getDescription(){
        return $this->_application_specific;
    }
    
    public function isUserSpecific(){
        return $this->_user_specific;
    }
    
    public function isSiteSpecific(){
        return $this->_site_specific;
    }

}