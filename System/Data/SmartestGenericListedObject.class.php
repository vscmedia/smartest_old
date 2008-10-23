<?php

// a class for tag contents, search resuts, clipboard, and any other time where mixed content is listed.

class SmartestGenericListedObject implements ArrayAccess{
    
    protected $_internal_object;
    protected $_type;
    protected $_properties = array();
    
    const USER = 0; // not yet in use
    const PAGE = 1;
    const ITEM = 2;
    const ASSET = 4;
    const TODO = 8;
    
    public function __construct($object){
        
        if(is_object($object)){
            
            if(is_subclass_of($object, 'SmartestCmsItem')){
                
                $this->_internal_object = $object;
                $this->_type = self::ITEM;
                
                $this->_properties['url'] = $this->_internal_object->getUrl();
                $this->_properties['title'] = $this->_internal_object->getItem()->getName();
                
                if($this->_internal_object->getIsPublished()){
                    $this->_properties['date'] = $this->_internal_object->getItem()->getLastPublished();
                }else{
                    $this->_properties['date'] = $this->_internal_object->getItem()->getCreated();
                }
                
                if($this->_internal_object->getDescriptionField()){
                    $this->_properties['description'] = SmartestStringHelper::toSummary($this->_internal_object->getDescriptionFieldContents());
                }
                
                $this->_properties['type'] = $this->_internal_object->getModel()->getName();
                
                $this->_properties['id'] = $this->_internal_object->getId();
                $this->_properties['webid'] = $this->_internal_object->getWebid();
            
            }else if($object instanceof SmartestItem){
            
                 $this->_internal_object = $object;
                 $this->_type = self::ITEM;

                 $this->_properties['url'] = $this->_internal_object->getUrl();
                 $this->_properties['title'] = $this->_internal_object->getName();

                 if($this->_internal_object->getIsPublished()){
                     $this->_properties['date'] = $this->_internal_object->getLastPublished();
                 }else{
                     $this->_properties['date'] = $this->_internal_object->getCreated();
                 }
                 
                 if($this->_internal_object->getDescriptionField()){
                     $this->_properties['description'] = SmartestStringHelper::toSummary($this->_internal_object->getDescriptionFieldContents());
                 }

                 $this->_properties['type'] = $this->_internal_object->getModel()->getName();
                 
                 $this->_properties['id'] = $this->_internal_object->getId();
                 $this->_properties['webid'] = $this->_internal_object->getWebid();
            
            }else if($object instanceof SmartestPage || is_subclass_of($object, 'SmartestPage')){
                
                $this->_internal_object = $object;
                $this->_type = self::PAGE;
                $this->_properties['title'] = $this->_internal_object->getTitle();
                
                if($this->_internal_object->getIsPublished()){
                    $this->_properties['date'] = $this->_internal_object->getLastPublished();
                }else{
                    $this->_properties['date'] = $this->_internal_object->getCreated();
                }
                
                $this->_properties['url'] = SM_CONTROLLER_DOMAIN.$this->_internal_object->getDefaultUrl();
                
                $this->_properties['description'] = $this->_internal_object->getDescription();
                
                $this->_properties['type'] = 'Page';
                
                $this->_properties['id'] = $this->_internal_object->getId();
                $this->_properties['webid'] = $this->_internal_object->getWebid();
                
            }else{
                throw new SmartestException("Supplied data must be an object that is either a SmartestPage or a subclass of either SmartestPage or SmartestCmsItem", SM_ERROR_SMARTEST_INTERNAL);
            }
            
        }else{
            throw new SmartestException("Supplied data must be an object in SmartestGenericListedObject", SM_ERROR_SMARTEST_INTERNAL);
        }
    }
    
    public function __toString(){
        return $this->_internal_object->__toString();
    }
    
    public function getTitle(){
        return $this->_properties['title'];
    }
    
    public function getDescription(){
        return $this->_properties['description'];
    }
    
    public function getDate(){
        return $this->_properties['date'];
    }
    
    public function getUrl(){
        return $this->_properties['url'];
    }
    
    public function __toArray(){
        
        $data = array();
        $data = $this->_properties;
        $data['object'] = $this->_internal_object->__toArray();
        return $data;
        
    }
    
    public function offsetExists($offset){
        return (isset($this->_properties[$offset]) || isset($this->_internal_object[$offset]));
    }
    
    public function offsetGet($offset){
        if(isset($this->_properties[$offset])){
            return $this->_properties[$offset];
        }else{
            return $this->_internal_object[$offset];
        }
    }
    
    public function offsetSet($o, $v){
        // read only
    }
    
    public function offsetUnset($o){
        // read only
    }
    
}