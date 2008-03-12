<?php

// this class is not supposed to be instantiated directly.

class SmartestAssetClass extends SmartestDataObject{
    
    protected $_type_info;
    
	protected function __objectConstruct(){
		
		throw new SmartestException('SmartestAssetClass is not supposed to be instantiated directly. Please use SmartestPlaceholder or SmartestContainer.');
		
	}
	
	public function exists($name, $site_id){
	    
	    $sql = "SELECT * FROM AssetClasses WHERE assetclass_name='".$name."' AND assetclass_site_id='".$site_id."'";
	    $query_result = $this->database->queryToArray($sql);
	    
	    // print_r($query_result);
	    // echo count($result);
	    
	    if(count($result) > 0){
	        $this->hydrate($result[0]);
	        return true;
	    }else{
	        return false;
        }
	}
	
	public function getTypeInfo(){
	    
	    if(!$this->_type_info){
	        $types = SmartestDataUtility::getAssetClassTypes();
	        $this->_type_info = $types[$this->getType()];
        }
        
        // var_dump($this->getType());
        
        return $this->_type_info;
	}

}