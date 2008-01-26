<?php

// this class is not supposed to be instantiated directly.

class SmartestAssetIdentifier extends SmartestDataObject{
    
    protected $_ancestor_chain = array();
    protected $_level;
    protected $_loaded = false;
    
	protected function __objectConstruct(){
		
		throw new SmartestException('SmartestAssetIdentifier is not supposed to be instantiated directly. Please use SmartestPlaceholderDefinition or SmartestContainerDefinition.');
		
	}
	
	public function getLevel(){
	    return $this->_level;
	}
	
	protected function setLevel(){
	    
	}
	
	public function isLoaded(){
        return $this->_loaded;
    }

}