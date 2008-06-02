<?php

class SmartestItemPropertyValueHolder extends SmartestItemProperty{
    
    protected $_value = null;
	protected $_contextual_item_id = null;
    
    public function setContextualItemId($id){
	    if(is_numeric($id)){
	        $this->_contextual_item_id = $id;
	    }
	}
    
    public function hydrateValueFromIpvArray($ipv_array){
	    if(is_array($ipv_array)){
	        $ipv = new SmartestItemPropertyValue;
	        $ipv->hydrate($ipv_array);
	        $this->_value = $ipv;
        }
	}
	
	public function getDataType(){
	    return $this->_properties['datatype'];
	}
	
	public function hydrateValueFromIpvObject(SmartestItemPropertyValue $ipv_object){
	    // var_dump($ipv_object);
	    $this->_value = $ipv_object;
	}
	
	public function hasData(){
	    return is_object($this->_value instanceof SmartestItemPropertyValue);
	}
	
	public function getData(){
	    
	    if(!$this->_value instanceof SmartestItemPropertyValue){
	        
	        // echo 'Get Data Again<br />';
	        
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

        	            $this->_value->setPropertyId($this->getId());
        	            // $this->_value->save();
        	            $this->_value->setDraftContent($this->getDefaultValue());
        	        }
	            
                }
	        
	        }else{
	            
	            // Not even sure what item this is - something must be seriously wrong
	            
	        }
	        
	    }
	    
	    return $this->_value;
	    
	}
    
}