<?php

class SmartestAssetType extends SmartestBaseAssetType{

	protected $_category;
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'assettype_';
		$this->_table_name = 'AssetTypes';
		
	}
	
	public function getCategory(){
	    
	    if(!is_object($this->_category)){
	        // calculate category
	        $cat = new SmartestAssetTypeCategory;
	        $cat->hydrate($this->getCatId());
	        $this->_category = $cat;
	    }
	    
	    return $this->_category;
	}

}