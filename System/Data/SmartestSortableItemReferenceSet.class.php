<?php

class SmartestSortableItemReferenceSet implements ArrayAccess, IteratorAggregate, Countable{
	
	protected $_model;
	protected $_items = array();
	protected $_unused_items = array();
	protected $_item_ids = array();
	protected $_unused_item_ids = array();
	protected $_items_retrieval_attempted = false;
	protected $_sort_field = '';
	protected $_sort_field_direction = 'ASC';
	protected $_is_draft = false;
	protected $database;
	
	public function __construct(SmartestModel $model, $set_item_draft_mode=false){
		$this->_model = $model;
		$this->_is_draft = $set_item_draft_mode;
		$this->database = SmartestPersistentObject::get('db:main');
	}
	
	public function getModelId(){
	    return $this->_model->getId();
	}
	
	public function getModel(){
	    return $this->_model();
	}
	
	public function getDraftMode(){
	    return $this->_is_draft();
	}
	
	private function getSimpleIdsArray($array){
		
		$new_array = array();
		
		foreach($array as $result){
			$av = array_values($result);
			$new_array[] = $av['itempropertyvalue_item_id'];
		}
		
		return $new_array;
	}
	
	public function sort($field='_DEFAULT', $direction='ASC'){
	    
	    if($field == '_DEFAULT'){
	        if(is_numeric($this->_model->getDefaultSortPropertyId())){
	            if($sort_property = $this->_model->getDefaultSortProperty()){
	                $field = $sort_property->getId();
	                $direction = $this->_model->getDefaultSortPropertyDirection();
	            }else{
	                $field = SmartestCmsItem::NAME;
	            }
            }else{
                if(strlen($this->_model->getDefaultSortPropertyId())){
                    $field = $this->_model->getDefaultSortPropertyId();
                }else{
                    $field = SmartestCmsItem::NAME;
                }
            }
	    }
	    
	    if(count($this->_item_ids)){
	        
	        $p = $this->_model->getPropertiesForReorder();
	        
	        if(in_array($field, array_merge(array_keys($p), array(SmartestCmsItem::ID, SmartestCmsItem::NAME, SmartestCmsItem::NUM_COMMENTS, SmartestCmsItem::NUM_HITS, SmartestQuery::RANDOM)))){
	            
	            if(in_array($field, array_keys($p))){
    		        
    		        if($this->_is_draft){
        		        $content_field = "ItemPropertyValues.itempropertyvalue_draft_content";
        		    }else{
        		        $content_field = "ItemPropertyValues.itempropertyvalue_content";
        		    }
    		        
        		    $property = $p[$field];
        		    $property_type_info = $property->getTypeInfo();
    		    
        		    if(!isset($property_type_info['sortable'])){
    		        
        		    }else if(!SmartestStringHelper::toRealBool($property_type_info['sortable'])){
    		        
        		    }else{
        		        // Property is not of a sortable type
        		        SmartestLog::getInstance('system')->log('Tried to sort by a property of a non-sortable type');
        		    }
        		    
        		    if(isset($property_type_info['quantity']) && SmartestStringHelper::toRealBool($property_type_info['quantity'])){
    		            $sql = "SELECT DISTINCT CONVERT(".$content_field.", DECIMAL(15,5)) AS content,";
		            }else{
		                $sql = "SELECT DISTINCT ".$content_field." AS content,";
		            }
    		        
		        }else{
		            
		            $sql = "SELECT DISTINCT";
		            
		        }
	        
                $sql .= " itempropertyvalue_item_id FROM Items, ItemProperties, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id";
    
        	    if(!in_array($field, array(SmartestCmsItem::ID, SmartestCmsItem::NAME, SmartestCmsItem::NUM_COMMENTS, SmartestCmsItem::NUM_HITS, SmartestQuery::RANDOM))){
        	        $sql .= " AND ItemPropertyValues.itempropertyvalue_property_id=ItemProperties.itemproperty_id AND ItemPropertyValues.itempropertyvalue_property_id='".$field."'";
        	    }
    
        	    $sql .= " AND Items.item_id IN (";
    
        	    $i = 0;
    
        	    foreach($this->_item_ids as $id){
        
        	        if($i > 0){
        	            $sql .= ',';
        	        }
        
        	        $sql .= $id;
        
        	        $i++;
        	    }
    
        	    if($field == SmartestQuery::RANDOM){

    	            $sql .= ') ORDER BY RAND()';

    	        }else{
	            
    	            $sql .= ') ORDER BY ';
    
        		    if($field == SmartestCmsItem::ID){
	
            		    $sql .= "Items.item_id ";
	
            		}else if($field == SmartestCmsItem::NAME){
	    
            		    $sql .= "Items.item_name ";
        		    
            		}else if($field == SmartestCmsItem::NUM_COMMENTS){

                		$sql .= "Items.item_num_comments ";
    		    
                	}else if($field == SmartestCmsItem::NUM_HITS){

                		$sql .= "Items.item_num_hits ";
    		    
                    }else{
	    
            		    $sql .= "content ";
        		    
            		}
    		
            		$sql .= $direction;
    		
    		    }
    		    
    		    $result = $this->database->queryToArray($sql);
        		
        		$ids = array();
	
    		    foreach($result as $record){
    		        $ids[] = $record['itempropertyvalue_item_id'];
    		    }
	
    		    $this->_item_ids = $ids;
    	        $this->_items_retrieval_attempted = false;
	        
            }
	    
        }
		
	}
	
	public function insert($object){
		
		$c = $this->_model->getClassName();
		
		if($object instanceof $c){
			$this->_items[] = $object;
			$this->_items_retrieval_attempted = false;
		}else{
		    throw new SmartestException(sprintf("Cannot add object of type %s to result set of class %s", get_class($object), $this->_model->getClassName()));
		}
		
	}
	
	public function insertItemId($id){
	    if(!in_array($id, $this->_item_ids)){
	        $this->_item_ids[] = $id;
	    }
	}
	
	public function loadItemIds($ids, $overwrite=false){
	    if(is_array($ids)){
	        if($overwrite){
	            $this->_item_ids = array();
	        }
	        foreach($ids as $item_id){
	            if(($overwrite || !in_array($this->_item_ids)) && is_numeric($item_id)){
	                $this->_item_ids[] = $item_id;
	            }
	        }
	    }
	}
	
	public function getFirst(){
	    
	    $this->getItems();
	    
	    if(count($this->_items)){
	        return $this->_items[0];
	    }
	}
	
	public function __toArray(){
		
	}
	
	protected function _getSimpleItems($limit=0){
	    
	    $limit = (int) $limit;
	    
	    $cardinality = 0;
        
        $sql = "SELECT * FROM Items WHERE item_id IN ('".implode("', '", $this->_item_ids)."')";
        
        if($limit > 0){
            $sql .= " LIMIT ".$limit;
        }
        
        $result = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($result as $record){
            
	        $obj = new SmartestItem;
            $obj->hydrate($record);
            $items[$record['item_id']] = $obj;
                
	    }
	    
	    return $items;
	
	}
	
	public function getSimpleItems($limit=0){
	    
	    if(!$this->_items_retrieval_attempted){
	        $this->_simple_items = array_values($this->_getSimpleItems($limit));
    	    $this->_items_retrieval_attempted = true;
	    }
	    
	    return $this->_simple_items;
	    
	}
	
	public function getIds(){
	    return $this->_item_ids;
	}
	
	// For consistency/ease of use
	public function getItemIds(){
	    return $this->getIds();
	}
	
	public function getSimpleItemsPreservingOrder($limit=0){
	    
	    $items = $this->_getSimpleItems($limit);
	    
	    foreach($this->_item_ids as $id){
	        $this->_simple_items[] = $items[$id];
	    }
	    
	    $this->_items_retrieval_attempted = true;
	    
	    return $this->_simple_items;
	    
	}
	
	public function getCardinality(){
	    return count($this->_item_ids);
	}
	
	public function getNumPages($page_size){
	    
	    $cardinality = $this->getCardinality();
	    
	    if($page_size > $cardinality){
	        return 1;
	    }else{
	        
	        $num_pages = 1;
	        $c = $cardinality;
	        
	        while($c > $page_size){
	            ++$num_pages;
	            $c = $c - $page_size;
	        }
	        
	        return $num_pages;
	        
	    }
	    
	}
	
	public function getPage($page_num, $page_size){
	    
	    $start = ($page_num-1) * $page_size + 1;
	    
	    if($start > $this->getCardinality()){
	        return array();
	    }else{
	        return $this->getItems($page_size, $start);
	    }
	    
	}
	
	/* public function getNumPages($page_size=10){
	    
	} */
	
	public function getItems($limit=null, $start=null){
	        
        $cardinality = 0;
        
        // $this->_items = array();
        
        $ids = $this->_item_ids;
        
        if($start > 1){
		    $key = $start-1;
		    $ids = array_slice($ids, $key);
		}
		
		if($limit > 0){
		    $ids = array_slice($ids, 0, $limit);
		}
		
		$h = new SmartestCmsItemsHelper;
		$this->_items = $h->hydrateUniformListFromIdsArrayPreservingOrder($ids, $this->_model->getId(), $this->_is_draft);
		
		$this->_items_retrieval_attempted = true;
        
        return $this->_items;
        
	}
	
	public function count(){
	    // echo "counting";
        return count($this->_item_ids);
    }

    public function offsetGet($offset){
    
        switch($offset){
        
            case "_ids":
            return $this->getIds();
            case "_data":
            case "_items":
            case "_objects":
            return $this->getData();
            case "_count":
            return count($this->_item_ids);
            case "_keys":
            if(!$this->_items_retrieval_attempted){
                $this->getItems();
            }
            return array_keys($this->_items);
            case "_first":
            if(!$this->_items_retrieval_attempted){
                $this->getItems();
            }
            return reset($this->_items);
            case "_last":
            if(!$this->_items_retrieval_attempted){
                $this->getItems();
            }
            return end($this->_items);
        
        }
    
        return $this->_items[$offset];
    }

    public function offsetExists($offset){
        return isset($this->_items[$offset]);
    }

    public function offsetSet($offset, $value){
        if($offset){
            $this->_items[$offset] = $value;
        }else{
            $this->_items[] = $value;
        }
    }

    public function offsetUnset($offset){
        unset($this->_items[$offset]);
    }

    /* public function next(){
        return next($this->_data);
    }

    public function seek($index){
    
        $this->rewind();
        $position = 0;
    
        while($position < $index && $this->valid()) {
            $this->next();
            $position++;
        }
    
        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid seek position');
        }
    
    } */

    public function &getIterator(){
        return new ArrayIterator($this->getItems());
    }

    /* public function current(){
        return current($this->_data);
    }

    public function key(){
        return array_search(current($this->_data), $this->_data);
    }

    public function rewind(){
        reset($this->_data);
    } */
    
    public function mergeWith(SmartestSortableItemReferenceSet $set){
        
        $ids_array = array_unique(array_merge($this->getItemIds(), $set->getItemIds()));
        
        if($set->getModelId() == $this->getModelId()){
            $s = new SmartestSortableItemReferenceSet($this->_model, $this->_is_draft);
            $s->loadItemIds($ids_array, true);
            return $s;
        }else{
            throw new SmartestException('SmartestSortableItemReferenceSet of model '.$this->_model->getName().' merged with SmartestSortableItemReferenceSet of model '.$set->getModel()->getName());
        }
    }
    
    public static function mergeSeveral(){
        
        $sets = func_get_args();
        $num_sets = count($sets);
        
        if($num_sets){
        
            $primary_set = $sets[0];
        
            for($i=1;$i<$num_sets;$i++){
            
                if(isset($sets[$i])){
                
                    if($sets[$i] instanceof SmartestSortableItemReferenceSet){
                        $primary_set = $primary_set->mergeWith($sets[$i]);
                    }else{
                        throw new SmartestException('Argument no '.($i+1).' provided to SmartestSortableItemReferenceSet::mergeSeveral() was not an instance of SmartestSortableItemReferenceSet.');
                    }
                
                }
            
            }
            
            return $primary_set;
        
        }
        
    }
    
    public function remove(SmartestSortableItemReferenceSet $set){
        
        $ids_array = array_diff($this->getItemIds(), $set->getItemIds());
        
        if($set->getModelId() == $this->getModelId()){
            $s = new SmartestSortableItemReferenceSet($this->_model, $this->_is_draft);
            $s->loadItemIds($ids_array, true);
            return $s;
        }else{
            throw new SmartestException('SmartestSortableItemReferenceSet of model '.$set->getModel()->getName().' removed from SmartestSortableItemReferenceSet of model '.$this->_model->getName());
        }
        
    }
    
    public function intersectWith(SmartestSortableItemReferenceSet $set){
        
        $ids_array = array_unique(array_intersect($this->getItemIds(), $set->getItemIds()));
        
        if($set->getModelId() == $this->getModelId()){
            $s = new SmartestSortableItemReferenceSet($this->_model, $this->_is_draft);
            $s->loadItemIds($ids_array, true);
            return $s;
        }else{
            throw new SmartestException('SmartestSortableItemReferenceSet of model '.$this->_model->getName().' intersected with SmartestSortableItemReferenceSet of model '.$set->getModel()->getName());
        }
    }

    public function append($value){
        $this->_items[] = $value;
    }

    public function asort(){
        if(!$this->_items_retrieval_attempted){
            $this->getItems();
        }
        sort($this->_items);
    }

    public function ksort(){
        if(!$this->_items_retrieval_attempted){
            $this->getItems();
        }
        ksort($this->_items);
    }

    public function natcasesort(){
        if(!$this->_items_retrieval_attempted){
            $this->getItems();
        }
        natcasesort($this->_items);
    }

    public function natsort(){
        if(!$this->_items_retrieval_attempted){
            $this->getItems();
        }
        natsort($this->_items);
    }

    public function reverse(){
        if(!$this->_items_retrieval_attempted){
            $this->getItems();
        }
        return array_reverse($this->_items);
    }

}