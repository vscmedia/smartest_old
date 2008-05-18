<?php

class SmartestCmsItemSet extends SmartestDataObject{
    
    protected $_set_members = array();
    protected $_set_member_ids = array();
    protected $_set_member_webids = array();
    protected $_set_member_slugs = array();
    protected $_conditions = array();
    protected $_fetch_attempted = false;
    protected $_model = null;
    
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('ModelId', 'itemclass_id');
		$this->_table_prefix = 'set_';
		$this->_table_name = 'Sets';
		
	}
	
	public function setType($type){
	    if(!$this->_came_from_database){
	        $this->_properties['type'] = $type;
	        $this->_modified_properties['type'] = $type;
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public function addItems($item_ids){
	    
	    foreach($item_ids as $key=>$id){

		    $this->addItem($id);
		    
        }
	}
	
	public function addItem($id, $safe=false){
	    
	    if($this->getType() == 'STATIC'){
	    
	        if($safe){
	            $count_query  = "SELECT setlookup_id FROM SetsItemsLookup WHERE setlookup_set_id='".$set_id."' AND setlookup_item_id='".$id."'";
	            $count        = $this->database->howMany($count_query);
            }else{
                $count = 0;
            }
	    
	        if($count == 0){
	            $sql = "INSERT INTO SetsItemsLookup (setlookup_set_id, setlookup_item_id) VALUES ('".$this->getId()."','".$id."')";
	            $this->database->rawQuery($sql);
            }
        
        }
	    
	}
	
	public function removeItems($item_ids){
	    
	    foreach($item_ids as $key=>$id){

		    $this->removeItem($id);
		    
        }
	}
	
	public function removeItem($id, $limit=true){
	    
	    if($this->getType() == 'STATIC'){
	    
	        if($count == 0){
	            $sql = "DELETE FROM SetsItemsLookup WHERE setlookup_set_id='".$this->getId()."' AND setlookup_item_id='".$id."'";
	            
	            if($limit){
	                $sql .= ' LIMIT 1';
	            }
	            
	            $this->database->rawQuery($sql);
            }
        
        }
	    
	}
	
	public function getMembers($draft=false, $refresh=false, $limit=null, $query_data=''){
	    // calculate which items are in the set and assign to $_set_members.
	    
	    if(!is_array($query_data)){
	        $query_data = array();
	    }
	    
	    // print_r($query_data);
	    
	    if($refresh || !$this->_fetch_attempted){
	    
	        if($this->getType() == 'STATIC'){
	        
    	        $model = new SmartestModel;
    	        $model->hydrate($this->getModelId());
    	        $class_name = $model->getClassName();
	        
    	        $sql = "SELECT setlookup_item_id FROM SetsItemsLookup WHERE setlookup_set_id='".$this->getId()."' ORDER BY setlookup_order ASC";
    	        
    	        $results = $this->database->queryToArray($sql);
	            
	            $cardinality = 0;
	            
    	        foreach($results as $lookup){
	                
	                if(($limit && is_numeric($limit) && $cardinality <= $limit) || !$limit){
	                
    	                $item = new $class_name;
    	                $item->hydrate($lookup['setlookup_item_id'], $draft);
    	                
    	                if($item->isHydrated()){
    	                    $this->_set_members[$cardinality] = $item;
    	                    $this->_set_member_ids[$cardinality] = $item->getId();
    	                    $this->_set_member_webids[$cardinality] = $item->getWebid();
    	                    $this->_set_member_slugs[$cardinality] = $item->getSlug();
    	                    $cardinality++;
    	                }
	                }
    	        }
	        
    	        $this->_fetch_attempted = true;
	        
    	    }else if($this->getType() == 'DYNAMIC'){
	            
	            if(SmartestCache::hasData('dynamic_set_item_ids_'.$this->getId(), true)){
	                
	                $ids = SmartestCache::load('dynamic_set_item_ids_'.$this->getId(), true);
	                
	            }else{
	            
	                $model = $this->getModel();
	            
    	            // get members if the set is dynamic
    	            $q = new SmartestQuery($model->getId());
	            
    	            $data_source = $this->getDataSourceSiteId();
	            
    	            $site_id = $this->getCurrentSiteId();
                
    	            if($data_source){
    	                if(is_numeric($data_source)){
    	                    $q->setSiteId($data_source);
                        }else if($data_source == 'CURRENT' && is_numeric($site_id)){
                            // echo $site_id;
                            $q->setSiteId($site_id);
                        }else if($data_source == 'ALL'){
                            // do nothing, and the query will know it's looking at all sites
                        }
    	            }
	            
    	            // get conditions
    	            foreach($this->getConditions() as $c){
	                
    	                $final_value = $c->getValue();
	                
    	                if(SmartestStringHelper::startsWith($final_value, ':')){
	                    
    	                    $keys = array_keys($query_data);
    	                    $position = array_search(substr($final_value, 1), $keys);
	                    
    	                    if($position !== false){
    	                        $final_value = $query_data[$keys[$position]];
    	                    }else{
    	                        $final_value = '';
    	                    }
    	                }
	                    
	                    // little hack to get around the fact that there can be more than one tagging criteria but they aren't actual properties
	                    $property_id = $c->getItempropertyId();
	                    
	                    if($property_id == '_SMARTEST_ITEM_TAGGED'){
	                        $property_id .= '_'.$c->getId();
	                    }
	                    
	                    $q->add($property_id, $final_value, $c->getOperator(), $draft);
    	            }
	            
    	            $result = $q->doSelect($draft);
	            
    	            if($this->getSortField()){
    	                $result->sort($this->getSortField(), $this->getSortDirection());
    	            }
	            
    	            $this->_set_members = $result->getItems($limit);
	            
    	            foreach($this->_set_members as $item){
	                
    	                $this->_set_member_ids[] = $item->getId();
    	                $this->_set_member_webids[] = $item->getWebid();
    	                $this->_set_member_slugs[] = $item->getSlug();
	                
    	            }
	            
                }
	            
	            $this->_fetch_attempted = true;
	            
    	    }
	    
        }
	    
	    return $this->_set_members;
	}
	
	public function getMemberIds($draft=false, $refresh=false){
	    
	    $this->getMembers($draft, $refresh);
	    return $this->_set_member_ids;
	    
	}
	
	public function getItem($field, $value){
	    
	    foreach($this->_set_members as $item){
	        
	        if($field == 'id'){
	            $compare = $item->getId();
	        }else if($field == 'slug'){
    	        $compare = $item->getSlug();
            }else if($field == 'webid'){
	            $compare = $item->getWebid();
            }
            
            if($value == $compare){
                return $item;
            }
	    
        }
        
        return null;
        
	}
	
	public function getMembersAsArrays($draft=false, $refresh=false){
	    
	    $members = $this->getMembers($draft, $refresh);
	    
	    // print_r($draft);
	    
	    $result = array();
	    
	    foreach($members as $item){
	        $result[] = $item->__toArray($draft);
	    }
	    
	    return $result;
	    
	}
	
	public function __toArray($get_members=true){
	    $result = parent::__toArray();
	    
	    if($get_members){
	        $result['_members'] = $this->getMembersAsArrays();
        }
        
	    return $result;
	}
	
	public function getModel(){
	    // return $this->getItemclass();
	    if(!$this->_model){
	        
	        $model = new SmartestModel;
	        
	        $model->hydrate($this->getItemclassId());
	        $this->_model = $model;
        }
        
        return $this->_model;
	}
	
	public function getConditions(){
	    
	    if($this->getType != 'STATIC'){
	        
	        $this->_conditions = array();
	        
	        $sql = "SELECT * FROM SetRules WHERE setrule_set_id='".$this->getId()."'";
	        $result = $this->database->queryToArray($sql);
	    
	        foreach($result as $c){
	            $condition = new SmartestDynamicDataSetCondition;
	            $condition->hydrate($c);
	            $this->_conditions[] = $condition;
	        }
	        
	        return $this->_conditions;
	        
        }
	}
	
	public function getConditionsAsArrays(){
	    $conditions = $this->getConditions();
	    
	    $result = array();
	    
	    foreach($conditions as $c){
	        $result[] = $c->__toArray();
	    }
	    
	    return $result;
	    
	}
	
	public function hasItem($field, $value){
	    
	    // make sure the set is populated
	    $this->getMembers();
	    
	    // print_r($this->_set_member_ids);
	    
	    // echo $field.' '.$value;
	    
	    if($field == "id"){
	        $tmp_array = $this->_set_member_ids;
	    }else if($field == "slug"){
            $tmp_array = $this->_set_member_slugs;
        }else if($field == 'webid'){
            // echo $compare.' ';
	        $tmp_array = $this->_set_member_webids;
        }else{
    	    // create an empty array to avoid E_WARNING
    	    $tmp_array = array();
    	}
    	
    	// print_r($this->_set_member_webids);
    	
    	if(in_array($value, $tmp_array)){
    	    $has_it = true;
    	}else{
    	    $has_it = false;
    	}
    	
    	// print_r($tmp_array);
    	// var_dump($has_it);
    	
    	return $has_it;
    	
	}
	
	function compile(){
	    return $this->__toArray();
	}
	
	/* public function hasPropertyOfId($id){
	    
	}*/

}