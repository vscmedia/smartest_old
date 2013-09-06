<?php

class SmartestPageFieldDefinition extends SmartestBasePageFieldDefinition{
	
	protected $_page;
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'pagepropertyvalue_';
		$this->_table_name = 'PagePropertyValues';
		
	}
	
	public function loadForUpdate($field, $page, $draft=false){
	    
	    if(is_object($field) && is_object($page)){
            
            $this->_page = $page;
            
            if($field->getIsSitewide()){
                
                $sql = "SELECT * FROM PagePropertyValues WHERE pagepropertyvalue_site_id='".$this->_page->getSiteId()."' AND pagepropertyvalue_pageproperty_id='".$field->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    $this->hydrate($result[0]);
                }
                
            }else{
            
                $sql = "SELECT * FROM PagePropertyValues WHERE pagepropertyvalue_page_id='".$this->_page->getId()."' AND pagepropertyvalue_pageproperty_id='".$field->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    $this->hydrate($result[0]);
                }
            }
        }
	}
	
	public function getPage(){
	    
	    return $this->_page;
	    
	}
	
}
