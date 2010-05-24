<?php

class SmartestQueryResultSet{
	
	protected $_model_id;
	protected $_model_class;
	protected $_items = array();
	protected $_unused_items = array();
	protected $_item_ids = array();
	protected $_unused_item_ids = array();
	protected $_items_retrieval_attempted = false;
	protected $_sort_field = '';
	protected $_sort_field_direction = 'ASC';
	protected $_is_draft = false;
	protected $database;
	
	public function __construct($model_id, $model_class, $set_item_draft_mode=false){
		$this->_model_id = $model_id;
		$this->_model_class = $model_class;
		$this->_is_draft = $set_item_draft_mode;
		$this->database = SmartestPersistentObject::get('db:main');
	}
	
	private function getSimpleIdsArray($array){
		
		$new_array = array();
		
		foreach($array as $result){
			$av = array_values($result);
			$new_array[] = $av['itempropertyvalue_item_id'];
		}
		
		return $new_array;
	}
	
	public function sort($field, $direction='ASC'){
	    
	    if(count($this->_item_ids)){
	        
	            $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemProperties, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model_id."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id";
	    
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

    	            // echo "test";
    	            $sql .= ') ORDER BY RAND()';

    	        }else{
    	            
    	            $sql .= ') ORDER BY ';
	    
        		    if($field == SmartestCmsItem::ID){
		
            		    $sql .= "Items.item_id ";
		
            		}else if($field == SmartestCmsItem::NAME){
		    
            		    $sql .= "Items.item_name ";
            		    // $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_name ";
		
            		}else if($field == SmartestCmsItem::NUM_COMMENTS){

                		$sql .= "Items.item_num_comments ";
        		    
                	}else if($field == SmartestCmsItem::NUM_HITS){

                		$sql .= "Items.item_num_hits ";
        		    
                    }else{
		    
            		    if($this->_is_draft){
            		        $sql .= "ItemPropertyValues.itempropertyvalue_draft_content ";
            		    }else{
            		        $sql .= "ItemPropertyValues.itempropertyvalue_content ";
            		    }
            		    // $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM ItemPropertyValues WHERE ItemPropertyValues.itempropertyvalue_property_id='$property_id' AND ".$value_field.' ';
		    
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
	
	public function insert($object){
		
		if($object instanceof $this->_model_class){
			$this->_items[] = $object;
			$this->_items_retrieval_attempted = false;
		}else{
		    throw new SmartestException(sprintf("Cannot add object of type %s to result set of class %s", get_class($object), $this->_model_class));
		}
		
	}
	
	public function insertItemId($id){
	    if(!in_array($id, $this->_item_ids)){
	        $this->_item_ids[] = $id;
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
	
	public function getSimpleItems($limit=0){
	    
	    // echo $limit;
	    $limit = (int) $limit;
	    
	    if(!$this->_items_retrieval_attempted){
	        
	        $cardinality = 0;
	        
	        $this->_simple_items = array();
	        
	        $sql = "SELECT * FROM Items WHERE item_id IN ('".implode("', '", $this->_item_ids)."')";
	        
	        if($limit > 0){
	            $sql .= " LIMIT ".$limit;
	        }
	        
	        $result = $this->database->queryToArray($sql);
	        
	        foreach($result as $record){
	            
    	        $obj = new SmartestItem;
	            $obj->hydrate($record);
	            $this->_simple_items[] = $obj;
	                
    	    }
	        
	        $this->_items_retrieval_attempted = true;
	    
	    }
	    
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
	
	public function getItems($limit=null, $start=null){
	        
        $cardinality = 0;
        
        $this->_items = array();
        
        $ids = $this->_item_ids;
        
        if($start > 1){
		    $key = $start-1;
		    $ids = array_slice($ids, $key);
		}
		
		if($limit > 0){
		    $ids = array_slice($ids, 0, $limit);
		}
		
		foreach($ids as $id){
            
            $obj = new $this->_model_class;
        
            if($this->_is_draft){
                $obj->setDraftMode(true);
            }
        
            if($obj->find($id, $this->_is_draft)){
                $this->_items[] = $obj;
            }
            
        }
        
        $this->_items_retrieval_attempted = true;
        
        return $this->_items;
        
	}

}