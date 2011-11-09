<?php

class SmartestQuery{
	
	protected $database;
	protected $conditions = array();
	protected $_model;
	protected $_properties;
	protected $site_id = null;
	
	const EQUAL = 0;
	const EQUALS = 0;
	
	const NOT_EQUAL = 1;
	const NOTEQUAL = 1;
	
	const CONTAINS = 2;
	
	const NOTCONTAINS = 3;
	const NOT_CONTAINS = 3;
	const DOESNOTCONTAIN = 3;
	const DOES_NOT_CONTAIN = 3;
	
	const STARTSWITH = 4;
	const STARTS_WITH = 4;
	
	const ENDSWITH = 5;
	const ENDS_WITH = 5;
	
	const GREATERTHAN = 6;
	const GREATER_THAN = 6;
	
	const LESSTHAN = 7;
	const LESS_THAN = 7;
	
	const TAGGEDWITH = 8;
	const TAGGED_WITH = 8;
	
	const NOTTAGGEDWITH = 9;
	const NOT_TAGGED_WITH = 9;
	
	const IN = 256;
	
	const RANDOM = -1024;
	
	public function __construct($model_id, $site_id=''){
		
		$this->database =& SmartestPersistentObject::get('db:main');
		
		$this->setSiteId($site_id);
		
		$models = SmartestCache::load('model_id_name_lookup', true);
		
		$m = new SmartestModel;
		
		if($m->find($model_id)){
		    $this->_model = $m;
		    $this->_properties = $m->getPropertiesForQueryEngine();
		}else{
		    // ERROR: using non-existent model
		    throw new SmartestException("The specified model $model_id was not recognized.");
		}
		
	}
	
	public static function getModelIdNameLookup(){
	    
	    if(SmartestCache::hasData('model_id_name_lookup', true)){
	        return SmartestCache::load('model_id_name_lookup', true);
	    }else{
	        
	    }
	    
	}
	
	public function setSiteId($site_id){
	    if(is_numeric($site_id)){
	        $this->site_id = $site_id;
	    }
	}
	
	public function getSiteId(){
	    if(is_numeric($this->site_id)){
	        return $this->site_id;
	    }else{
	        return false;
	    }
	}
	
	public function getModel(){
		return $this->_model;
	}
	
	public function add($property_id, $value, $operator=0){
	    if(isset($this->_properties[$property_id])){
	        $p = $this->_properties[$property_id];
	        try{
	            if($value_obj = SmartestDataUtility::objectize($value, $p->getDataType())){
	                $c = new SmartestQueryCondition($value_obj, $p, $operator);
	                $this->conditions[] = $c;
	            }else{
	                // value not understood - log and use SmartestString?
	                $value_obj = new SmartestString($value);
	                $c = new SmartestQueryCondition($value_obj, $p, $operator);
	                $this->conditions[] = $c;
	            }
	        }catch(SmartestException $e){
	            // value not understood - log and skip?
	        }
	    }else if($property_id == SmartestCmsItem::NAME){
	        $value_obj = new SmartestString($value);
	        $p = new SmartestPseudoItemProperty;
	        $p->setId(SmartestCmsItem::NAME);
	        $c = new SmartestQueryCondition($value_obj, $p, $operator);
	        $this->conditions[] = $c;
	    }else if($property_id == SmartestCmsItem::ID || $property_id == SmartestCmsItem::NUM_COMMENTS || $property_id == SmartestCmsItem::NUM_HITS || $property_id == SmartestCmsItem::AVERAGE_RATING){
	        $value_obj = new SmartestNumeric($value);
	        $p = new SmartestPseudoItemProperty;
	        $p->setId($property_id);
	        $c = new SmartestQueryCondition($value_obj, $p, $operator);
	        $this->conditions[] = $c;
        }else{
	        // unknown property ID - throw exception?
	    }
	}
	
	public function clear(){
		$this->conditions = array();
	}
	
	private function getSimpleIdsArray($array){
		
		$new_array = array();
		
		foreach($array as $result){
			$av = array_values($result);
			$new_array[] = $av[0];
		}
		
		return $new_array;
	}
	
	private function createResultSet($conditions, $set_item_draft_mode){
		
		$ids_array = array();
		
		$class_name = $this->_model->getClassName();
		
		$ds = new SmartestSortableItemReferenceSet($this->_model, $set_item_draft_mode);
		
		if(count($this->conditions)){
		    
			$array_values = array_values($this->conditions);
			
			$ids_array = $this->conditions[0]->getIdsArray();
			
			for($i=1; $i < count($array_values); $i++){
			    $new_ids_array = array_intersect($ids_array, $array_values[$i]->getIdsArray());
			    $ids_array = $new_ids_array;
			}
			
			foreach($ids_array as $item_id){
			    $ds->insertItemId($item_id);
			}
			
		}else{
			// no conditions specified - return all items of the model
			
			$ids_array = array();
			
			foreach($ids_array as $item_id){
			    $ds->insertItemId($item_id);
			}
		}
		
		return $ds;
		
	}
	
	public function doSelect($mode=9, $limit=''){
		
	    $mode = (int) $mode;
	    			
		if(in_array($mode, array(0,1,2,6,7,8))){
		    $value_field = 'itempropertyvalue_draft_content';
		    $set_item_draft_mode = true;
		}else{
		    $value_field = 'itempropertyvalue_content';
		    $set_item_draft_mode = false;
		}
		
		$allow_draft_items = $mode < 6;
		
		if(count($this->conditions)){
		
		    foreach($this->conditions as $condition){
			
				if($condition->getProperty()->getId() == SmartestCmsItem::ID){
			
				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_id ";
			
				}else if($condition->getProperty()->getId() == SmartestCmsItem::NAME){
			
				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_name ";
			    
			    }else if($condition->getProperty()->getId() == SmartestCmsItem::NUM_COMMENTS){

    				$sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_num_comments ";

                }else if($condition->getProperty()->getId() == SmartestCmsItem::NUM_HITS){

        			$sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_num_hits ";

                }else if($condition->getProperty()->getId() == self::TAGGED_WITH){
			        
			        $tag_name = SmartestStringHelper::toSlug($condition->getValueAsString());
    				$tag = new SmartestTag;
    				$sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id ";
    				
    				if($tag->findBy('name', $tag_name)){
    				    $ids = $tag->getSimpleItemIds($this->getSiteId(), $allow_draft_items, $this->_model->getId());
    				    $sql .= "AND Items.item_id IN ('".implode("', '", $ids)."')";
    				}else{
    				    SmartestLog::getInstance('system')->log("The tag '".$condition->getValueAsString()."' was used as a query condition but does not exist.");
    				}
			        
			    }else if($condition->getOperator() == self::NOT_TAGGED_WITH){
			        
			        $tag_name = SmartestStringHelper::toSlug($condition->getValueAsString());
    				$tag = new SmartestTag;
    				
    				if($tag->hydrateBy('name', $tag_name)){
    				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_id ";
    				    $ids = $tag->getSimpleItemIds($this->getSiteId(), $allow_draft_items, $this->_model->getId());
    				    $sql .= "NOT IN ('".implode("', '", $ids)."')";
    				}else{
    				    if(SM_DEVELOPER_MODE){
    				        // throw new SmartestException('Unknown tag: \''.$tag_name.'\' in SmartestQuery::doSelect()');
    				    }
    				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id ";
    				}
			    
				}else{
			        
			        // Standard item property field as added by the user
				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE ItemPropertyValues.itempropertyvalue_property_id='".$condition->getProperty()->getId()."' AND Items.item_id = ItemPropertyValues.itempropertyvalue_item_id AND ".$value_field.' ';
			    
				}
				
				$sql .= $condition->getSql();
			    
			    if($mode > 5){
			    
			        $sql .= " AND Items.item_public='TRUE'";
			    
		        }
		        
		        $sql .= " AND Items.item_deleted!='1'";
		        
		        if(in_array($mode, array(1,4,7,10))){
			    
			        $sql .= " AND Items.item_is_archived='1'";
			    
		        }else if(in_array($mode, array(2,5,8,11))){
		            
		            $sql .= " AND Items.item_is_archived!='1'";
		            
		        }
			    
			    if($this->getSiteId()){
			        $sql .= " AND (Items.item_site_id='".$this->getSiteId()."' OR Items.item_shared='1')";
			    }
			    
			    if(is_numeric($limit)){
			        $sql .= ' LIMIT '.$limit;
			    }
			    
			    $result = $this->database->queryToArray($sql);
			    $condition->setIdsArray($this->getSimpleIdsArray($result));
			
			}
		
		    return $this->createResultSet($conditions, $set_item_draft_mode);
		    
		}else{
		    
		    $sql = "SELECT DISTINCT item_id FROM Items WHERE Items.item_itemclass_id='".$this->_model->getId()."'";
		    
		    if($mode > 5){
		    
		        $sql .= " AND Items.item_public='TRUE'";
		    
	        }
	        
	        $sql .= " AND Items.item_deleted!='1'";
	        
	        if(in_array($mode, array(1,4,7,10))){
		    
		        $sql .= " AND Items.item_is_archived='1'";
		    
	        }else if(in_array($mode, array(2,5,8,11))){
	            
	            $sql .= " AND Items.item_is_archived!='1'";
	            
	        }
		    
		    if($this->getSiteId()){
		        $sql .= " AND (Items.item_site_id='".$this->getSiteId()."' OR Items.item_shared='1')";
		    }
		    
		    if(is_numeric($limit)){
		        $sql .= ' LIMIT '.$limit;
		    }
		    
		    $result = $this->database->queryToArray($sql);
		    return $this->createResultSet(array(), $draft);
		    
		}

	}
	
	public function convertOperator(){
	    
	}
	
	public function doSelectOne(){
		
	}
	
	//////////////////////////////// INIT FUNCTION //////////////////////////////////
	
	public static function init($force_regenerate=false, $site_id=''){
		
		if(!defined('SM_QUERY_INIT_COMPLETE') || $force_regenerate == true){
		
			$database =& SmartestPersistentObject::get('db:main');
			
			$du = new SmartestDataUtility;
			$models = $du->getModels(false, $site_id);
			
			foreach($models as $m){
			    
			    $m->init();
			    
			}
			
			define('SM_QUERY_INIT_COMPLETE', true);
		
		}
	}
}
