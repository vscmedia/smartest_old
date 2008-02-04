<?php

class SmartestQuery{
	
	protected $database;
	protected $conditions = array();
	protected $model;
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
	
	public function __construct($model_id, $site_id=''){
		
		$this->database =& SmartestPersistentObject::get('db:main');
		
		if(!SmartestCache::hasData('model_id_name_lookup', true)){
			self::init(true);
		}
		
		$this->setSiteId($site_id);
		
		$models = SmartestCache::load('model_id_name_lookup', true);
		
		if(in_array($model_id, $models)){
			
			$this->model = new SmartestModel;
			$this->model->hydrate($model_id);
			
		}else{
			// ERROR: using non-existent model
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
	
	static function init($force_regenerate=false){
		
		if(!defined('SM_QUERY_INIT_COMPLETE') || $force_regenerate == true){
		
			$database =& SmartestPersistentObject::get('db:main');
			
			if(SmartestCache::hasData('model_id_name_lookup', true) && $force_regenerate != true){
				
				$models = SmartestCache::load('model_id_name_lookup', true);
				
				// print_r($models);
				
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
 					
 					// print_r($models);
 					
 					SmartestCache::save('model_id_name_lookup', $models, -1, true);
 				}
			}
			
			if(SmartestCache::hasData('model_class_names', true)){
			
				$modelnames = SmartestCache::load('model_class_names', true);
			
			}else{
				
				$sql = "SELECT itemclass_id, itemclass_name, itemclass_plural_name FROM ItemClasses";
 				$results = $database->queryToArray($sql);
 				$modelnames = array();
 				
 				foreach($results as $item_class){
 					
 					$modelnames[$item_class["itemclass_id"]] = SmartestStringHelper::toCamelCase($item_class["itemclass_name"]);
 						
 				}
 				
 				SmartestCache::save('model_class_names', $modelnames, -1, true);
				
			}
			
			foreach($modelnames as $class_id => $class_name){
				
				// echo 'Loading Auto OM Class: auto'.$class_name.'<br />';
				
				if(is_file(SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php')){
					include SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php';
				}else{
					// build auto class
					if(SmartestObjectModelHelper::buildAutoClassFile($class_id, $class_name)){
						include SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php';
					}else{
						throw new SmartestException('Could not auto-generate model class: '.$class_name, SM_ERROR_MODEL);
					}
				}
				
				// echo 'Loading OM Class: '.$class_name.'<br />';
					
				if(is_file(SM_ROOT_DIR.'Library/ObjectModel/'.$class_name.'.class.php')){
					include SM_ROOT_DIR.'Library/ObjectModel/'.$class_name.'.class.php';
				}else{
					// build extensible class
					if(SmartestObjectModelHelper::buildClassFile($class_id, $class_name)){
						include SM_ROOT_DIR.'Library/ObjectModel/'.$class_name.'.class.php';
					}else{
						throw new SmartestException('Could not auto-generate model class: '.$class_name, SM_ERROR_MODEL);
					}
				}
 				
			}
			
			define('SM_QUERY_INIT_COMPLETE', true);
		
		}else{
			// init has already taken place - do nothing
		}
	}
	
	public function getModel(){
		return $this->model;
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
	
	private function createDataSet($conditions, $draft){
		
		$ids_array = array();
		
		$models = array_flip(SmartestCache::load('model_id_name_lookup', true));
		
		// print_r($models);
		
		$class_name = $models[$this->model->getId()];
		
		// var_dump($this->model->getId());
		
		$ds = new SmartestQueryResultSet($this->model->getId(), $this->model->getClassName(), $draft);
		
		if(count($this->conditions)){
		    
			$array_values = array_values($this->conditions);
			
			// print_r($array_values);
			
			$ids_array = $array_values[0]['ids'];
			
			for($i=1; $i < count($array_values); $i++){
			    $new_ids_array = array_intersect($ids_array, $array_values[$i]['ids']);
			    // 
			    $ids_array = $new_ids_array;
			}
			
			foreach($ids_array as $item_id){
			    $ds->insertItemId($item_id);
			}
			
			// print_r($ds);
			
			
			// return $ds;
			/* foreach($av[0]['ids'] as $item_id){
			    // echo $class_name;
				$item = new $class_name;
				$item->hydrate($item_id);
				$dataset->insert($item);
			} */
			
		}else{
			// no conditions specified - return all items of the model
			
			$ids_array = array();
			
			foreach($ids_array as $item_id){
			    $ds->insertItemId($item_id);
			}
		}
		
		return $ds;
		
	}
	
	public function doSelect($draft=false){
		
		// if($forcedb){
			// do it the slow way, getting ids of matching rows and hydrating them
			
			if($draft){
			    $value_field = 'itempropertyvalue_draft_content';
			}else{
			    $value_field = 'itempropertyvalue_content';
			}
			
			if(count($this->conditions)){
			
			    foreach($this->conditions as $property_id => $condition){
				
    				if($condition['field'] == SmartestCmsItem::ID){
				
    				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' AND Items.item_id ";
				
    				}else if($condition['field'] == SmartestCmsItem::NAME){
				
    				    $sql = "SELECT DISTINCT itempropertyvalue_item_id FROM Items, ItemPropertyValues WHERE Items.item_itemclass_id='".$this->model->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted != '1' AND Items.item_name ";
				
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
    		        }
				    
				    $sql .= " AND Items.item_public='TRUE'";
				    
				    // var_dump($this->getSiteId());
				    
				    if($this->getSiteId()){
				        $sql .= " AND (Items.item_site_id='".$this->getSiteId()."' OR Items.item_shared='1')";
				    }
				    
				    // echo $sql;
				    
    			    $result = $this->database->queryToArray($sql);
				
    				$this->conditions[$property_id]['ids'] = $this->getSimpleIdsArray($result);
				
    			}
    		
			    return $this->createDataSet($conditions, $draft);
			    
			}else{
			    
			    $sql = "SELECT DISTINCT item_id FROM Items WHERE Items.item_itemclass_id='".$this->model->getId()."'";
			    $result = $this->database->queryToArray($sql);
			    return $this->createDataSet(array(), $draft);
			    
			}
			
		// }
	}
	
	public function doSelectOne(){
		
	}
}