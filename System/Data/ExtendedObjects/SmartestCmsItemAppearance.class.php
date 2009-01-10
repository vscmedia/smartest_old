<?php

class SmartestCmsItemAppearance extends SmartestManyToManyLookup{
    
    protected $_item;
    protected $_page;
    
    protected function __objectConstruct(){
		
		$this->setField('type', 'SM_MTMLOOKUP_PAGE_ITEM_APPS');
		
	}
	
	public function setItemId($id){
	    
	    $this->setField('entity_2_foreignkey', (int) $id);
	    
	}
	
	public function setPageId($id){
	    
	    $this->setField('entity_1_foreignkey', (int) $id);
	    
	}
	
	public function getItemId(){
	    
	    return $this->getField('entity_2_foreignkey');
	    
	}
	
	public function getPageId(){
	    
	    return $this->getField('entity_1_foreignkey');
	    
	}
    
}