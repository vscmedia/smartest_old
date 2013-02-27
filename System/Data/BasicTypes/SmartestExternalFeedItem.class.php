<?php

class SmartestExternalFeedItem extends SimplePie_Item implements SmartestGenericListedObject, SmartestBasicType, ArrayAccess, SmartestStorableValue{
    
    public function setValue($v){
        
    }
    
    public function getValue(){
        
    }
    
    public function __toString(){
        return ''.$this->getTitle();
    }
    
    public function isPresent(){
        
    }
    
    public function getId(){
        return parent::__toString();
    }
    
    public function getWebId(){
        return parent::__toString();
    }
    
    public function getSlug(){
        return SmartestStringHelper::toSlug($this->getTitle());
    }
    
    public function getTitle(){
        return $this->get_title();
    }
    
    public function getDate(){
        
    }
    
    public function getLinkContents(){
        
    }
    
    public function getDescription(){
        
    }
    
    public function offsetGet($offset){
        switch($offset){
            
            case "name":
            case "title":
            case "headline":
            case "label":
            //print_r($this);
            //break;
            return $this->getTitle();
            
            case "url":
            break;
            
            case "description":
            break;
        }
    }
    
    public function offsetExists($offset){
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    
    public function getStorableFormat(){
        
    }
    
    public function hydrateFromStorableFormat($v){
        
    }
    
}