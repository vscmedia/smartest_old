<?php

class SmartestPageField extends SmartestBasePageField{
	
	
	protected $_contextual_page_id = null;
	protected $_value = null;
	
	public function setContextualPageId($id){
	    if(is_numeric($id)){
	        $this->_contextual_page_id = $id;
	    }
	}
	
	public function hydrateValueFromPpdArray($ppd_array){
	    if(is_array($ppd_array)){
	        $ppd = new SmartestPageFieldDefinition;
	        $ppd->hydrate($ppd_array);
	        $this->_value = $ppd;
        }
	}
	
	public function hydrateValueFromPpdObject($ppd_object){
	    if($ppd_object instanceof SmartestPageFieldDefinition){
	        $this->_value = $ppd_object;
        }
	}
	
	public function loadValues(){
	    if($this->_contextual_page_id){
	        
	    }
	}
	
	public function __toArray($include_value=false, $draft_mode=false){
	    
	    $data = parent::__toArray();
	    
	    if($include_value){
	        $data['value'] = $this->getData()->__toArray();
	    }
	    
	    return $data;
	    
	}
	
	public function getData(){
	    
        if(!$this->_value instanceof SmartestPageFieldDefinition){
	        
	        $this->_value = new SmartestPageFieldDefinition;
	        
	        if($this->_contextual_page_id){
	            $this->_value->setPageId($this->_contextual_page_id);
	        }
	        
	        if($this->getId()){
	            
	            $this->_value->setPagepropertyId($this->getId());
	            
	        }
	        
	    }
	    
	    return $this->_value;
	    
	}
	
	public function getDefinitions(){
	    
	}
	
	public function getDefinitionsAsArrays(){
	    $definitions = $this->getDefinitions();
	    $arrays = array();
	}
	
	public function clearAllDefinitions(){
	    $query = "DELETE FROM PagePropertyValues WHERE pagepropertyvalue_pageproperty_id='".$this->getId()."'";
		$this->database->rawQuery($query);
	}
	
}