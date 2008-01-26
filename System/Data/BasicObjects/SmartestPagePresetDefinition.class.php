<?php

class SmartestPagePresetDefinition extends SmartestDataObject{
    
    const CONTAINER = 'SM_PAGE_PRESET_DEF_CONTAINER';
	const PLACEHOLDER = 'SM_PAGE_PRESET_DEF_PLACEHOLDER';
	const FIELD = 'SM_PAGE_PRESET_DEF_FIELD';
    
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'plpd_';
		$this->_table_name = 'PageLayoutPresetDefinitions';
		
	}
	
	public function applyToPage($page){
	    
	    switch($this->getElementType()){
	        
	        case self::CONTAINER:
	        $definition = new SmartestContainerDefinition;
	        $definition->setPageId($page->getId());
	        $definition->setAssetclassId($this->getElementId());
	        $definition->setDraftAssetId($this->getElementValue());
	        $definition->save();
	        break;
	        
	        case self::PLACEHOLDER:
	        $definition = new SmartestPlaceholderDefinition;
	        $definition->setPageId($page->getId());
	        $definition->setAssetclassId($this->getElementId());
	        $definition->setDraftAssetId($this->getElementValue());
	        $definition->save();
	        break;
	        
	        case self::FIELD:
	        $definition = new SmartestPageFieldDefinition;
	        $definition->setPageId($page->getId());
	        $definition->setPagepropertyId($this->getElementId());
	        $definition->setDraftValue($this->getElementValue());
	        $definition->save();
	        break;
	        
	    }
	    
	}
	
}