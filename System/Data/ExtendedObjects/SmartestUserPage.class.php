<?php

class SmartestUserPage extends SmartestPage{
    
    protected $_user;
    
    public function assignUser(SmartestUser $u){
        $this->_user = $u;
    }
    
    public function getUser(){
        return $this->_user;
    }
    
    public function getTitle($force_static=false){
        if(is_object($this->_user) && !$force_static){
            return $this->_user->getFullName();
        }else{
            return $this->_properties['title'];
        }
    }
    
    public function getFormattedTitle(){
        $separator = $this->getParentSite()->getTitleFormatSeparator();
        return $this->getParentSite()->getName().' '.$separator.' Author '.$separator.' '.$this->_user->getFullName();
    }
    
    public function getDefaultUrl(){
        return 'author/'.$this->_user->getUsername();
    }
    
    public function fetchRenderingData(){
        
        $data = parent::fetchRenderingData();
        $data->setParameter('user', $this->_user);
        return $data;
        
    }
    
    public function offsetGet($offset){
        
        return parent::offsetGet($offset);
        
    }
    
}