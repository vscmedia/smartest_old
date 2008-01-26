<?php

class SmartestAssetTypeCategory extends SmartestDataObject{

    protected $types;

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'assettypecat_';
		$this->_table_name = 'AssetTypeCategories';
		
	}
	
	public function getTypes(){
	    
	    $types = array();
	    $sql = "SELECT * FROM AssetTypes WHERE assettype_cat_id='".$this->getId()."'";
	    $results = $this->database->queryToArray($sql);
	    
	    foreach($results as $type){
	        $newType = new SmartestAssetType;
	        $newType->hydrate($type);
	        $types[] = $newType;
	    }
	    
	    $this->types = $types;
	    
	    return $this->types;
	    
	}

}