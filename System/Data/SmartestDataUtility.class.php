<?php

class SmartestDataUtility{

	protected $database;
	
	public static $data_types;
	public static $asset_types;
	public static $assetclass_types;
	private static $_type_to_class_converter = array();
	
	public function __construct($connection_name = ''){
        
        if(strlen($connection_name)){
            $this->database = SmartestDatabase::getInstance($connection_name);
        }else{
            if(isset($_SESSION)){
                $this->database = SmartestPersistentObject::get('db:main');
            }else{
                throw new SmartestException("Tried to construct a SmartestDataUtility object with neither an active session or a specified connection name;");
            }
        }
        
    }
    
    public function getModels($simple = false, $site_id='', $force_regenerate=false){
	    
	    if(is_numeric($site_id)){
	        $cache_name = 'models_query_site_'.$site_id;
	    }else{
	        $cache_name = 'models_query';
	    }
	    
	    if(!SmartestCache::hasData($cache_name, true) || $force_regenerate || $simple){
		
		    if($simple){
    			$sql = "SELECT itemclass_id FROM ItemClasses";
    		}else{
    			$sql = "SELECT * FROM ItemClasses";
    		}
		
    		$sql .= " WHERE itemclass_type='SM_ITEMCLASS_MODEL'";
		
    		if(is_numeric($site_id)){
    		    $sql .= " AND (itemclass_site_id='".$site_id."' OR itemclass_shared='1')";
    		}
		
    		$sql .= ' ORDER BY itemclass_name';
    		
    		$result = $this->database->queryToArray($sql, true);
    		
    		if($simple){
    		    return $result;
    		}else{
    		    SmartestCache::save($cache_name, $result, -1, true);
		    }
		
	    }else{
	        $result = SmartestCache::load($cache_name, true);
	    }
		
		$model_objects = array();
		
		foreach($result as $model){
			$m = new SmartestModel;
			$m->hydrate($model);
			$model_objects[] = $m;
		}
		
		return $model_objects;
		
	}
	
	public function getModelIds($site_id=''){
	    
	    $result = $this->getModels(true, $site_id);
	    $ids = array();
	    
	    foreach($result as $r){
	        $ids[] = $r['itemclass_id'];
	    }
	    
	    return $ids;
	    
	}
	
	public function getModelsWithPrimaryKeyIndices($site_id='', $force_regenerate=false){
	    
	    $m = $this->getModels(false, $site_id, $force_regenerate);
	    $models = array();
	    
	    foreach($m as $model){
	        $models[$model->getId()] = $model;
	    }
	    
	    return $models;
	    
	}
	
	public function getSharedModels(){
	    
	    $sql = "SELECT * FROM ItemClasses WHERE itemclass_shared = 1";
	    $result = $this->database->queryToArray($sql);
	    
	    $model_objects = array();
		
		foreach($result as $model){
			$m = new SmartestModel;
			$m->hydrate($model);
			$model_objects[] = $m;
		}
		
		return $model_objects;
	    
	}
	
	public function getModelPluralNamesLowercase($site_id='', $reverse=false){
	    
	    $models = $this->getModels(false, $site_id);
	    $names = array();
	    
	    foreach($models as $m){
	        if($reverse){
	            $names[$m->getId()] = SmartestStringHelper::toVarName($m->getPluralName());
	        }else{
	            $names[SmartestStringHelper::toVarName($m->getPluralName())] = $m->getId();
            }
	    }
	    
	    return $names;
	    
	}
	
	public function getModelNamesLowercase($site_id=''){
	    
	    $models = $this->getModels(false, $site_id);
	    $names = array();
	    
	    foreach($models as $m){
	        $names[SmartestStringHelper::toVarName($m->getName())] = $m->getId();
	    }
	    
	    return $names;
	    
	}
	
	public function getModelsAsArrays($simple=false, $site_id=''){
	    
	    $models = $this->getModels(false, $site_id);
	    $arrays = array();
	    
	    foreach($models as $m){
	        $arrays[] = $m->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getDataSets($simple = false, $site_id=''){
		
		if($simple){
			$sql = "SELECT set_id FROM Sets";
		}else{
			$sql = "SELECT * FROM Sets";
		}
		
		if(is_numeric($site_id)){
		    $sql .= " WHERE (set_site_id='".$site_id."' OR set_shared='1') AND (set_data_source_site_id='".$site_id."' OR set_data_source_site_id='CURRENT' OR set_data_source_site_id='ALL')";
		}
		
		$sql .= " AND set_type IN ('DYNAMIC', 'STATIC', 'SM_SET_ITEMS_DYNAMIC', 'SM_SET_ITEMS_STATIC')";
		
		$sql .= " ORDER BY set_name";
		
		$result = $this->database->queryToArray($sql);
		
		if($simple){
			
			return $result;
			
		}else{
			
		    $set_objects = array();
			
		    foreach($result as $set){
			    $m = new SmartestCmsItemSet;
			    $m->hydrate($set);
			    $set_objects[] = $m;
		    }
			
		    return $set_objects;
		
		}
	}
	
	public function getDataSetsAsArrays($simple = false, $site_id='', $get_contents=false){
	    
	    $sets = $this->getDataSets($simple, $site_id);
	    $arrays = array();
	    
	    foreach($sets as $s){
	        if(is_object($s)){
	            $arrays[] = $s->__toArray($get_contents);
            }else{
                // ??? something to do with $simple ???
            }
	    }
	    
	    return $arrays;
	}
	
	public static function getForeignKeyFilterOptions($data_type_code){
	    
	    $data_types = self::getDataTypes();
	    
	    if(isset($data_types[$data_type_code])){
	        $dt = $data_types[$data_type_code];
	        
	        if(($dt['valuetype'] == 'foreignkey' || $dt['valuetype'] == 'manytomany') && isset($dt['filter']['typesource'])){
	            
	            $t = $dt['filter']['typesource']['type'];
	            
	            if($t == 'smartest:assettypes'){
	                
	                /* $options = self::getAssetTypes();
	                return $options; */
	                
	                $options = array();
	                
	                $options['asset_types'] = SmartestDataUtility::getAssetTypes();
            	    $options['placeholder_types'] = SmartestDataUtility::getAssetClassTypes(true);

            	    return $options;
            	    
	            }else{
	                
	                $database = SmartestDatabase::getInstance('SMARTEST');
	                $sql = "SELECT * FROM ".$dt['filter']['typesource']['table'];
	                
	                // add WHERE conditions here later
	                if(isset($dt['filter']['typesource']['orderfield'])){
	                    $sql .= " ORDER BY ".$dt['filter']['typesource']['orderfield'];
	                }
	                
	                $c = $dt['filter']['typesource']['class'];
	                
	                if(strlen($c) && class_exists($c)){
	                    
	                    $result = $database->queryToArray($sql);
	                    $options = array();
	                    
	                    foreach($result as $r){
	                        $type = new $c;
	                        $type->hydrate($r);
	                        $options[] = $type;
	                    }
	                    
	                    return $options;
	                    
                    }else{
                        throw new SmartestException("Data type '".$data_type_code."' foreign key entity type does not have a value class.", SM_ERROR_SMARTEST_INTERNAL);
                    }
	            }
	        }else{
	            throw new SmartestException("Tried to get foreign key filter options for non foreign key data type: ".$data_type_code, SM_ERROR_SMARTEST_INTERNAL);
	        }
	        
	    }else{
	        throw new SmartestException("Tried to get foreign key filter options for non existent data type: ".$data_type_code, SM_ERROR_SMARTEST_INTERNAL);
	    }
	    
	}
	
	public function getTags(){
	    
	    $sql = "SELECT * FROM Tags ORDER BY tag_name";
	    $result = $this->database->queryToArray($sql);
	    $tags = array();
	    
	    foreach($result as $raw_tag_array){
	        $tag = new SmartestTag;
	        $tag->hydrate($raw_tag_array);
	        $tags[] = $tag;
	    }
	    
	    return $tags;
	    
	}
	
	public function getTagsAsArrays(){
	    
	    $tags = $this->getTags();
	    $tags_as_arrays = array();
	    
	    foreach($tags as $tag){
	        $tags_as_arrays[] = $tag->__toArray();
	    }
	    
	    return $tags_as_arrays;
	    
	}
	
	public function getSites(){
	    
	    $sql = "SELECT * FROM Sites";
	    $result = $this->database->queryToArray($sql);
	    $sites = array();
	    
	    foreach($result as $s){
	        $site = new SmartestSite;
	        $site->hydrate($s);
	        $sites[] = $site;
	    }
	    
	    return $sites;
	    
	}
	
	public function getSitesAsArrays(){
	    
	    $sites = $this->getSites();
	    $arrays = array();
	    
	    foreach($sites as $s){
	        $arrays[] = $s->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function isValidModelName($string){
	    
	    $constant_names = array_keys(get_defined_constants());
	    $class_names = get_declared_classes();
	    
	    if(in_array(SmartestStringHelper::toCamelCase($string), $class_names) || in_array(SmartestStringHelper::toConstantName($string), $constant_names)){
	        return false;
	    }else{
	        return true;
	    }
	}
	
	public function modelNameIsAvailable($name, $site_id, $shared){
	    
	    if($shared){
	        $models = $this->getSharedModels();
	    }else{
	        $models = $this->getModels(false, $site_id);
	    }
	    
	    foreach($models as $m){
	        if($m->getName() == $name){
	            return false;
	        }
	    }
	    
	    return true;
	    
	}
	
	public function flushModelsCache(){
	    
	    SmartestCache::clear('models_query', true);
	    
	    foreach($this->getSites() as $s){
	        $cache_name = 'models_query_site_'.$s->getId();
	        SmartestCache::clear($cache_name, true);
	    }
	    
	}
	
	static function isValidPropertyName($string, $model=''){
	    
	    if((strlen($string) < 2) || is_numeric($string{0})){
	        return false;
	    }else{
	        return true;
	    }
	    
	    /*$get_function = 'get'.SmartestStringHelper::toCamelCase($string);
	    
	    if($model instanceof SmartestModel){
	        $methods = get_class_methods(get_class($model));
	    }else if(is_string($model && class_exists($model))){
	        $methods = get_class_methods($model);
	    } */
	    
	    // var_dump($check_against_model);
	    
	    /*if(in_array(SmartestStringHelper::toCamelCase($string), $class_names) || in_array(SmartestStringHelper::toConstantName($string), $constant_names)){
	        return false;
	    }else{
	        return true;
	    } */
	}
	
	public static function getDataTypesXmlData(){
	    
	    $file_path = SM_ROOT_DIR.'System/Core/Types/datatypes.xml';
	    
	    if(SmartestCache::hasData('datatypes_xml_file_hash', true)){
	        
	        $old_hash = SmartestCache::load('datatypes_xml_file_hash', true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save('datatypes_xml_file_hash', $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['type'];
	            SmartestCache::save('datatypes_xml_file_data', $data, -1, true);
            }else{
                $data = SmartestCache::load('datatypes_xml_file_data', true);
            }
            
            // return $data;
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('datatypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('datatypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	public static function getDataTypes($usage_filter=''){
	    
	    if(self::$data_types){
	        
	        return self::$data_types;
	        
	    }else{
	        
	        $data = self::getDataTypesXmlData();
	    
    	    $raw_types = $data;
    	    $types = array();
	    
    	    $usage_filter = strlen($usage_filter) ? $usage_filter : null;
	    
    	    foreach($raw_types as $raw_type){
	        
    	        if(isset($raw_type['filter'])){
	            
    	            // regularize conditions
    	            if(isset($raw_type['filter']['condition'])){
    	                if(isset($raw_type['filter']['condition']['field'])){
    	                    $raw_type['filter']['condition'] = array($raw_type['filter']['condition']);
    	                }
    	            }else{
    	                $raw_type['filter']['condition'] = array();
    	            }
	            
    	            // regularize option set types
    	            if(isset($raw_type['filter']['optionsettype'])){
	                
    	                if(isset($raw_type['filter']['optionsettype']['id'])){
    	                    $raw_type['filter']['optionsettype'] = array($raw_type['filter']['optionsettype']['id'] => $raw_type['filter']['optionsettype']);
    	                }else{
    	                    $osts = array();
	                    
    	                    foreach($raw_type['filter']['optionsettype'] as $ost){
    	                        $osts[$ost['id']] = $ost;
    	                    }
	                    
    	                    $raw_type['filter']['optionsettype'] = $osts;
    	                }
	                
    	                foreach($raw_type['filter']['optionsettype'] as &$ost){
	                    
    	                    if(isset($ost['condition'])){
    	                    
        	                    if(isset($ost['condition']['field'])){
            	                    $ost['condition'] = array($ost['condition']); // $raw_type['filter']['optionsettype'] = array($raw_type['filter']['optionsettype']);
            	                }
        	                
        	                }else{
        	                    $ost['condition'] = array();
        	                }
	                    
    	                }
	                
    	            }else{
    	                $raw_type['filter']['optionsettype'] = array();
    	            }
	            
    	        }
	        
    	        if($usage_filter){
    	            $usages = explode(',', $raw_type['usage']);
    	            if(in_array($usage_filter, $usages)){
    	                $types[$raw_type['id']] = $raw_type;
    	            }
    	        }else{
    	            $types[$raw_type['id']] = $raw_type;
                }
    	    }
    	    
    	    self::$data_types = $types;
	    
        }
	    
	    return $types;
	}
	
	/* static function getAssetTypesXmlData(){
	    
	    $file_path = SM_ROOT_DIR.'System/Core/Types/assettypes.xml';
	    
	    if(SmartestCache::hasData('assettypes_xml_file_hash', true)){
	        
	        $old_hash = SmartestCache::load('assettypes_xml_file_hash', true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save('assettypes_xml_file_hash', $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['type'];
	            SmartestCache::save('assettypes_xml_file_data', $data, -1, true);
            }else{
                $data = SmartestCache::load('assettypes_xml_file_data', true);
            }
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('assettypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('assettypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	} */
	
	static function getAssetTypes(){
	    
	    if(self::$asset_types){
	        
	        return self::$asset_types;
	        
	    }else{
	    
    	    $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/assettypes.yml');
	    
    	    $types = $data['type'];
	    
    	    foreach($types as $id=>$raw_type){
	        
    	        if(isset($types[$id]['param'])){
                    if(isset($types[$id]['param']['name'])){
                        $types[$id]['param'] = array($types[$id]['param']);
                    }
                }else{
                    $types[$id]['param'] = array();
                }
    	    }
    	    
    	    self::$asset_types = $types;
    	    
        }
	    
	    return $types;
	}
	
	/* static function getAssetTypes_old(){
	    
	    $data = self::getAssetTypesXmlData();
	    
	    $types = array();
	    $raw_data = $data;
	    
	    foreach($raw_data as $raw_type){
	        
	        $types[$raw_type['id']] = $raw_type;
	        
	        if(!defined($raw_type['id'])){
	            define($raw_type['id'], $raw_type['id']);
	        }
	        
	        if(is_array($types[$raw_type['id']]['suffix'])){
	            
	            if(isset($types[$raw_type['id']]['suffix']['mime'])){
	                
	                // $key = $types[$raw_type['id']]['suffix']['_content'];
	                $suffix = $types[$raw_type['id']]['suffix'];
	                $types[$raw_type['id']]['suffix'] = array();
	                $types[$raw_type['id']]['suffix'][0] = $suffix;
	                
	            }
	        
            }
            
            // if(isset($types[$raw_type['id']]['param'])){
            if(isset($types[$raw_type['id']]['param'])){
                if(isset($types[$raw_type['id']]['param']['name'])){
                    $types[$raw_type['id']]['param'] = array($types[$raw_type['id']]['param']);
                    // $types[$id]['param'] = array($types[$id]['param']);
                }
            }else{
                $types[$raw_type['id']]['param'] = array();
                // $types[$id]['param'] = array();
            }
	    }
	    
	    // print_r($types);
	    return $types;
	    
	} */
	
	public static function getAssetClassTypesXmlData(){
	    
	    $file_path = SM_ROOT_DIR.'System/Core/Types/placeholdertypes.xml';
	    
	    if(SmartestCache::hasData('placeholdertypes_xml_file_hash', true)){
	        
	        $old_hash = SmartestCache::load('placeholdertypes_xml_file_hash', true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save('placeholdertypes_xml_file_hash', $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['type'];
	            SmartestCache::save('placeholdertypes_xml_file_data', $data, -1, true);
            }else{
                $data = SmartestCache::load('placeholdertypes_xml_file_data', true);
            }
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('placeholdertypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('placeholdertypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	public static function getAssetClassTypes($ignore_hide=false){
	    
	    if(self::$assetclass_types){
	        
	        return self::$assetclass_types;
	    
	    }else{
	        
	        $data = self::getAssetClassTypesXmlData();
	    
    	    $raw_types = $data;
    	    $types = array();
	    
    	    foreach($raw_types as $raw_type){
	        
    	        if(!$ignore_hide || (!isset($raw_type['hide']) || !SmartestStringHelper::toRealBool($raw_type['hide']))){
	        
    	            $types[$raw_type['id']] = $raw_type;
	        
        	        if(!defined($raw_type['id'])){
        	            define($raw_type['id'], $raw_type['id']);
        	        }
	        
        	        if(!is_array($types[$raw_type['id']]['accept'])){
	            
        	            $types[$raw_type['id']]['accept'] = array($types[$raw_type['id']]['accept']);
	        
                    }
            
                }
            
    	    }
    	    
    	    self::$assetclass_types = $types;
	    
        }
	    
	    return $types;
	}
	
	public static function loadBasicTypes(){
		
		$available_objects = SmartestCache::load('smartest_available_type_objects', true);
		
		$singlefile = '';
		
		// load the basic types if this hasn't already been done
		$object_types = array();
		
		if($res = opendir(SM_ROOT_DIR.'System/Data/BasicTypes/')){
		    
		    $object_type_cache_string = '';
		    
			while (false !== ($file = readdir($res))) {
    		
    			if(preg_match('/^Smartest([A-Z0-9]\w+)\.class\.php$/', $file, $matches)){
    				if($matches[1] != 'Object'){
    					$object_type = array();
    					$object_type['name'] = $matches[1];
    					$object_type['file'] = SM_ROOT_DIR.'System/Data/BasicTypes/'.$matches[0];
    					$object_type_cache_string .= sha1_file($object_type['file']);
    					$object_types[] = $object_type;
    				}
    			}
    		
			}
		
			closedir($res);
			
			$type_object_cache_hash = sha1($object_type_cache_string);
			
		}
	    
	    $use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_type_objects_hash', true) != $system_helper_cache_hash || !is_file(SM_ROOT_DIR.'System/Cache/Includes/SmartestBasicTypeClasses.cache.php')));
	    
	    if($use_cache){
	        if($rebuild_cache){
	            $singlefile .= file_get_contents(SM_ROOT_DIR.'System/Data/BasicTypes/SmartestObject.class.php');
            }
	    }else{
	        include SM_ROOT_DIR.'System/Data/BasicTypes/SmartestObject.class.php';
	    }
	
		foreach($object_types as $h){
			
			if(is_file($h['file'])){
				if($use_cache){
				    if($rebuild_cache){
				        $singlefile .= file_get_contents($h['file']);
			        }else{
    			        // don't need to include anything because types are already in cache
    			    }
				}else{
				    // Include the original file rather than the cache
				    include $h['file'];
				}
			}else{
				// File was there amoment ago but has now disappeared (???)
			}
		}
		
		if($rebuild_cache){
	        
	        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
			$singlefile = "<"."?php\n\n// Cache of basic type classes\n\n// Auto-generated by SmartestDataUtility - Do Not Edit\n".$singlefile;
		    
		    file_put_contents(SM_ROOT_DIR.'System/Cache/Includes/SmartestBasicTypeClasses.cache.php', $singlefile);
		    
		    SmartestCache::save('smartest_type_objects_hash', $type_object_cache_hash, -1, true);
		    
		}
		
		if($use_cache){
	        include SM_ROOT_DIR.'System/Cache/Includes/SmartestBasicTypeClasses.cache.php';
	    }
	
	}
	
	public static function loadExtendedTypes(){
	    
	}

	public static function stripSlashes($value){
		return is_array($value) ? array_map(array('SmartestDataUtility','stripSlashes'), $value) : utf8_encode(stripslashes($value));
	}
	
	public static function getDataType($type_code){
	    
	    $types = self::getDataTypes();
	    
	    if(isset(self::$_type_to_class_converter[$type_code])){
            return self::$_type_to_class_converter[$type_code];
        }else{
            if(isset($types[$type_code]) && isset($types[$type_code])){
                self::$_type_to_class_converter[$type_code] = $types[$type_code];
    	        return $types[$type_code];
            }else{
                return null;
            }
        }
	    
	}
	
	public static function getClassForDataType($as_type){
	    
	    $types = self::getDataTypes();
	    
	    if(isset(self::$_type_to_class_converter[$as_type])){
            return self::$_type_to_class_converter[$as_type];
        }else{
            if(isset($types[$as_type]) && isset($types[$as_type]['class'])){
                self::$_type_to_class_converter[$as_type] = $types[$as_type]['class'];
    	        return $types[$as_type]['class'];
            }else{
                return null;
            }
        }
	    
	}
	
	public static function getBlankObjectForDataType($as_type){
	    
	    if($class = self::getClassForDataType($as_type)){
	        
	        if(!class_exists($class)){
                throw new SmartestException("Class ".$class." required for handling properties of type ".$t['id']." does not exist.");
            }
            
            return new $class;
	        
	    }else{
	        
	        throw new SmartestException("Data type code ".$as_type." does not correspond to any class.");
	        
	    }
	    
	}
	
	public static function objectize($value, $as_type, $fk_field='id'){
	    
	    if($object = self::getBlankObjectForDataType($as_type)){
	        
	        if($object instanceof SmartestStorableValue){
	            // print_r ($value);
	            if($object->hydrateFromStorableFormat($value)){
	                return $object;
	            }else{
	                // object couldn't be hydrated
	            }
	        }else{
	            throw new SmartestException("Class ".get_class($object)." must implement interface SmartestStorableValue");
	        }
	        
	    }else{
	        
	        throw new SmartestException("Could not objectize value ".$value." as type '".$as_type."'");
	        
	    }
	    
	}
	
	public static function objectizeFromRawFormData($value, $as_type, $fk_field='id'){
	    
	    if($object = self::getBlankObjectForDataType($as_type)){
	        
	        if($object instanceof SmartestStorableValue){
	            // echo get_class($object);
	            // var_dump($value);
	            if($object->hydrateFromFormData($value)){
	                // print_r($object);
	                return $object;
	            }else{
	                // object couldn't be hydrated
	                // echo "#fail";
	            }
	        }else{
	            throw new SmartestException("Class ".get_class($object)." must implement interface SmartestStorableValue");
	        }
	        
	    }else{
	        
	        throw new SmartestException("Could not objectize value ".$value." as type '".$as_type."'");
	        
	    }
	    
	}
	
}
