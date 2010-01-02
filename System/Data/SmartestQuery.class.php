<?php

class SmartestQuery{
	
	protected $database;
	protected $conditions = array();
	protected $_model;
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
	
	public function __construct($model_id, $site_id=''){
		
		$this->database =& SmartestPersistentObject::get('db:main');
		
		/* if(!SmartestCache::hasData('model_id_name_lookup', true)){
			self::init(true);
		} */
		
		$this->setSiteId($site_id);
		
		$models = SmartestCache::load('model_id_name_lookup', true);
		
		$m = new SmartestModel;
		
		if($m->find($model_id)){
		    $this->_model = $m;
		}else{
		    // ERROR: using non-existent model
		}
		
		/* if(in_array($model_id, $models)){
			
			$this->_model = new SmartestModel;
			$this->_model->hydrate($model_id);
			
		}else{
			// ERROR: using non-existent model
		} */
		
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
		if(!is_object($value) && !is_array($value)){
			$this->conditions[$property_id] = array('field'=>$property_id, 'value'=>$value, 'operator'=>$operator);
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
	
	private function createDataSet($conditions, $set_item_draft_mode){
		
		$ids_array = array();
		
		$class_name = $this->_model->getClassName();
		
		$ds = new SmartestQueryResultSet($this->_model->getId(), $this->_model->getClassName(), $set_item_draft_mode);
		
		if(count($this->conditions)){
		    
			$array_values = array_values($this->conditions);
			
			$ids_array = $array_values[0]['ids'];
			
			for($i=1; $i < count($array_values); $i++){
			    $new_ids_array = array_intersect($ids_array, $array_values[$i]['ids']);
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
	
	public function doSelect($mode=9){
		
	    // var_dump($draft);
	    
	    $mode = (int) $mode;
	    
		// if($forcedb){
			// do it the slow way, getting ids of matching rows and hydrating them
			
			if(in_array($mode, array(0,1,2,6,7,8))){
			    $value_field = 'itempropertyvalue_draft_content';
			    $set_item_draft_mode = true;
			}else{
			    $value_field = 'itempropertyvalue_content';
			    $set_item_draft_mode = false;
			}
			
			$allow_draft_items = $mode < 6;
			
			if(count($this->conditions)){
			
			    foreach($this->conditions as $property_id => $condition){
				
    				if($condition['field'] == SmartestCmsItem::ID){
				
    				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' AND Items.item_id ";
				
    				}else if($condition['field'] == SmartestCmsItem::NAME){
				
    				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' AND Items.item_name ";
				    
				    }else if($condition['field'] == SmartestCmsItem::NUM_COMMENTS){

        				$sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' AND Items.item_num_comments ";

                    }else if($condition['field'] == SmartestCmsItem::NUM_HITS){

            			$sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' AND Items.item_num_hits ";

                    }else if($condition['operator'] == self::TAGGED_WITH){
				        
				        $tag_name = SmartestStringHelper::toSlug($condition['value']);
        				$tag = new SmartestTag;
        				$sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' ";
        				
        				if($tag->hydrateBy('name', $tag_name)){
        				    $ids = $tag->getSimpleItemIds($this->getSiteId(), $allow_draft_items, $this->_model->getId());
        				    $sql .= "AND Items.item_id IN ('".implode("', '", $ids)."')";
        				}else{
        				    if(SM_DEVELOPER_MODE){
        				        // throw new SmartestException('Unknown tag: \''.$tag_name.'\' in SmartestQuery::doSelect()');
        				    }
        				    
        				    // $sql .= "AND Items.item_id ='x'";
        				}
				        
				    }else if($condition['operator'] == self::NOT_TAGGED_WITH){
				        
				        $tag_name = SmartestStringHelper::toSlug($condition['value']);
        				$tag = new SmartestTag;
        				
        				
        				if($tag->hydrateBy('name', $tag_name)){
        				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' AND Items.item_id ";
        				    $ids = $tag->getSimpleItemIds($this->getSiteId(), $allow_draft_items, $this->_model->getId());
        				    $sql .= "NOT IN ('".implode("', '", $ids)."')";
        				}else{
        				    if(SM_DEVELOPER_MODE){
        				        // throw new SmartestException('Unknown tag: \''.$tag_name.'\' in SmartestQuery::doSelect()');
        				    }
        				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->_model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' ";
        				}
				    
    				}else{
				
    				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE ItemPropertyValues.itempropertyvalue_property_id='$property_id' AND Items.item_deleted != '1' AND Items.item_id = ItemPropertyValues.itempropertyvalue_item_id AND ".$value_field.' ';
				    
    				}
				
    				switch($condition['operator']){
			
    				    case 0:
        				$sql .= "='".mysql_real_escape_string($condition['value'])."'";
        				break;
			
        				case 1:
        				$sql .= " != '".mysql_real_escape_string($condition['value'])."'";
        				break;
			
        				case 2:
        				$sql .= " LIKE '%".mysql_real_escape_string($condition['value'])."%'";
        				break;
			
        				case 3:
        				$sql .= " NOT LIKE '%".mysql_real_escape_string($condition['value'])."%'";
        				break;
			
        				case 4:
        				$sql .= " LIKE '".mysql_real_escape_string($condition['value'])."%'";
        				break;
			
        				case 5:
        				$sql .= " LIKE '%".mysql_real_escape_string($condition['value'])."'";
        				break;
    				
        				case 6:
        				$sql .= " > '".mysql_real_escape_string($condition['value'])."'";
        				break;
    				
        				case 7:
        				$sql .= " < '".mysql_real_escape_string($condition['value'])."'";
        				break;
        				
        				// For 8 and 9 involving tagging, see above
        				
    		        }
				    
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
				    
				    $result = $this->database->queryToArray($sql);
				    $this->conditions[$property_id]['ids'] = $this->getSimpleIdsArray($result);
				
    			}
    		
			    return $this->createDataSet($conditions, $set_item_draft_mode);
			    
			}else{
			    
			    $sql = "SELECT DISTINCT item_id FROM Items WHERE Items.item_itemclass_id='".$this->_model->getId()."'";
			    $result = $this->database->queryToArray($sql);
			    return $this->createDataSet(array(), $draft);
			    
			}
			
		// }
	}
	
	public function doSelectOne(){
		
	}
	
	//////////////////////////////// INIT FUNCTION //////////////////////////////////
	
	public static function init($force_regenerate=false, $site_id=''){
		
		// print_r(SmartestCache::load('models_query', true));
		
		if(!defined('SM_QUERY_INIT_COMPLETE') || $force_regenerate == true){
		
			$database =& SmartestPersistentObject::get('db:main');
			
			$du = new SmartestDataUtility;
			$models = $du->getModels(false, $site_id);
			
			// print_r($models);
			
			foreach($models as $m){
			    
			    $m->init();
			    
			}
			
			define('SM_QUERY_INIT_COMPLETE', true);
			
			/* if(SmartestCache::hasData('model_id_name_lookup', true) && $force_regenerate != true){
				
				$models = SmartestCache::load('model_id_name_lookup', true);
				
				foreach($models as $constant_name => $constant_value){
				
					if(!defined($constant_name)){
 						define($constant_name, $constant_value, true);
 					}
 				
				}
			
			}else{
			
				$sql = "SELECT itemclass_id, itemclass_name, itemclass_plural_name FROM ItemClasses";
 				$results = $database->queryToArray($sql);
 			
 				if(is_array($results)){
 				
 					$models = array();
 					
 					foreach($results as $item_class){
 					
 						$constant_name = SmartestStringHelper::toCamelCase($item_class["itemclass_name"]);
 						
 					
 						if(!defined($constant_name)){
 							define($constant_name, $item_class["itemclass_id"], true);
 						}
 						
 						$models[$constant_name] = $item_class["itemclass_id"];
 						
 					}
 					
 					SmartestCache::save('model_id_name_lookup', $models, -1, true);
 				}
			} */
			
			/* if(SmartestCache::hasData('model_class_names', true) && SmartestCache::hasData('model_names', true)){
			
				$modelclassnames = SmartestCache::load('model_class_names', true);
				$modelnames = SmartestCache::load('model_names', true);
			
			}else{
				
				$sql = "SELECT itemclass_id, itemclass_name, itemclass_plural_name FROM ItemClasses";
 				$results = $database->queryToArray($sql);
 				
 				$modelclassnames = array();
 				$modelnames = array();
 				
 				foreach($results as $item_class){
 					
 					$modelclassnames[$item_class["itemclass_id"]] = SmartestStringHelper::toCamelCase($item_class["itemclass_name"]);
 					$modelnames[$item_class["itemclass_id"]] = $item_class["itemclass_name"];
 						
 				}
 				
 				SmartestCache::save('model_class_names', $modelclassnames, -1, true);
 				SmartestCache::save('model_names', $modelnames, -1, true);
				
			} */
			
			// print_r($modelnames);
			
			/* foreach($modelclassnames as $class_id => $class_name){
				
				
 				
			} */
		
		}else{
			// init has already taken place - do nothing
		}
	}
}
