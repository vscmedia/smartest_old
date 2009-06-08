<?php

class SmartestItemProperty extends SmartestBaseItemProperty{
	
	protected $_type_info;
	protected $_possible_values = array();
	protected $_possible_values_retrieval_attempted = false;
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'itemproperty_';
		$this->_table_name = 'ItemProperties';
		$this->addPropertyAlias('VarName', 'varname');
		
	}
	
	public function hydrate($value){
	    
	    $result = parent::hydrate($value);
	    
	    if($result){
	        $this->getTypeInfo();
	    }
	    
	    return $result;
	    
	}

	public function getTypeInfo(){
	    
	    $datatypes = SmartestDataUtility::getDataTypes();
	    
	    if(!$this->_type_info){
	    
	        if(array_key_exists($this->getDatatype(), $datatypes)){
	            $this->_type_info = $datatypes[$this->getDatatype()];
            }
        
        }
        
        return $this->_type_info;
	    
	}
	
	public function isForeignKey(){
	    
	    $info = $this->getTypeInfo();
	    
	    // print_r($info);
	    
	    if($info['valuetype'] == 'foreignkey'){
	        return true;
	    }else{
	        return false;
	    }
	    
	}
	
	public function getPossibleValues(){
	    
	    if($this->_possible_values_retrieval_attempted){
	    
	        return $this->_possible_values;
	    
        }else{
            
            if($this->isForeignKey()){
	            
	            $info = $this->getTypeInfo();
	            $filter = $this->getForeignKeyFilter();
	            
	            if($info['filter']['entitysource']['type'] == 'db'){
	                
	                if($this->getDatatype() == 'SM_DATATYPE_ASSET' && substr($filter, 0, 13) == 'SM_ASSETCLASS'){
	                    
	                    if(is_object($this->getSite())){
    	                    $site_id = $this->getSite()->getId();
    	                }
	                    
	                    $p = new SmartestPlaceholder;
	                    $p->setType($filter);
	                    $this->_possible_values = $p->getPossibleAssets();
	                    // print_r($this->_possible_values);
	                    
	                }else{
	                
    	                if(is_object($this->getSite())){
    	                    $site_id = $this->getSite()->getId();
    	                }
	                
    	                if(isset($info['filter']['entitysource']['class']) && class_exists($info['filter']['entitysource']['class'])){
	                
    	                    $sql = "SELECT * FROM ".$info['filter']['entitysource']['table']." WHERE ".$info['filter']['entitysource']['matchfield']." ='".$filter."'";
	                    
    	                    if($site_id && $info['filter']['entitysource']['sitefield'] && $info['filter']['entitysource']['sharedfield']){
    	                        $sql .= " AND (".$info['filter']['entitysource']['sitefield']."='".$site_id."' OR ".$info['filter']['entitysource']['sharedfield']."='1')";
    	                    }
    	                    
    	                    if(isset($info['filter']['condition'])){
	                            
	                            foreach($info['filter']['condition'] as $condition){
	                                $sql .= ' AND '.$this->convertFieldCondition($condition);
	                            }
	                            
                            }
	                    
    	                    if(isset($info['filter']['entitysource']['sortfield'])){
    	                        $sql .= " ORDER BY ".$info['filter']['entitysource']['sortfield'];
    	                    }
                            
                            $result = $this->database->queryToArray($sql);
    	                    $options = array();
	                    
    	                    foreach($result as $raw_array){
	                        
    	                        $class = $info['filter']['entitysource']['class'];
    	                        $option = new $class;
    	                        $option->hydrate($raw_array);
    	                        $options[] = $option;
                        
    	                    }
	                    
    	                    $this->_possible_values = $options;
	                
    	                }else{
	                        
    	                    throw new SmartestException("Foreign key data object class '".$info['filter']['entitysource']['class']."' not defined or doesn't exist for property datatype: ".$this->getDatatype());
	                        
    	                }
	                
    	            }
	            
	            }else{
	                
    	            // non-database entity types? to be continued...
    	            $this->_possible_values = array();
	                
    	        }
	            
	            $this->_possible_values_retrieval_attempted = true;
	            return $this->_possible_values;
	            
	        }else{
	            $this->_possible_values_retrieval_attempted = true;
	            return array();
	        }
            
        }
	    
	}
	
	public function getPossibleValuesAsArrays(){
	    
	    $arrays = array();
	    $pv = $this->getPossibleValues();
	    
	    foreach($pv as $pvo){
	        $arrays[] = $pvo->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function convertFieldCondition($condition){
	    
	    $sql = $condition['field'];
	    
	    if(!isset($condition['operator'])){$condition['operator'] = 'EQUAL';}
	    
	    switch($condition['operator']){
	        case "NOT_EQUAL":
	        $sql .= ' != ';
	        break;
	        
	        case "NOT_LIKE":
	        $sql .= ' NOT LIKE ';
	        break;
	        
	        case "LIKE":
	        $sql .= ' LIKE ';
	        break;
	        
	        default:
	        $sql .= ' = ';
	        break;
	        
	    }
	    
	    $sql .= "'".$condition['value']."'";
	    
	    return $sql;
	    
	}
	
}