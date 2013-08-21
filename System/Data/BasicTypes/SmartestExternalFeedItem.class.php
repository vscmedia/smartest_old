<?php

class SmartestExternalFeedItem extends SimplePie_Item implements SmartestGenericListedObject, SmartestBasicType, ArrayAccess, SmartestStorableValue{
    
    public $bf; // Backup feed container
    
    public function setValue($v){
        
    }
    
    public function getValue(){
        
    }
    
    public function get_base($element = array()){
		if(is_object($this->feed)){
		    return $this->feed->get_base($element);
		}else if(is_object($this->bf)){
		    return $this->bf->get_base($element);
		}
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
        return $this->get_date('U');
    }
    
    public function getDateWithFormat($format){
        return $this->get_date($format);
    }
    
    public function getLinkContents(){
        
    }
    
    public function getDescription(){
        return $this->get_description();
    }
    
    public function offsetGet($offset){
        switch($offset){
            
            case "name":
            case "title":
            case "headline":
            case "label":
            //print_r($this);
            //break;
            return new SmartestString($this->get_title());
            
            case "url":
            return new SmartestExternalUrl($this->get_permalink());
            break;
            
            case "slug":
            return $this->getSlug();
            break;
            
            case "date":
            return new SmartestDateTime($this->getDate('U'));
            
            case "description":
            return new SmartestString($this->get_description());
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
    
    public function __destruct(){}
    
}