<?php

class SmartestDataObjectHelper{
    
    protected $_dbconfig;
    protected $database;
    
    public function __construct(SmartestParameterHolder $dbconfig){
        $this->_dbconfig = $dbconfig;
        // $this->database = new SmartestMysql($this->_dbconfig['host'], $this->_dbconfig['username'], $this->_dbconfig['database'], $this->_dbconfig['password']);
    }
    
    static function getSchemaXmlData($file_path){
	    
	    $cache_name_hash = sha1_file($file_path).'_xml_file_hash';
	    $cache_name_data = sha1_file($file_path).'_xml_file_data';
	    
	    if(SmartestCache::hasData($cache_name_hash, true)){
	        
	        $old_hash = SmartestCache::load($cache_name_hash, true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save($cache_name_hash, $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['table'];
	            SmartestCache::save($cache_name_data, $data, -1, true);
            }else{
                $data = SmartestCache::load($cache_name_data, true);
            }
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save($cache_name_hash, $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['table'];
            SmartestCache::save($cache_name_data, $data, -1, true);
        }
        
        return $data;
        
	}
	
	static function getBasicObjectSchemaXmlData(){
	    return self::getSchemaXmlData(SM_ROOT_DIR.'System/Core/Types/basicobjecttypes.xml');
	}
	
	static function getBasicObjectSchemaInfo(){
	    
	    $data = self::getBasicObjectSchemaXmlData();
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        
	        $types[$raw_type['name']] = $raw_type;
	        
	        if(isset($types[$raw_type['name']]['noprefix'])){
	        
	            if(!is_array($types[$raw_type['name']]['noprefix'])){
	            
	                $types[$raw_type['name']]['noprefix'] = array($types[$raw_type['name']]['noprefix']);
	        
                }
            
            }else{
                
                $types[$raw_type['name']]['noprefix'] = array();
                
            }
            
	    }
	    
	    return $types;
	    
	}
	
	public function buildBasicObjects(){
	    
	    $tables = self::getBasicObjectSchemaInfo();
	    
	    foreach($tables as $t){
	        
	        $this->buildBaseDataObjectFile($t, true);
	        $this->buildDataObjectFile($t, true);
	        
	    }
	    
	}
	
	public function buildDataObjectFile($table_info, $is_smartest=false){
	    
	    $class_name = $is_smartest ? 'Smartest'.$table_info['class'] : $table_info['class'];
	    $base_class_name = $is_smartest ? 'SmartestBase'.$table_info['class'] : 'Base'.$table_info['class'];
	    $directory = $is_smartest ? SM_ROOT_DIR.'System/Data/BasicObjects/' : SM_ROOT_DIR.'Library/ObjectModel/DataObjects/';
	    $file_name = $directory.$class_name.'.class.php';
	    
	    if(!is_dir($directory)){
	        if(@mkdir($directory)){
	            
	        }else{
	            throw new SmartestException("Couldn't create new directory: ".$directory);
	        }
	    }
	    
	    if(!file_exists($file_name)){
	        
	        $file_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/dataobject_template.txt');
	        $sp = $is_smartest ? 'Smartest' : '';
	        
	        $file_contents = str_replace('__CLASSNAME__', $class_name, $file_contents);
	        $file_contents = str_replace('__BASECLASSNAME__', $base_class_name, $file_contents);
	        
			SmartestFileSystemHelper::save($file_name, $file_contents);
	    }
	    
	}
	
	public function buildBaseDataObjectFile($table_info, $is_smartest=false){
	    
	    $class_name = $is_smartest ? 'SmartestBase'.$table_info['class'] : 'Base'.$table_info['class'];
	    $directory = $is_smartest ? SM_ROOT_DIR.'System/Cache/ObjectModel/DataObjects/' : SM_ROOT_DIR.'Library/ObjectModel/DataObjects/Base/';
	    $file_name = $directory.$class_name.'.class.php';
	    
	    if(!is_dir($directory)){
	        if(@mkdir($directory)){
	            
	        }else{
	            throw new SmartestException("Couldn't create new directory: ".$directory);
	        }
	    }
	    
	    if(!file_exists($file_name)){
	        
	        $dbTableHelper = new SmartestDatabaseTableHelper('SMARTEST');
	        $columns = $dbTableHelper->getColumnNames($table_info['name']);
	        $offset = strlen($table_info['prefix']);
	        $pns = array();
	        
	        $file_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/basedataobject_template.txt');
	        $sp = $is_smartest ? 'Smartest' : '';
	        
	        $file_contents = str_replace('__CLASSNAME__', $class_name, $file_contents);
	        $file_contents = str_replace('__BASE_CLASS__', $table_info['class'], $file_contents);
	        
	        foreach($columns as $column){
		    
			    if(in_array($column, $table_info['noprefix'])){
				    $pn = $column;
				}else{
					$pn = substr($column, $offset);
				}
				
				$pns[] = $pn;
				
			}
			
			$file_contents = str_replace('__IS_SMARTEST__', $is_smartest ? 'true' : 'false', $file_contents);
			$file_contents = str_replace('__TABLE_PREFIX__', $table_info['prefix'], $file_contents);
			$file_contents = str_replace('__TABLE_NAME__', $table_info['name'], $file_contents);
			$file_contents = str_replace('__NO_PREFIX_FIELDS__', count($table_info['noprefix']) ? self::getNoPrefixFieldsAsString($table_info) : null, $file_contents);
			$file_contents = str_replace('__ORIGINAL_FIELDS__', "'".implode("', '", $columns)."'", $file_contents);
			
			$pac = self::buildBasePropertiesArray($pns);
			$file_contents = str_replace('__PROPERTIES_ARRAY__', $pac, $file_contents);
			
			$fc = self::buildBaseFunctions($pns);
			$file_contents = str_replace('__FUNCTIONS__', $fc, $file_contents);
			
	        SmartestFileSystemHelper::save($file_name, $file_contents);
	    }
	    
	}
	
	static function buildBasePropertiesArray($pns){
	    
	    $pac = '    protected $_properties = array('."\n";
		
		$i = count($pns);
		
		foreach($pns as $pn){
		    
		    if(strlen($pn)){
		        
		        $pac .= "        '".$pn."' => ''";
		        
		        if($i>1){
		            $pac .= ",";
		        }
		        
		        $i--;
		        
		        $pac .= "\n";
		        
	        }
		    
		}
		
		$pac .= '    );'."\n";
		
		return $pac;
	    
	}
	
	static function buildBaseFunctions($pns){
	    
	    $file_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/dataobject_datafunctions.txt');
	    
	    foreach($pns as $pn){
	        
		    if(strlen($pn)){
		        $f = str_replace('__PROPNAME__', $pn, $file_contents);
		        $f = str_replace('__FUNCNAME__', SmartestStringHelper::toCamelCase($pn), $f);
		        $fc .= $f;
	        }
		    
		}
		
		return $fc;
	    
	}
	
	static function getNoPrefixFieldsAsString($table_info){
	    
	    $string = '';
	    
	    $i = count($table_info['noprefix']);
	    
	    foreach($table_info['noprefix'] as $npf){
	        $string .= "'".$npf."' => 1";
	        
	        if($i>1){
		        $string .= ", ";
		    }
		    
		    $i--;
	    }
	    
	    return $string;
	}
	
	public function loadBasicObjects(){
		
		$available_objects = SmartestCache::load('smartest_available_objects', true);
		
		$singlefile = '';
		
		$this->buildBasicObjects();
		
		// find the tables info if this hasn't already been done
		
		$tables = self::getBasicObjectSchemaInfo();
		
		$object_types = array();
		
		$object_type_cache_string = '';
		
		foreach($tables as $t){
		    $base_class_name = 'SmartestBase'.$t['class'];
		    $class_name = 'Smartest'.$t['class'];
    	    $directory = SM_ROOT_DIR.'System/Data/BasicObjects/';
    	    $base_directory = SM_ROOT_DIR.'System/Cache/ObjectModel/DataObjects/';
    	    $file_name = $directory.$class_name.'.class.php';
    	    $base_file_name = $base_directory.$base_class_name.'.class.php';
		    $object_type_cache_string .= sha1_file($file_name);
		    $object_type_cache_string .= sha1_file($base_file_name);
		}
		
		$basic_object_cache_hash = sha1($object_type_cache_string);
		
		/* if($res = opendir(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR)){
		
			while (false !== ($file = readdir($res))) {
    		
    			if(preg_match('/^Smartest([A-Z]\w+)\.class\.php$/', $file, $matches)){
    				if($matches[1] != 'DataObject'){
    					$object_type = array();
    					$object_type['name'] = $matches[1];
    					$object_type['file'] = SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR.$matches[0];
    					$object_type_cache_string .= sha1_file($object_type['file']);
    					$object_types[] = $object_type;
    				}
    			}
    		
			}
		
			closedir($res);
			
			$basic_object_cache_hash = sha1($object_type_cache_string);
			
			// SmartestCache::save('smartest_available_objects', $object_types, -1, true);
	
		} */
		
		$use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_basic_objects_hash', true) != $basic_object_cache_hash) || !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestBasicObjects.cache.php'));
	
	    if($use_cache){
	        if($rebuild_cache){
	            $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR.'SmartestDataObject.class.php');
	        }
	    }else{
	        include SM_ROOT_DIR.'System/Data/BasicObjects/SmartestDataObject.class.php';
	    }
	
		foreach($tables as $t){
		    
		    $base_class_name = 'SmartestBase'.$t['class'];
		    $class_name = 'Smartest'.$t['class'];
    	    $directory = SM_ROOT_DIR.'System/Data/BasicObjects/';
    	    $base_directory = SM_ROOT_DIR.'System/Cache/ObjectModel/DataObjects/';
    	    $file_name = $directory.$class_name.'.class.php';
    	    $base_file_name = $base_directory.$base_class_name.'.class.php';
		    
			if(is_file($file_name) && is_file($base_file_name)){
				if($use_cache){
				    if($rebuild_cache){
				        $singlefile .= file_get_contents($base_file_name);
				        $singlefile .= file_get_contents($file_name);
			        }else{
    			        // don't need to include anything because types are already in cache
    			    }
				}else{
				    // Include the original file rather than the cache
				    require $base_file_name;
				    require $file_name;
				}
			}else{
				// File was there amoment ago but has now disappeared (???)
			}
		}
		
		if($rebuild_cache){
	        
	        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
			$singlefile = "<"."?php\n\n// Cache of Basic Data Objects\n\n// Auto-generated by SmartestDataObjectHelper - Do Not Edit".$singlefile;
		    
		    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestBasicObjects.cache.php', $singlefile);
		    
		    SmartestCache::save('smartest_basic_objects_hash', $basic_object_cache_hash, -1, true);
		    
		}
		
		if($use_cache){
	        include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestBasicObjects.cache.php';
	    }
	
	}
	
	static function loadExtendedObjects(){
		
		$available_objects = SmartestCache::load('smartest_available_extended_objects', true);
		
		$singlefile = '';
		
		// find the helpers if this hasn't already been done
		$object_types = array();
		
		$object_type_cache_string = '';
		
		if($res = opendir(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR)){
			
			while (false !== ($file = readdir($res))) {
    		
    			if(preg_match('/([A-Z]\w+)\.class\.php$/', $file, $matches)){
    				$object_type = array();
    				$object_type['name'] = $matches[1];
    				$object_type['file'] = SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR.$matches[0];
    				$object_type_cache_string .= sha1_file($object_type['file']);
    				$object_types[] = $object_type;
    			}
    		
			}
		
			closedir($res);
			
			$extended_object_cache_hash = sha1($object_type_cache_string);
			
			// SmartestCache::save('smartest_available_extended_objects', $object_types, -1, true);
	
		}
		
		$use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_extended_objects_hash', true) != $extended_object_cache_hash || !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestExtendedObjects.cache.php')));
	
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
				// File was there a moment ago but has now disappeared (???)
			}
		}
		
		if($rebuild_cache){
		    
	        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
			$singlefile = "<"."?php\n\n// Cache of Extended Data Objects\n\n// Auto-generated by SmartestDataUtility - Do Not Edit".$singlefile;
		    
		    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestExtendedObjects.cache.php', $singlefile);
		    
		    SmartestCache::save('smartest_extended_objects_hash', $extended_object_cache_hash, -1, true);
		}
		
		if($use_cache){
	        include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestExtendedObjects.cache.php';
	    }
	
	}
    
}