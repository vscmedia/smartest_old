<?php

class SmartestTwitterAccountName extends SmartestString{
    
    public function setValue($v){
        
        if($v{0} == '@'){
            $this->_string = substr($v, 1);
        }else{
            $this->_string = (string) $v;
        }
        
    }
    
    public function getUrl($secure=false){
        
        $p = $secure ? 'https' : 'http';
        return new SmartestExternalUrl($p.'://twitter.com/'.$this->_string);
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "url":
            return $this->getUrl();
            case "secure_url":
            return $this->getUrl(true);
            case "link":
            $p = new SmartestParameterHolder('Twitter account link parameters: @'.$this->_string);
            $p->setParameter('with', '@'.$this->_string);
            return SmartestCmsLinkHelper::createLink($this->getUrl(), $p)->render();
            case "secure_link":
            $p = new SmartestParameterHolder('Twitter account secure link parameters: @'.$this->_string);
            $p->setParameter('with', '@'.$this->_string);
            return SmartestCmsLinkHelper::createLink($this->getUrl(true), $p)->render();
            case "empty":
            return !strlen($this->_string);
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function __toString(){
        return (string) $this->_string;
    }
  
}