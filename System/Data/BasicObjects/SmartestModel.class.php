<?php

class SmartestModel extends SmartestDataObject{

	protected $_model_properties = array();

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'itemclass_';
		$this->_table_name = 'ItemClasses';
		
	}
	
	/* public function hydrate($id){
		
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
		
	} */
	
	public function hydrate($id){
	    $bool = parent::hydrate($id);
	    $this->buildPropertyMap();
	    return $bool;
	}
	
	protected function buildPropertyMap(){
		
		// $this->_model_properties = array();
		
		$sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->getId()."'";
		$result = $this->database->queryToArray($sql);
		
		// $properties = array();
		
		foreach($result as $db_property){
			$property = new SmartestItemProperty;
			$property->hydrate($db_property);
			$this->_model_properties[] = $property;
		}
		
		// print_r($properties);
		
		// return $result;
	}
	
	public function getProperties(){
		return $this->_model_properties;
	}
	
	public function __toArray(){
	    $data = parent::__toArray();
	    $data['default_metapage_id'] = $this->getDefaultMetaPageId($this->getCurrentSiteId());
	    return $data;
	}
	
    public function __toArrayLikeCmsItemArray(){
	    
	    $array = array();
	    $array['_model'] = $this->__toArray();
	    $array['_properties'] = array();
	    
	    foreach($this->_model_properties as $p){
	        
	        if($p->getDatatype() == "SM_DATATYPE_ASSET"){
	            // print_r($p);
            }
	        
	        if(is_object($p)){
	            $array['_properties'][$p->getId()] = $p->__toArray();
	            $array['_properties'][$p->getId()]['_options'] = $p->getPossibleValuesAsArrays();
	            // print_r($array['_properties'][$p->getId()]);
	            $array[$p->getId()] = '';
	        }
	    }
	    
	    return $array;
	    
	}
	
	public function getPropertiesAsArrays(){
		
		$propertyarrays = array();
		
		// echo 'called';
		
		foreach($this->getProperties() as $property){
		    $propertyarrays[] = $property->__toArray();
		}
		
		return $propertyarrays;
	}
	
	public function getPropertyNames(){
		
		$propertynames = array();
		
		foreach($this->_model_properties as $mp){
			$propertynames[] = $mp->getName();
		}
		
		return $propertynames;
	}
	
	public function getPropertyVarNames(){
		
		$propertynames = array();
		
		foreach($this->_model_properties as $mp){
			$propertynames[] = $mp->getVarName();
		}
		
		return $propertynames;
	}
	
	public function getPropertyVarNamesLookup(){
	    
	    $properties = array();
		
		foreach($this->_model_properties as $mp){
			$properties[$mp->getVarName()] = $mp->getId();
		}
		
		return $properties;
	}
    
    public function getClassName(){
	    return SmartestStringHelper::toCamelCase($this->getName());
    }
    
    public function getSimpleItems($site_id=''){
        
        $sql = "SELECT * FROM Items WHERE item_itemclass_id='".$this->getId()."' AND item_deleted != 1";
        
        if(is_numeric($site_id)){
            $sql .= " AND item_site_id='".$site_id."'";
        }
        
        $sql .= " ORDER BY item_name";
        
        // echo $sql;
        
        $result = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($result as $db_array){
            $item = new SmartestItem;
            $item->hydrate($db_array);
            $items[] = $item;
        }
        
        return $items;
    }
    
    public function getSimpleItemsAsArrays($site_id=''){
        
        $items = $this->getSimpleItems($site_id);
        $arrays = array();
        
        foreach($items as $item){
            $arrays[] = $item->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getItemIds($site_id=''){
        
        $sql = "SELECT item_id FROM Items WHERE item_itemclass_id='".$this->getId()."' AND item_deleted != 1";
        
        if(is_numeric($site_id)){
            $sql .= " AND item_site_id='".$site_id."'";
        }
        
        $sql .= " ORDER BY item_name";
        
        $result = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($result as $db_array){
            $items[] = $db_array['item_id'];
        }
        
        return $items;
        
    }
    
    public function getMetaPages(){
        
        $sql = "SELECT * FROM Pages WHERE page_type='ITEMCLASS' AND page_dataset_id='".$this->getId()."' and page_deleted != 1";
        
        if(is_object($this->getSite())){
            $sql .= " AND page_site_id='".$this->getSite()->getId()."'";
        }
        
        $result = $this->database->queryToArray($sql);
        $pages = array();
        
        foreach($result as $record){
            $p = new SmartestPage;
            $p->hydrate($record);
            $pages[] = $p;
        }
        
        return $pages;
        
    }
    
    public function getMetaPagesAsArrays(){
        
        $pages = $this->getMetaPages();
        $arrays = array();
        
        foreach($pages as $p){
            $arrays[] = $p->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function hasDefaultDescriptionPropertyId(){
        return is_numeric($this->getDefaultDescriptionPropertyId());
    }
    
    public function getAvailableDescriptionProperties(){
        
        $properties = $this->getProperties();
        $ok_properties = array();
        $asset_types = SmartestDataUtility::getAssetTypes();
        
        $text_asset_types = array();
        
        foreach($asset_types as $a){
            if($a['storage']['type'] == "database"){
                $text_asset_types[] = $a['id'];
            }
        }
        
        foreach($properties as $p){
            
            $info = $p->getTypeInfo();
            
            /* echo $p->getForeignKeyFilter();
            echo $p->getDatatype();
            print_r($text_asset_types); */
            
            if((isset($info['long']) && strtolower($info['long']) != 'false') || ($p->getDatatype() == 'SM_DATATYPE_ASSET' && in_array($p->getForeignKeyFilter(), $text_asset_types))){
                $ok_properties[] = $p;
            }
        }
        
        // print_r($ok_properties);
        
        return $ok_properties;
    }
    
    public function getDefaultMetaPageId($site_id){
        return SmartestSystemSettingHelper::load('model_'.$this->getId().'_default_metapage_site_'.$site_id);
    }
    
    public function setDefaultMetaPageId($site_id, $id){
        return SmartestSystemSettingHelper::save('model_'.$this->getId().'_default_metapage_site_'.$site_id, $id);
    }
    
    public function getAvailableDescriptionPropertiesAsArrays(){
        
        $properties = $this->getAvailableDescriptionProperties();
        $arrays = array();
        
        foreach($properties as $p){
            $arrays[] = $p->__toArray();
        }
        
        return $arrays;
        
    }
    
}
