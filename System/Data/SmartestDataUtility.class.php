<?php

class SmartestDataUtility{

	protected $database;
	
	public function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
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
	
	public function getDataSetsAsArrays($simple = false, $site_id=''){
	    $sets = $this->getDataSets($simple, $site_id);
	    $arrays = array();
	    
	    foreach($sets as $s){
	        $arrays[] = $s->__toArray();
	    }
	    
	    return $arrays;
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
	
	static function getDataTypes(){
	    
	    $data = self::getDataTypesXmlData();
	    
	    // print_r($data);
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        $types[$raw_type['id']] = $raw_type;
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
	    
	    // print_r($data);
	    
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
	
	static function loadBasicObjects(){
		
		$available_objects = SmartestCache::load('smartest_available_objects', true);
		
		$singlefile = '';
		
		// find the helpers if this hasn't already been done
		if(!is_array($available_objects) || !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'basicobjects.php')){
		
			$helpers = array();
			
			if($res = opendir(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR)){
			
				while (false !== ($file = readdir($res))) {
        		
        			if(preg_match('/^Smartest([A-Z]\w+)\.class\.php$/', $file, $matches)){
        				// $files[] = $file;
        				// print_r($matches);
        				if($matches[1] != 'DataObject'){
        					$helper = array();
        					$helper['name'] = $matches[1];
        					$helper['file'] = $matches[0];
        					$helpers[] = $helper;
        				}
        			}
        		
				}
			
				closedir($res);
				
				SmartestCache::save('smartest_available_objects', $helpers, -1, true);
				$available_objects = $helpers;
		
			}
		
		    if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			    include SM_ROOT_DIR.'System/Data/BasicObjects/SmartestDataObject.class.php';
		    }else{
		        $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR.'SmartestDataObject.class.php');
		    }
		
			foreach($available_objects as $h){
				if(is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR.$h['file'])){
					// echo 'Loading Basic Object: '.$h['name'].'<br />';
					// include 'System/Data/BasicObjects/'.$h['file'];
					if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
					    include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR.$h['file'];
					}else{
					    $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR.$h['file']);
				    }
				}else{
					SmartestCache::clear('smartest_available_objects', true);
				}
			}
			
			if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
		        
		    }else{
		        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
    			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
    			$singlefile = "<"."?php\n\n// Auto-generated by SmartestHelper - Do Not Edit".$singlefile;
			    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'basicobjects.php', $singlefile);
			}
			
		}
		
		if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
	    
	    }else{
		    include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'basicobjects.php';
	    }
	
	}
	
	static function loadExtendedObjects(){
		
		$available_objects = SmartestCache::load('smartest_available_extended_objects', true);
		
		$singlefile = '';
		
		// find the helpers if this hasn't already been done
		if(!is_array($available_objects) || !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'extendedobjects.php')){
		
			$helpers = array();
			
			if($res = opendir(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR)){
			
				while (false !== ($file = readdir($res))) {
        		
        			if(preg_match('/([A-Z]\w+)\.class\.php$/', $file, $matches)){
        				// $files[] = $file;
        				// print_r($matches);
        				$helper = array();
        				$helper['name'] = $matches[1];
        				$helper['file'] = $matches[0];
        				$helpers[] = $helper;
        			}
        		
				}
			
				closedir($res);
				
				SmartestCache::save('smartest_available_extended_objects', $helpers, -1, true);
				$available_objects = $helpers;
		
			}
		
		    if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			    // include SM_ROOT_DIR.'System/Data/ExtendedObjects/SmartestDataObject.class.php';
		    }else{
		        // $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR.'SmartestDataObject.class.php');
		    }
		
			foreach($available_objects as $h){
				if(is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR.$h['file'])){
					// echo 'Loading Extended Object: '.$h['name'].'<br />';
					// include 'System/Data/ExtendedObjects/'.$h['file'];
					if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
					    include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR.$h['file'];
					}else{
					    $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR.$h['file']);
				    }
				}else{
					SmartestCache::clear('smartest_available_extended_objects', true);
				}
			}
			
			if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
		        
		    }else{
		        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
    			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
    			$singlefile = "<"."?php\n\n// Auto-generated by SmartestHelper - Do Not Edit".$singlefile;
			    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'extendedobjects.php', $singlefile);
			}
			
		}
		
		if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
	    
	    }else{
		    include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'extendedobjects.php';
	    }
	
	}
	
    static function loadTypeObjects(){
		
		$available_objects = SmartestCache::load('smartest_available_type_objects', true);
		
		$singlefile = '';
		
		// find the helpers if this hasn't already been done
		if(!is_array($available_objects) || !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'typeobjects.php')){
		
			$helpers = array();
			
			if($res = opendir(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR)){
			
				while (false !== ($file = readdir($res))) {
        		
        			if(preg_match('/^Smartest([A-Z]\w+)\.class\.php$/', $file, $matches)){
        				// $files[] = $file;
        				// print_r($matches);
        				if($matches[1] != 'Object'){
        					$helper = array();
        					$helper['name'] = $matches[1];
        					$helper['file'] = $matches[0];
        					$helpers[] = $helper;
        				}
        			}
        		
				}
			
				closedir($res);
				
				SmartestCache::save('smartest_available_type_objects', $helpers, -1, true);
				$available_objects = $helpers;
		
			}
		
		    if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			    include SM_ROOT_DIR.'System/Data/Types/SmartestObject.class.php';
		    }else{
		        $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR.'SmartestObject.class.php');
		    }
		
			foreach($available_objects as $h){
				if(is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR.$h['file'])){
					// echo 'Loading Basic Object: '.$h['name'].'<br />';
					// include 'System/Data/BasicObjects/'.$h['file'];
					if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
					    include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR.$h['file'];
					}else{
					    $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR.$h['file']);
				    }
				}else{
					SmartestCache::clear('smartest_available_type_objects', true);
				}
			}
			
			if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
		        
		    }else{
		        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
    			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
    			$singlefile = "<"."?php\n\n// Auto-generated by SmartestHelper - Do Not Edit".$singlefile;
			    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'typeobjects.php', $singlefile);
			}
			
		}
		
		if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
	    
	    }else{
		    include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'typeobjects.php';
	    }
	
	}

	static function stripSlashes($value){
		return is_array($value) ? array_map(array('SmartestDataUtility','stripSlashes'), $value) : utf8_encode(stripslashes($value));
	}
	
	
}
