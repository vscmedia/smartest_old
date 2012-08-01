<?php

class SmartestItemPropertyValueHolder extends SmartestItemProperty{
    
    protected $_value = null;
	protected $_contextual_item_id = null;
	protected $_item;
    
    public function setContextualItemId($id){
	    if(is_numeric($id)){
	        $this->_contextual_item_id = $id;
	    }
	}
	
	public function setItem(SmartestCmsItem $item){
	    $this->_item = $item;
	    $this->setContextualItemId($item->getId());
	}
    
    public function hydrateValueFromIpvArray($ipv_array, SmartestCmsItem $item){
	    if(is_array($ipv_array)){
	        $ipv = new SmartestItemPropertyValue;
	        $ipv->hydrate($ipv_array);
	        $this->_value = $ipv;
	        $this->_item = $item;
        }
	}
	
	public function getDataType(){
	    return $this->_properties['datatype'];
	}
	
	public function hydrateValueFromIpvObject(SmartestItemPropertyValue $ipv_object){
	    $this->_value = $ipv_object;
	}
	
	public function hasData(){
	    return ($this->_value instanceof SmartestItemPropertyValue);
	}
	
	public function delete(){
	    throw new SmartestException('Cannot delete temporary SmartestItemPropertyValueHolder object. Deletion of properties should use SmartestItemProperty, values should use SmartestItemPropertyValueHolder.');
	}
	
	public function save(){
	    throw new SmartestException('Cannot save temporary SmartestItemPropertyValueHolder object. Modification or insertion of properties should use SmartestItemProperty, values should use SmartestItemPropertyValueHolder.');
	}
	
	// returns a SmartestItemPropertyValue object
	public function getData(){
	    
	    if(!$this->_value instanceof SmartestItemPropertyValue){
	        
	        $this->_value = new SmartestItemPropertyValue;
	        
	        if($this->_contextual_item_id){
	            
	            // try to find value
	            $sql = "SELECT * FROM ItemPropertyValues WHERE itempropertyvalue_property_id='".$this->getId()."' AND itempropertyvalue_item_id='".$this->_contextual_item_id."'";
	            $result = $this->database->queryToArray($sql);
	            
	            if(count($result)){
	                
	                // hydrate value from array
	                $this->_value->hydrate($result[0]);
	                
	            }else{
	        
	                $this->_value->setItemId($this->_contextual_item_id);
	                
	                if($this->getId()){
                        
                        // Creates a value object when none is found
        	            $this->_value->setPropertyId($this->getId());
        	            
        	            if(!$this->isManyToMany() && $this->getDefaultValue()){
        	                $this->_value->setDraftContent($this->getDefaultValue());
    	                }
    	                
    	                $this->_value->save();
        	            
        	        }
	            
                }
	        
	        }else{
	            
	            // Not even sure what item this is - something must be seriously wrong
	            SmartestLog::getInstance('system')->log('SmartestItemProprtyValueHolder->getData() called without a contextual item ID.');
	            
	        }
	        
	    }
	    
	    return $this->_value;
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        case "ipv_object":
	        return $this->_value;
	        case "value":
	        return $this->_value->getContent();
	        case "info":
	        return $this->_value->getInfo();
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
	public function replaceItemPropertyValueWith(SmartestItemPropertyValue $v){
	    $this->_value = $v;
	}
	
	// Todo: if a file has been removed from the group that is used to get possible values for a property, add it back to the list
	/* public function getPossibleValues(){
	    $v = parent::getPossibleValues();
	    return $v;
	} */
    
}