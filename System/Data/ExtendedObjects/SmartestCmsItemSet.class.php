<?php

class SmartestCmsItemSet extends SmartestSet implements SmartestSetApi{

    protected $_set_members = array();
    protected $_set_members_simple = array();
    protected $_set_member_ids = array();
    protected $_set_member_webids = array();
    protected $_set_member_slugs = array();
    protected $_conditions = array();
    protected $_fetch_attempted = false;
    protected $_model = null;
    
    public function __objectConstruct(){
        
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
	    
	    $order_index = $this->getNextOrderIndex();
	    
	    foreach($item_ids as $key=>$id){

		    $this->addItem($id, false, $order_index);
		    $order_index++;
		    
        }
	}
	
	public function addItem($id, $safe=false, $order_index=''){
	    
	    if($this->getType() == 'STATIC'){
	    
	        if($safe){
	            $count_query  = "SELECT setlookup_id FROM SetsItemsLookup WHERE setlookup_set_id='".$set_id."' AND setlookup_item_id='".$id."'";
	            $count        = $this->database->howMany($count_query);
            }else{
                $count = 0;
            }
	        
	        if(!$order_index){
	            $order_index = $this->getNextOrderIndex();
	        }
	        
	        if($count == 0){
	            $sql = "INSERT INTO SetsItemsLookup (setlookup_set_id, setlookup_item_id, setlookup_order) VALUES ('".$this->getId()."','".$id."', '".$order_index."')";
	            $this->database->rawQuery($sql);
            }
        
        }else{
            
            SmartestLog::getInstance('system')->log("SmartestCmsItemSet::addItem() was called on a non-static data set: '{$this->getName()}'.");
            
        }
	    
	}
	
	public function getNextOrderIndex(){
	    
	    if($this->getType() == 'STATIC'){
	    
	        $sql = "SELECT setlookup_order FROM SetsItemsLookup, Sets WHERE SetsItemsLookup.setlookup_set_id=Sets.set_id AND Sets.set_id='".$this->getId()."' ORDER BY SetsItemsLookup.setlookup_order DESC LIMIT 1";
	        $result = $this->database->queryToArray($sql);
	        
	        if(count($result)){
	            $existing_highest_index = (int) $result[0]['setlookup_order'];
	            return $existing_highest_index+1;
	        }else{
	            return 0;
	        }
	    
	    }else{
	        
	        SmartestLog::getInstance('system')->log("SmartestCmsItemSet::getNextOrderIndex() was called on a non-static data set: '{$this->getName()}'.");
	        
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
        
        }else{

            SmartestLog::getInstance('system')->log("SmartestCmsItemSet::removeItem() was called on a non-static data set: '{$this->getName()}'.");

        }
	    
	}
	
	public function delete(){
	    
	    if($this->getType() == 'STATIC'){
	        
	        $ls = $this->getLookups(SM_QUERY_ALL_DRAFT);
	        
	        foreach($ls as $l){
	            $l->delete();
	        }
	        
	        parent::delete();
	        
	    }else if($this->getType() == 'DYNAMIC'){
	        
	        $cs = $this->getConditions();
	        
	        foreach($cs as $c){
	            $c->delete();
	        }
	        
	        parent::delete();
	        
	    }
	    
	}
	
	public function getLookups($mode){
	    
	    if($this->getType() == 'STATIC'){
	        
	        $sql = "SELECT SetsItemsLookup.* FROM Items, SetsItemsLookup WHERE SetsItemsLookup.setlookup_item_id=Items.item_id AND SetsItemsLookup.setlookup_set_id='".$this->getId()."'";
	        
	        if($mode > 5){
	            $sql .= " AND Items.item_public='TRUE'";
	        }
	        
	        if(in_array($mode, array(1,4,7,10))){
		    
		        $sql .= " AND Items.item_is_archived='1'";
		    
	        }else if(in_array($mode, array(2,5,8,11))){
	            
	            $sql .= " AND Items.item_is_archived='0'";
	            
	        }
	        
	        $sql .= " ORDER BY SetsItemsLookup.setlookup_order ASC";
	        
	        $results = $this->database->queryToArray($sql);
            $lookups = array();
            
	        foreach($results as $result){
                
                $lookup = new SmartestSetItemLookup;
	            $lookup->hydrate($result);
	            $lookups[] = $lookup;
	            
	        }
	        
	        return $lookups;
	        
	    }else{
	        throw new SmartestException("SmartestCmsItemSet::getLookups() must be called on a static set", SM_ERROR_USER);
	    }
	    
	}
	
	public function fixOrderIndices(){
	    
	    if($this->getType() == 'STATIC'){
	    
    	    $lookups = $this->getLookups(SM_QUERY_ALL_DRAFT);
    	    $c = count($lookups);
	    
    	    for($i=0;$i<$c;$i++){
    	        $l = $lookups[$i];
    	        $l->setOrder($i);
    	        $l->save();
    	    }
	    
        }else{
            throw new SmartestException("SmartestCmsItemSet::fixOrderIndices() must only be called on a static data set", SM_ERROR_USER);
        }
	    
	}
	
	public function getMembers($mode=9, $query_data=''){
	    
	    // retrieve ALL members of the set, but only once per instance
	    
	    if(!is_array($query_data)){
	        $query_data = array();
	    }
	    
	    $draft = $mode < 6;
	    
	    if($refresh || !$this->_fetch_attempted){
	    
	        if($this->getType() == 'STATIC'){
	            
	            if(!$this->_fetch_attempted){
    	            $this->_set_members = $this->getRawStaticSetMembers($mode);
	                $this->_fetch_attempted = true;
                }
	        
    	    }else if($this->getType() == 'DYNAMIC'){
    	        
    	        if(!$this->_fetch_attempted){
    	            $this->_set_members = $this->getRawDynamicSetMembers($mode, $query_data);
	                $this->_fetch_attempted = true;
	            }
	            
    	    }
	    
        }
        
        foreach($this->_set_members as $key=>$item){
	                
            $this->_set_member_ids[] = $item->getId();
            $this->_set_member_webids[] = $item->getWebid();
            $this->_set_member_slugs[] = $item->getSlug();
            $this->_set_members[$key]->setDraftMode($draft);
        
        }
	    
	    return $this->_set_members;
	    
	}
	
	public function getMembersPaged($mode=9, $limit=0, $start=1, $query_data=''){
	    
	    // if static
	      // get ids
	      // remove ids that come before $start
	      // remove ids that come after limit
	      
	    // if dynamic
	      // get result set
	      // tell result set to remove ids before $start and after $limit
	      
	    if($this->getType() == 'STATIC'){
	        
	        $ids = $this->getRawStaticSetMemberIds($mode);
	        
	        if($start > 1){
    		    $key = $start-1;
    		    $ids = array_slice($ids, $key);
    		}
    		
    		if($limit > 0){
    		    $ids = array_slice($ids, 0, $limit);
    		}
    		
    		return $this->getItemsFromIds($ids);
	        
  	    }else if($this->getType() == 'DYNAMIC'){
  	        
            return $this->getRawDynamicSetResultSet($mode)->getItems($limit, $start);
	            
  	    }
	    
	}
	
	public function getRawStaticSetMemberIds($mode){
	    
	    $sql = "SELECT setlookup_item_id FROM Items, SetsItemsLookup WHERE SetsItemsLookup.setlookup_item_id=Items.item_id AND SetsItemsLookup.setlookup_set_id='".$this->getId()."'";
        
        if($mode > 5){
            $sql .= " AND Items.item_public='TRUE'";
        }
        
        $sql .= " AND Items.item_deleted!='1'";
        
        if(in_array($mode, array(1,4,7,10))){
	    
	        $sql .= " AND Items.item_is_archived='1'";
	    
        }else if(in_array($mode, array(2,5,8,11))){
            
            $sql .= " AND Items.item_is_archived='0'";
            
        }
        
        $sql .= " ORDER BY SetsItemsLookup.setlookup_order ASC";
        
        $results = $this->database->queryToArray($sql);
        
        $cardinality = 0;
        $ids = array();
        
        foreach($results as $lookup){
            $ids[] = $lookup['setlookup_item_id'];
        }
        
        return $ids;
	    
	}
	
	public function getRawStaticSetMembers($mode=9){
	    
	    $ids = $this->getRawStaticSetMemberIds($mode);
	    $members = $this->getItemsFromIds($ids);
        return $members;
        
	}
	
	public function getRawDynamicSetMemberIds($mode=9, $limit=0, $start=1){
	    if($this->getType() == 'DYNAMIC'){
	        return $this->getRawDynamicSetResultSet()->getIds();
        }
	}
	
	public function getRawDynamicSetResultSet($mode=9, $query_data=''){
	    
	    $model = $this->getModel();
	    
	    $q = new SmartestQuery($model->getId());
    
        $data_source = $this->getDataSourceSiteId();
    
        $site_id = $this->getCurrentSiteId();
        
        if($data_source){
            if(is_numeric($data_source)){
                $q->setSiteId($data_source);
            }else if($data_source == 'CURRENT' && is_numeric($site_id)){
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
            
            $q->add($property_id, $final_value, $c->getOperator());
        }
    
        $result = $q->doSelect($mode);
    
        if($this->getSortField()){
            $result->sort($this->getSortField(), $this->getSortDirection());
        }
        
        // returns a SmartestQueryResultSet object
        return $result;
	    
	}
	
	public function getRawDynamicSetMembers($mode=9, $query_data=''){
	    
	    return $this->getRawDynamicSetResultSet($mode, $query_data)->getItems();
	    
	}
	
	public function getSimpleMembers($mode=9, $refresh=false, $limit=null, $query_data=''){
	    
	    // calculate which items are in the set and assign to $_set_members.
	    
	    if(!is_array($query_data)){
	        $query_data = array();
	    }
	    
	    $draft = $mode < 6;
	    
	    if($refresh || !$this->_fetch_attempted){
	    
	        if($this->getType() == 'STATIC'){
	        
    	        $model = new SmartestModel;
    	        $model->hydrate($this->getModelId());
    	        $class_name = $model->getClassName();
	        
    	        $sql = "SELECT Items.* FROM Items, SetsItemsLookup WHERE SetsItemsLookup.setlookup_item_id=Items.item_id AND SetsItemsLookup.setlookup_set_id='".$this->getId()."'";
    	        
    	        if($mode > 5){
    	            $sql .= " AND Items.item_public='TRUE'";
    	        }
    	        
    	        if(in_array($mode, array(1,4,7,10))){
			    
			        $sql .= " AND Items.item_is_archived='1'";
			    
		        }else if(in_array($mode, array(2,5,8,11))){
		            
		            $sql .= " AND Items.item_is_archived='0'";
		            
		        }
    	        
    	        $sql .= " ORDER BY SetsItemsLookup.setlookup_order ASC";
    	        
    	        $results = $this->database->queryToArray($sql);
	            
	            $cardinality = 0;
	            
    	        foreach($results as $lookup){
	                
	                if(($limit && is_numeric($limit) && $cardinality <= $limit) || !$limit){
	                
    	                $item = new SmartestItem;
    	                $item->hydrate($lookup);
    	                
    	                if($item->isHydrated()){
    	                    $this->_set_members_simple[$cardinality] = $item;
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
	                    
	                    $q->add($property_id, $final_value, $c->getOperator());
    	            }
	            
    	            $result = $q->doSelect($mode);
	            
		            if($this->getSortField()){
    	                $result->sort($this->getSortField(), $this->getSortDirection());
    	            }
	            
    	            $this->_set_members_simple = $result->getSimpleItems($limit);
	            
    	            foreach($this->_set_members_simple as $item){
	                
    	                $this->_set_member_ids[] = $item->getId();
    	                $this->_set_member_webids[] = $item->getWebid();
    	                $this->_set_member_slugs[] = $item->getSlug();
	                
    	            }
	            
                }
	            
	            $this->_fetch_attempted = true;
	            
    	    }
	    
        }
	    
	    return $this->_set_members_simple;
	}
	
	public function getMemberIds($mode=9, $refresh=false){
	    
	    $this->getMembers($mode);
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
	
	/* public function getMembersAsArrays($mode=9, $refresh=false){
	    
	    $members = $this->getMembers($mode, $refresh);
	    
	    // print_r($draft);
	    
	    $draft = in_array($mode, array(0,1,2,6,7,8));
	    
	    $result = array();
	    
	    foreach($members as $item){
	        $result[] = $item->__toArray($draft);
	    }
	    
	    return $result;
	    
	}
	
	public function getSimpleMembersAsArrays($mode=9, $refresh=false){
	    
	    $members = $this->getSimpleMembers($mode, $refresh);
	    
	    $draft = in_array($mode, array(0,1,2,6,7,8));
	    
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
	} */
	
	public function getModel(){
	    
	    if(!$this->_model){
	        $model = new SmartestModel;
	        $model->find($this->getItemclassId());
	        $this->_model = $model;
        }
        
        return $this->_model;
	}
	
	public function getModelId(){
	    return $this->getItemclassId();
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
	    
	    if($field == "id"){
	        $tmp_array = $this->_set_member_ids;
	    }else if($field == "slug"){
            $tmp_array = $this->_set_member_slugs;
        }else if($field == 'webid'){
            $tmp_array = $this->_set_member_webids;
        }else{
    	    // create an empty array to avoid E_WARNING
    	    $tmp_array = array();
    	}
    	
    	if(in_array($value, $tmp_array)){
    	    $has_it = true;
    	}else{
    	    $has_it = false;
    	}
    	
    	return $has_it;
    	
	}
	
	function compile(){
	    return $this->__toArray();
	}
	
	public function getItemsFromIds($ids){
	    
	    $members = array();
	    
	    // $model = new SmartestModel;
        // $model->find($this->getModelId());
        $class_name = $this->getModel()->getClassName();
        // $cardinality = 0;
	    
	    foreach($ids as $id){
            
            if(($limit && is_numeric($limit) && $cardinality <= $limit) || !$limit){
            
                $item = new $class_name;
                $item->find($id, $draft);
                $item->setDraftMode($draft);
                // echo $item->getName().' ';
                // var_dump($item->isHydrated());
                
                if($item->isHydrated()){
                    $members[] = $item;
                    // $cardinality++;
                }
            }
        }
        
        return $members;
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "model":
	        return $this->getModel();
	        
	        case "_members":
	        return $this->getMembers();
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}

}