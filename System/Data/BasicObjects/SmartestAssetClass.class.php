<?php

// this class is not supposed to be instantiated directly.

class SmartestAssetClass extends SmartestBaseAssetClass{
    
    protected $_type_info;
    
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'assetclass_';
		$this->_table_name = 'AssetClasses';
		
	}
	
	public function exists($name, $site_id){
	    
	    $sql = "SELECT * FROM AssetClasses WHERE assetclass_name='".$name."' AND assetclass_site_id='".$site_id."'";
	    $query_result = $this->database->queryToArray($sql);
	    
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
	
	public function getInfoField($field_name){
	    
	    $field_name = SmartestStringHelper::toVarName($field_name);
	    
	    $data = $this->getInfo();
	    
	    if(isset($data[$field_name])){
	        return $data[$field_name];
	    }else{
	        return null;
	    }
	}
	
	public function setInfoField($field_name, $data){
	    
	    $field_name = SmartestStringHelper::toVarName($field_name);
	    
	    $existing_data = $this->getInfo();
	    
	    $existing_data[$field_name] = $data;
	    
	    $this->setInfo($existing_data);
	    
	}
	
	public function getInfo(){
	    
	    return @unserialize($this->_getInfo());
	    
	}
	
	public function setInfo($data){
	    
	    if(!is_array($data)){
	        $data = array($data);
	    }
	    
	    $this->_setInfo(serialize($data));
	    
	}
	
	protected function _getInfo(){
	    return $this->_properties['info'];
	}
	
	protected function _setInfo($serialized_data){
	    $this->setField('info', $serialized_data);
	}

}