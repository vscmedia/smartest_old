<?php

class SmartestDataUtility{

	protected $database;
	
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
	
	public function getModels($simple = false, $site_id=''){
		
		if($simple){
			$sql = "SELECT itemclass_id FROM ItemClasses";
		}else{
			$sql = "SELECT * FROM ItemClasses";
		}
		
		if(is_numeric($site_id)){
		    $sql .= " WHERE itemclass_site_id='".$site_id."'";
		}
		
		$sql .= ' ORDER BY itemclass_name';
		
		$result = $this->database->queryToArray($sql);
		
		if($simple){
			
			return $result;
			
		}else{
			
		    $model_objects = array();
			
			foreach($result as $model){
				$m = new SmartestModel;
				$m->hydrate($model);
				$model_objects[] = $m;
			}
			
			return $model_objects;
		}
	}
	
	public function getModelsAsArrays($simple=false, $site_id=''){
	    
	    $models = $this->getModels($simple, $site_id);
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
		    $sql .= " WHERE (set_site_id='".$site_id."' OR set_shared='1')";
		}
		
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
	        
	        if($dt['valuetype'] == 'foreignkey' && isset($dt['filter']['typesource'])){
	            
	            $t = $dt['filter']['typesource']['type'];
	            
	            if($t == 'smartest:assettypes'){
	                $options = self::getAssetTypes();
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
	
	static function isValidModelName($string){
	    
	    $constant_names = array_keys(get_defined_constants());
	    $class_names = get_declared_classes();
	    // $reserved_names = array_merge($constant_names, $class_names);
	    
	    if(in_array(SmartestStringHelper::toCamelCase($string), $class_names) || in_array(SmartestStringHelper::toConstantName($string), $constant_names)){
	        return false;
	    }else{
	        return true;
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
	
	static function getDataTypesXmlData(){
	    
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
	
	static function getDataTypes($usage_filter=''){
	    
	    $data = self::getDataTypesXmlData();
	    
	    // print_r($data);
	    
	    $raw_types = $data;
	    $types = array();
	    
	    $usage_filter = strlen($usage_filter) ? $usage_filter : null;
	    
	    foreach($raw_types as $raw_type){
	        if($usage_filter){
	            $usages = explode(',', $raw_type['usage']);
	            if(in_array($usage_filter, $usages)){
	                $types[$raw_type['id']] = $raw_type;
	            }
	        }else{
	            $types[$raw_type['id']] = $raw_type;
            }
	    }
	    
	    return $types;
	}
	
	static function getAssetTypesXmlData(){
	    
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
            
            // return $data;
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('assettypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('assettypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	static function getAssetTypes(){
	    
	    $data = self::getAssetTypesXmlData();
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        
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
            
            if(isset($types[$raw_type['id']]['param'])){
                if(isset($types[$raw_type['id']]['param']['name'])){
                    $types[$raw_type['id']]['param'] = array($types[$raw_type['id']]['param']);
                }
            }else{
                $types[$raw_type['id']]['param'] = array();
            }
	    }
	    
	    return $types;
	}
	
	static function getAssetClassTypesXmlData(){
	    
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
            
            // return $data;
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('placeholdertypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('placeholdertypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	static function getAssetClassTypes(){
	    
	    $data = self::getAssetClassTypesXmlData();
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        
	        $types[$raw_type['id']] = $raw_type;
	        
	        if(!defined($raw_type['id'])){
	            define($raw_type['id'], $raw_type['id']);
	        }
	        
	        if(!is_array($types[$raw_type['id']]['accept'])){
	            
	            $types[$raw_type['id']]['accept'] = array($types[$raw_type['id']]['accept']);
	        
            }
            
	    }
	    
	    return $types;
	}
	
	static function loadTypeObjects(){
		
		$available_objects = SmartestCache::load('smartest_available_type_objects', true);
		
		$singlefile = '';
		
		// load the basic types if this hasn't already been done
		$object_types = array();
		
		if($res = opendir(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR)){
		    
		    $object_type_cache_string = '';
		    
			while (false !== ($file = readdir($res))) {
    		
    			if(preg_match('/^Smartest([A-Z]\w+)\.class\.php$/', $file, $matches)){
    				if($matches[1] != 'Object'){
    					$object_type = array();
    					$object_type['name'] = $matches[1];
    					$object_type['file'] = SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR.$matches[0];
    					$object_type_cache_string .= sha1_file($object_type['file']);
    					$object_types[] = $object_type;
    				}
    			}
    		
			}
		
			closedir($res);
			
			$type_object_cache_hash = sha1($object_type_cache_string);
			
		}
	    
	    $use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_type_objects_hash', true) != $system_helper_cache_hash || !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestTypeObjects.cache.php')));
	    
	    if($use_cache){
	        if($rebuild_cache){
	            $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR.'SmartestObject.class.php');
            }
	    }else{
	        include SM_ROOT_DIR.'System/Data/Types/SmartestObject.class.php';
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
			$singlefile = "<"."?php\n\n// Cache of Basic Type Objects\n\n// Auto-generated by SmartestDataUtility - Do Not Edit".$singlefile;
		    
		    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestTypeObjects.cache.php', $singlefile);
		    
		    SmartestCache::save('smartest_type_objects_hash', $type_object_cache_hash, -1, true);
		    
		}
		
		if($use_cache){
	        include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestTypeObjects.cache.php';
	    }
	
	}

	static function stripSlashes($value){
		return is_array($value) ? array_map(array('SmartestDataUtility','stripSlashes'), $value) : utf8_encode(stripslashes($value));
	}
	
}
