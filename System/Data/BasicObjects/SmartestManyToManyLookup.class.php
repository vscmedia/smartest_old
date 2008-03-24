<?php

class SmartestManyToManyLookup extends SmartestDataObject{

    protected function __objectConstruct(){
		
		$this->setTablePrefix('mtmlookup_');
		$this->setTableName('ManyToManyLookups');
		
	}
	
	public function getEntityForeignKeyValue($entity_num){
	    if(ceil($entity_num) > 0 && ceil($entity_num) < 5){
	        $field = 'entity_'.$entity_num.'_foreignkey';
	        return $this->_properties[$field];
	    }
	}
	
	public function setEntityForeignKeyValue($entity_num, $value){
	    if(ceil($entity_num) > 0 && ceil($entity_num) < 5){
	        $field = 'entity_'.$entity_num.'_foreignkey';
	        $this->_properties[$field] = $value;
			$this->_modified_properties[$field] = $value;
	    }
	}
	
	public function setContextDataField($field, $new_data){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    $data = $this->getContextData();
	    $data[$field] = $new_data;
	    // echo $field.'=>'.var_export($new_data, true).'<br />';
	    // var_dump($data);
	    $this->setContextData($data);
	    
	}
	
	public function getContextDataField($field){
	    
	    $data = $this->getContextData();
	    
	    $field = SmartestStringHelper::toVarName($field);
	    
	    if(isset($data[$field])){
	        return $data[$field];
	    }else{
	        return null;
	    }
	}
	
	public function getContextData(){
	    
	    if($data = @unserialize($this->_getContextData())){
	        
	        if(is_array($data)){
	            return $data;
            }else{
                return array($data);
            }
	    }else{
	        return array();
	    }
	}
	
	public function setContextData($data){
	    
	    if(!is_array($data)){
	        $data = array($data);
	    }
	    
	    $this->_setContextData(serialize($data));
	    
	    // echo $this->_modified_properties['context_data'];
	    
	}
	
	protected function _getContextData(){
	    return $this->_properties['context_data'];
	}
	
	protected function _setContextData($serialized_data){
	    $this->_properties['context_data'] = $serialized_data;
		$this->_modified_properties['context_data'] = $serialized_data;
	}
   
}