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
	
	public function getDefinitionOnPage(SmartestPage $page){
	    
	    if($this->_value){
	        
	        return $this->_value;
	        
	    }else{
	    
    	    if($this->getIsSitewide()){
    	        $sql = "SELECT * FROM PagePropertyValues WHERE PagePropertyValues.pagepropertyvalue_pageproperty_id='{$this->getId()}' AND PagePropertyValues.pagepropertyvalue_site_id='{$page->getSiteId()}'";
    	    }else{
    	        $sql = "SELECT * FROM PagePropertyValues WHERE PagePropertyValues.pagepropertyvalue_pageproperty_id='{$this->getId()}' AND PagePropertyValues.pagepropertyvalue_page_id='{$page->getId()}'";
    	    }
	    
    	    $result = $this->database->queryToArray($sql);
    	    $value = new SmartestPageFieldDefinition;
	    
    	    if(count($result)){
    	        $value->hydrate($result[0]);
    	        $this->_value = $value;
    	        return $this->_value;
    	    }else{
    	        return null;
    	    }
	    
        }
	}
	
	public function getStatusOnPage(SmartestPage $page){
	    
	    $v = $this->getDefinitionOnPage($page);
	    
	    if(is_object($v)){
	        if($v->getLiveValue() && ($v->getLiveValue() == $v->getDraftValue())){
	            return "PUBLISHED";
	        }else{
	            return "DRAFT";
	        }
	    }else{
	        return "UNDEFINED";
	    }
	    
	}
	
}