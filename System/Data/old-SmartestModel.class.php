<?php

class SmartestModel extends SmartestObject{

	protected $_model_properties = array();

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'itemclass_';
		$this->_table_name = 'ItemClasses';
		
	}
	
	/* public function hydrate($id){
		
		if(is_array($id)){
		    
		    if(array_key_exists('itemclass_id', $id)){
		        foreach($id as $db_field_name => $value){
		            $key = substr($db_field_name, 10);
		            $this->_properties[$key] = $value;
		        }
		    }
		    
		}else{
		
		// determine what kind of identification is being used

		    if($id){
		
    			if(is_numeric($id)){
    				// numeric_id
    				$field = 'itemclass_id';
    			}else if(preg_match('/[a-zA-Z0-9]{32}/', $id)){
    				// 'webid'
    				$field = 'itemclass_webid';
    			}else if(preg_match('/[a-z0-9_-]+/', $id)){
    				// name
    				$field = 'itemclass_varname';
    			}else if(preg_match('/[a-zA-Z0-9\s_-]+/', $id)){
    				// name
    				$field = 'itemclass_name';
    			}
		
    			if($field){
    				$sql = "SELECT * FROM ItemClasses WHERE $field='$id'";
    				$result = $this->database->queryToArray($sql);
    			}
		
    			if(count($result)){
			
    				foreach($result[0] as $name => $value){
    					if (substr($name, 0, 10) == $this->_table_prefix) {
    						$this->_properties[substr($name, 10)] = $value;
    						$this->_properties_lookup[SmartestStringHelper::toCamelCase(substr($name, 10))] = substr($name, 10);
    					}
    				}
			
    				$this->_came_from_database = true;
				
    				$this->buildPropertyMap();
				
    				return true;
    			}else{
    				return false;
    			}
		
    		}else{
    			return false;
    		}
		
	    }
		
	} */
	
	protected function buildPropertyMap(){
		
		$sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->getId()."'";
		$result = $this->database->queryToArray($sql);
		// print_r($this->database);
		// return $result;
	}

}