<?php

class SmartestDataSetAppearance extends SmartestManyToManyLookup{
    
    protected $_set;
    protected $_page;
    
    protected function __objectConstruct(){
		
		$this->setField('type', 'SM_MTMLOOKUP_PAGE_SET_APPS');
		
	}
	
	public function setDataSetId($id){
	    
	    $this->setField('entity_2_foreignkey', (int) $id);
	    
	}
	
	public function setPageId($id){
	    
	    $this->setField('entity_1_foreignkey', (int) $id);
	    
	}
	
	public function getDataSetId(){
	    
	    return $this->getField('entity_2_foreignkey');
	    
	}
	
	public function getPageId(){
	    
	    return $this->getField('entity_1_foreignkey');
	    
	}
    
}