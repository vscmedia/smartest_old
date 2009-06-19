<?php

class SmartestModel extends SmartestBaseModel{

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
		
		if(SmartestCache::hasData('model_properties_'.$this->_properties['id'], true)){
		    $result = SmartestCache::load('model_properties_'.$this->_properties['id'], true);
	    }else{
		    $sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_properties['id']."'";
		    $result = $this->database->queryToArray($sql);
		    SmartestCache::save('model_properties_'.$this->_properties['id'], $result, -1, true);
		    // print_r(SmartestCache::load('model_properties_'.$this->_properties['id'], true));
	    }
		
		foreach($result as $db_property){
			$property = new SmartestItemProperty;
			$property->hydrate($db_property);
			$this->_model_properties[] = $property;
		}
		
	}
	
	public function getProperties(){
		return $this->_model_properties;
	}
	
	public function refresh(){
	    SmartestCache::clear('model_properties_'.$this->_properties['id'], true);
        SmartestObjectModelHelper::buildAutoClassFile($this->_properties['id'], SmartestStringHelper::toCamelCase($this->getName()));
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "sets":
	        case "datasets":
	        return $this->getDataSets();
	        
	        case "default_metapage_id":
	        return $this->getDefaultMetaPageId($this->getCurrentSiteId());
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
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
	        
	        if(is_object($p)){
	            $array['_properties'][$p->getId()] = $p->__toArray();
	            $array['_properties'][$p->getId()]['_options'] = $p->getPossibleValuesAsArrays();
	            // print_r($array['_properties'][$p->getId()]);
	            $array[$p->getId()] = '';
	        }
	    }
	    
	    return $array;
	    
	}
	
	public function getDataSets($site_id=''){
	    
	    $sql = "SELECT * FROM Sets WHERE set_itemclass_id='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND (set_site_id='".$site_id."' OR set_shared='1')";
	    }
	    
	    $sql .= " ORDER BY set_label";
	    
	    $result = $this->database->queryToArray($sql);
	    $sets = array();
	    
	    foreach($result as $r){
	        $s = new SmartestCmsItemSet;
	        $s->hydrate($r);
	        $sets[] = $s;
	    }
	    
	    return $sets;
	    
	}
	
	public function getPropertiesAsArrays(){
		
		$propertyarrays = array();
		
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
    
    public function getSimpleItems($site_id='', $mode=0, $query=''){
        
        $mode = (int) $mode;
        
        $sql = "SELECT * FROM Items WHERE item_itemclass_id='".$this->_properties['id']."' AND item_deleted != 1";
        
        if(is_numeric($site_id)){
            $sql .= " AND item_site_id='".$site_id."'";
        }
        
        if($mode > 0){
            
            if(in_array($mode, array(4,5,6))){
                $sql .= " AND item_public='TRUE'";
            }else if(in_array($mode, array(1,2,3))){
                $sql .= " AND item_public='FALSE'";
            }
            
            if($mode == 8){
                $sql .= " AND item_is_archived='1'";
            }else{
                $sql .= " AND item_is_archived='0'";
            }
            
            if(in_array($mode, array(2,3,5,6))){
                $sql .= " AND item_modified > item_last_published";
            }
            
            if(in_array($mode, array(3,6))){
                $sql .= " AND item_changes_approved='1'";
            }else if(in_array($mode, array(2,5))){
                $sql .= " AND item_changes_approved='0'";
            }
            
        }
        
        if(strlen($query)){
            
            $words = explode(' ', $query);
            
            $sql .= ' AND (';
            $conditions = array();
            
            foreach($words as $w){
                $conditions[] = "(item_name LIKE '%".$w."%' OR item_search_field LIKE '%".$w."%')";
            }
            
            $sql .= implode(' AND ', $conditions).')';
            
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
    
    public function getSimpleItemsAsArrays($site_id='', $mode=0, $query=''){
        
        $items = $this->getSimpleItems($site_id, $mode, $query);
        $arrays = array();
        
        foreach($items as $item){
            $arrays[] = $item->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getItemIds($site_id=''){
        
        $sql = "SELECT item_id FROM Items WHERE item_itemclass_id='".$this->_properties['id']."' AND item_deleted != 1";
        
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
        
        $sql = "SELECT * FROM Pages WHERE page_type='ITEMCLASS' AND page_dataset_id='".$this->_properties['id']."' and page_deleted != 1";
        
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
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::load('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id);
    }
    
    public function setDefaultMetaPageId($site_id, $id){
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::save('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id, $id);
    }
    
    public function getAvailableDescriptionPropertiesAsArrays(){
        
        $properties = $this->getAvailableDescriptionProperties();
        $arrays = array();
        
        foreach($properties as $p){
            $arrays[] = $p->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getForeignKeyProperties($item_id=''){
        
        if(is_numeric($item_id)){
            
            $class = $this->getClassName();
            $item = new $class;
            
            if($item->hydrate($item_id)){
                $properties = $item->getPropertyValueHolders();
            }else{
                $properties = $this->getProperties();
            }
            
        }else{
            $properties = $this->getProperties();
        }
        
        $fk_properties = array();
        
        foreach($properties as $p){
            if($p->getDatatype() == 'SM_DATATYPE_CMS_ITEM'){
                $fk_properties[] = $p;
            }
        }
        
        return $fk_properties;
        
    }
    
    public function hasForeignKeyProperties(){
        
        return (bool) count($this->getForeignKeyProperties());
        
    }
    
    public function getForeignKeyPropertiesForModelId($model_id, $item_id=''){
        
        $model_id = (int) $model_id;
        
        $fk_properties = $this->getForeignKeyProperties($item_id);
        $properties = array();
        
        foreach($fk_properties as $p){
            if($p->getForeignKeyFilter() == $model_id){
                $properties[] = $p;
            }
        }
        
        return $properties;
        
    }
    
    public function hasForeignKeyPropertiesForModelId($model_id){
        
        return (bool) count($this->getForeignKeyPropertiesForModelId($model_id));
        
    }
    
}
