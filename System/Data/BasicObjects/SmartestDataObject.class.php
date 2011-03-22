<?php

class SmartestDataObject implements ArrayAccess{
	
	protected $_properties = array();
	protected $_modified_properties = array();
	protected $_overloaded_properties = array();
	protected $_foreign_key_objects = array();
	protected $_properties_lookup = array();
	protected $_original_fields = array();
	protected $_original_fields_hash = null;
	protected $_no_prefix = array();
	protected $_table_prefix = '';
	protected $_table_name = '';
	protected $_came_from_database = false;
	protected $database;
	protected $_last_query = '';
	protected $_dbTableHelper;
	protected $_request;
	
	public function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		
		if(SmartestPersistentObject::get('controller')){
		    $this->_request = SmartestPersistentObject::get('controller')->getCurrentRequest();
		}
		
		if(method_exists($this, '__objectConstruct')){
			$this->__objectConstruct();
		}
		
		$this->_dbTableHelper = new SmartestDatabaseTableHelper;
		
		try{
		    $this->generateModel();
		    $this->generatePropertiesLookup();
	    }catch(SmartestException $e){
	        throw new SmartestException($e->getMessage());
	    }
		
	}
	
	private function generateModel(){
		
		/*if($this->_table_name){
			
			$tables = $this->_dbTableHelper->getTables();
			
			if(in_array($this->_table_name, $tables)){
				
				// build model
				
				// $columns = $this->_dbTableHelper->getColumnNames($this->_table_name);
			    // $this->_original_fields = $columns;
				
				if(SmartestCache::hasData('internal_property_names_'.$this->_table_name, true)){
				
				    $this->_properties = SmartestCache::load('internal_property_names_'.$this->_table_name, true);
				
				}else{
				
				    $offset = strlen($this->_table_prefix);
				
    				foreach($columns as $column){
				    
    				    if(!isset($this->_no_prefix[$column])){
    					    $this->_properties[substr($column, $offset)] = '';
    					}else{
    						$this->_properties[$column] = '';
    					}
    				}
    				
    				SmartestCache::save('internal_property_names_'.$this->_table_name, $this->_properties, -1, true);
				
			    }
				
				$this->_original_fields_hash = $this->calculateFieldsHash($this->_original_fields);
				
			}else{
				// ERROR: table doesn't exist
				throw new SmartestException('The table '.$this->_table_name.' doesn\'t exist. If you have just added a new table, try clearing the cache.');
			}
			
		}else{
			// ERROR: no table set
			throw new SmartestException('Tables list could not be found. Please check the database connection settings');
		} */
		
	}
	
	public function getModifiedProperties(){
	    return $this->_modified_properties;
	}

    public function copy(){
        $classname = get_class($this);
        $obj = new $classname;
        $obj->hydrate($this->getOriginalDbRecord());
        return $obj;
    }
	
	protected function calculateFieldsHash($array){
	    if(is_array($array)){
	        return sha1(serialize($array));
        }
	}
	
	public function isHydrated(){
	    return $this->_came_from_database;
	}
	
	public function refreshDataStructure(){
	    
	    SmartestCache::clear($this->_table_name.'_columns', true);
	    $columns = $this->database->getColumnNames($this->_table_name);
		SmartestCache::save($this->_table_name.'_columns', $columns, -1, true);
		
	    SmartestCache::clear('properties_lookup_'.$this->_table_name, true);
		
		try{
		    $this->generateModel();
		    $this->generatePropertiesLookup();
	    }catch(SmartestException $e){
	        throw new SmartestException($e->getMessage());
	    }
	}
	
	protected function generatePropertiesLookup(){
		
		/* if(SmartestCache::hasData('properties_lookup_'.$this->_table_name)){
		    
		    $this->_properties_lookup = SmartestCache::load('properties_lookup_'.$this->_table_name, true);
		    
		}else{
		
		    $fields = array_keys($this->_properties);
		
		    foreach($fields as $name){
			    $this->_properties_lookup[SmartestStringHelper::toCamelCase($name)] = $name;
		    }
		    
		    SmartestCache::save('properties_lookup_'.$this->_table_name, $this->_properties_lookup, -1, true);
		
	    } */
		
	}
	
	public function offsetExists($offset){
	    $offset = strtolower($offset);
	    return isset($this->_properties[$offset]);
	}
	
	public function offsetGet($offset){
	    
	    $offset = strtolower($offset);
	    
	    switch($offset){
	        case "class":
	        case "php_class":
	        return get_class($this);
	    }
	    
	    if(isset($this->_properties[$offset])){
	        return $this->_properties[$offset];
	    }else{
	        return $this->__toString();
	    }
	    
	}
	
	public function offsetSet($offset, $value){
	    // read only
	}
	
	public function offsetUnset($offset){
	    // read only
	}
	
	public function getId(){
	    return $this->_properties['id'];
	}
	
	public function addPropertyAlias($alias, $column){
		
	}
	
	public function exemptFromPrefix($field_name){
	    $this->_no_prefix[$field_name] = 1;
	}
	
	private function getCentralDataHolder(){
	    return SmartestPersistentObject::get('centralDataHolder');
	}
	
	/* protected function setTablePrefix($prefix){
		$this->_table_prefix = $prefix;
	} 
	
	protected function setTableName($name){
		// $this->_table_name = $name;
	} */
	
	public function getTablePrefix(){
		return $this->_table_prefix;
	}
	
	public function getTableName($name){
		return $this->_table_name;
	}
	
	public function getRawTableColumns(){
	    return $this->_dbTableHelper->getColumnNames($this->getTableName());
	}
	
	public function getFields(){
	    return array_keys($this->_properties);
	}
	
	public function fieldExists($field){
	    return in_array($field, $this->getFields());
	}
	
	public function getUnprefixedFields(){
	    return array_keys($this->_no_prefix);
	}
	
	public function __toArray(){
		$data = $this->_properties;
		SmartestLog::getInstance('system')->log('Deprecated API function used: '.get_class($this).'->__toArray()');
		return $data;
	}
	
	public function compile(){
	    return $this->__toArray();
	}
	
	public function __toSimpleObject(){
	    
	    $obj = new stdClass;
	    
	    foreach($this->_properties as $property => $value){
	        $obj->$property = $value;
	    }
	    
	    return $obj;
	    
	}
	
	public function __toJson(){
	    
	    $obj = $this->__toSimpleObject();
	    return json_encode($obj);
	    
	}
	
	public function stdObjectOrScalar(){
	    return $this->__toSimpleObject();
	}
	
	public function getOriginalDbRecord(){
	    
	    $neat_data = $this->_properties;
	    $messy_data = array();
	    
	    foreach($neat_data as $key => $value){
	        if(isset($this->_no_prefix[$key])){
	            $new_key = $key;
            }else{
                $new_key = $this->_table_prefix.$key;
            }
            
            $messy_data[$new_key] = $value;
            
	    }
	    
	    return $messy_data;
	    
	}
	
	public function __toString(){
		if(isset($this->_properties['label'])){
			return $this->getLabel();
		}else if(isset($this->_properties['title'])){
			return $this->getTitle();
		}else if(isset($this->_properties['name'])){
			return $this->getName();
		}else{
			return $this->getId();
		}
	}
	
	public function getDataAccessMethods(){
	    
	    $methods = array();
	    
	    foreach($this->_properties as $property_name=>$V){
	        $methods[] = 'get'.SmartestStringHelper::toCamelCase($property_name);
	    }
	    
	    return $methods;
	    
	}
	
	public function __call($name, $args){
		
		$f3 = strtolower(substr($name, 0, 3));
		
		// Has the database structure changed?
	    if ($f3 == 'get' || $f3 == 'set') {
		    
		    SmartestCache::clear('smartest_'.$this->_table_name.'_columns', true);
			$class_file = SM_ROOT_DIR.'System/Cache/ObjectModel/DataObjects/SmartestBase'.$this->_base_class.'.class.php';
			
			if(is_file($class_file)){
			    
			    // refresh cache of columns and delete so that it can regenerate
			    SmartestLog::getInstance('system')->log('Call to undefined get/set function: '.get_class($this).'->'.$name.'(). Auto-generated class SmartestBase'.$this->_base_class.' ('.$class_file.') has been deleted to allow for re-caching.', SmartestLog::NOTICE);
			    unlink($class_file);
			    
			    /* $data = SmartestDataObjectHelper::getBasicObjectSchemaInfo();
			    $table_info = $data[$this->_table_name];
			    SmartestDataObjectHelper::buildBaseDataObjectFile($table_info, true); */
			    
		    }else{
		        SmartestLog::getInstance('system')->log('Call to undefined get/set function: '.get_class($this).'->'.$name.'(). Auto-generated class SmartestBase'.$this->_base_class.' could not be found for deletion.', SmartestLog::NOTICE);
		    }
			
		}else{
		    throw new SmartestException('Call to undefined function: '.get_class($this).'->'.$name.'()', SM_ERROR_USER);
		}
		
	}
	
	protected function getField($field_name){
		if(isset($this->_properties[$field_name])){
			return stripslashes($this->_properties[$field_name]);
		}else if(array_key_exists($field_name.'_id', $this->_properties)){
			// retrieve foreign key object, getSite(), getModel(), etc...
			if(array_key_exists($this->_properties[$field_name], $this->_foreign_key_objects)){
				return $this->_foreign_key_objects[$field_name];
			}else{
				$foreign_model_name = 'Smartest'.$field_name;
				if(class_exists($foreign_model_name)){
					$obj = new $foreign_model_name;
					$obj->hydrate($this->_properties[$field_name.'_id']);
					$this->_foreign_key_objects[$field_name] = $obj;
					return $this->_foreign_key_objects[$field_name];
				}else{
					return null;
				}
			}
		}else if(isset($this->_overloaded_properties[$field_name])){
			return $this->_overloaded_properties[$field_name];
		}else{
			return null;
		}
	}
	
	public function getFieldByName($field_name){
	    
	    if(isset($this->_properties[$field_name])){
			return stripslashes($this->_properties[$field_name]);
		}else if(isset($this->_properties[substr($field_name, strlen($this->_table_prefix))])){
		    return $this->_properties[substr($field_name, strlen($this->_table_prefix))];
		}else if(isset($this->_properties[$field_name.'_id'])){
			// retrieve foreign key object, getSite(), getModel(), etc...
			if(isset($this->_foreign_key_objects[$field_name])){
				return $this->_foreign_key_objects[$field_name];
			}else{
			    $cn = SmartestStringHelper::toCamelCase($field_name);
				$foreign_model_name = 'Smartest'.$cn;
				
				if(class_exists($foreign_model_name)){
					$obj = new $foreign_model_name;
					$obj->hydrate($this->_properties[$field_name.'_id']);
					$this->_foreign_key_objects[$field_name] = $obj;
					return $this->_foreign_key_objects[$field_name];
				}else{
					return null;
				}
			}
		}else if(isset($this->_overloaded_properties[$field_name])){
			return $this->_overloaded_properties[$field_name];
		}else{
			return null;
		}
	}
	
	protected function setField($field_name, $value){
		
		if(array_key_exists($field_name, $this->_properties)){
			
			// field being set is part of the model and corresponds to a column in the db table
			$this->_properties[$field_name] = $value;
			
			// Magic Quotes is deprecated but if switched on can still fuck things up.
			if(!SM_OPTIONS_MAGIC_QUOTES){
			    $value = addslashes($value);
		    }
			
			$this->_modified_properties[$field_name] = SmartestStringHelper::sanitize($value);
			
		}else{
		    
			// field being set is an overloaded property, which is stored, but not retrieved from or stored in the db
			$this->_overloaded_properties[$field_name] = $value;
			
		}
		
		return true;
	}
	
	public function hydrate($id, $site_id=''){
		
		if(is_array($id)){
		        
	        $offset = strlen($this->_table_prefix);
	        
	        foreach($this->_original_fields as $fn){
	            // if the new array has a value with a key that exists in this object's table 
	            if(isset($id[$fn])){
	                // if the field is exempted from prefix (rare)
	                if(isset($this->_no_prefix[$fn])){
	                    $this->_properties[$fn] = $id[$fn];
	                }else{
	                    // this needs to be made faster
	                    $this->_properties[substr($fn, $offset)] = $id[$fn];
	                }
	            }
	        }
				
			$this->_came_from_database = true;
			
			return true;
		
		}else if(is_object($id) && (!method_exists($id, '__toString') || !is_numeric($id->__toString()))){
		    
		    throw new SmartestException("Tried to hydrate a ".get_class($this)." object with another object (of type ".get_class($id).")");
		
		}else{
		    
		    // SmartestLog::getInstance('system')->log("Deprecated usage of function '".get_class($this)."->hydrate()'. Please use only with an array.");
		    return $this->find($id, $site_id);
	        
		}
		
	}
	
	public function find($id, $site_id=''){
	    
	    if(strlen($id)){
        
            $sql = $this->getRetrievalSqlQuery((int) $id, 'id', $site_id);
	        
	        $this->_last_query = $sql;
			$result = $this->database->queryToArray($sql, $file, $line);

		    if(count($result)){
	
			    foreach($result[0] as $name => $value){
				    if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
					    $this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
				    }else if(isset($this->_no_prefix[$name])){
					    $this->_properties[$name] = $value;
				    }
			    }
	
			    $this->_came_from_database = true;
		    
			    return true;
		    }else{
			    return false;
		    }
	    
        }else{
            
            SmartestLog::getInstance('system')->log(get_class($this)."->find() called without a valid ID.", SmartestLog::WARNING);
            // A bit harsh for now:
            // throw new SmartestException("SmartestDataObject->find() must be called with a valid ID.");
            SmartestLog::getInstance('system')->log(__CLASS__."->find() must be called with a valid ID.");
            
        }
	    
	}
	
	public function findBy($field, $value, $site_id=''){
	    
	    if(isset($this->_no_prefix[$field])){
		    $column_name = $field;
		}else{
			$column_name = $this->_table_prefix.$field;
		}
	    
	    $sql = $this->getRetrievalSqlQuery($value, $field, $site_id);
	    
	    $result = $this->database->queryToArray($sql);
	    $this->_last_query = $sql;
	    
	    if(count($result)){
	
		    foreach($result[0] as $name => $value){
			    if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
				    $this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
			    }else if(isset($this->_no_prefix[$name])){
				    $this->_properties[$name] = $value;
			    }
		    }
	
		    $this->_came_from_database = true;
		    
		    return true;
	    }else{
		    return false;
	    }
	    
	}
	
	protected function getRetrievalSqlQuery($value, $field='id', $site_id=''){
	    
	    if(isset($this->_no_prefix[$field])){
		    $column_name = $field;
		}else{
			$column_name = $this->_table_prefix.$field;
		}
	    
	    $sql = "SELECT * FROM ".$this->_table_name." WHERE ".$column_name." = '".$value."'";
	    
	    if(is_numeric($site_id) && isset($this->_properties['site_id'])){
            if(isset($this->_properties['shared'])){
                $sql .= " AND (".$this->_table_prefix."site_id='".$site_id."' OR ".$this->_table_prefix."shared='1')";
            }else{
                $sql .= " AND ".$this->_table_prefix."site_id='".$site_id."'";
            }
	    }
	    
	    return $sql;
	    
	}
	
	public function clearRetrievalSqlQueryFromCache(){
	    
	    // $this->database->clearQueryFromCache($this->);
	    // echo get_class($this)." ";
	    // echo $this->getRetrievalSqlQuery($this->_properties['id']);
	    return $this->database->clearQueryFromCache($this->getRetrievalSqlQuery($this->_properties['id']), 'id');
	    
	}
	
	public function hydrateBy($field, $value, $site_id=''){
	    
	    SmartestLog::getInstance('system')->log("Deprecated function used: ".get_class($this)."->hydrateBy()");
	    return $this->findBy($field, $value, $site_id);
	    
	}
	
	public function getSaveSql(){
	    
	    if($this->_came_from_database){
			$sql = $this->getUpdateSql();
		}else{
			$sql = $this->getInsertSql();
		}
		
		return $sql;
	    
	}
	
	public function getInsertSql(){
	    
	    $sql = "INSERT INTO ".$this->_table_name." (";
		$fields = array();
	
		foreach($this->_modified_properties as $key => $value){
		    if(!isset($this->_no_prefix[$key])){
			    $fields[] = $this->_table_prefix.$key;
			}else{
			    $fields[] = $key;
			}
		}
	
		$sql .= join(', ', $fields).") VALUES (";
	
		$i = 0;
	
		foreach($this->_modified_properties as $value){
		
			if($i > 0){
				$sql .= ', ';
			}
		
			$sql .= "'".$value."'";
			$i++;
		}
	
		$sql .= ')';
		
		return $sql;
	    
	}
	
	public function getUpdateSql(){
	    
	    $sql = "UPDATE ".$this->_table_name." SET ";
	
		$i = 0;
	
		foreach($this->_modified_properties as $name => $value){
		
			if($i > 0){
				$sql .= ', ';
			}
		
			if(!isset($this->_no_prefix[$name])){
				$sql .= $this->_table_prefix.$name."='".$value."'";
			}else{
				$sql .= $name."='".$value."'";
			}
		
			$i++;
		}

		$sql .= " WHERE ".$this->_table_prefix."id='".$this->_properties['id']."' LIMIT 1";
		
		return $sql;
	    
	}
	
	public function save(){
		
		if(count($this->_modified_properties)){
		    
		    $sql = $this->getSaveSql();
		    $this->_last_query = $sql;
    		$id = $this->database->query($sql);
		    
		    if($this->_came_from_database){
		        $cc = $this->clearRetrievalSqlQueryFromCache();
		        // var_dump($cc);
		    }else{
    			$this->_properties['id'] = $id;
    			$this->_came_from_database = true;
    		}
		    
		    $this->_modified_properties = array();
		
	    }
	}
	
	public function delete(){
		$sql = "DELETE FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='".$this->getId()."' LIMIT 1";
		$this->_last_query = $sql;
		$this->database->rawQuery($sql);
		$this->_came_from_database = false;
	}
	
	protected function getSite(){
	    return SmartestSession::get('current_open_project');
	}
	
	public function getRequest(){
	    return $this->_request;
	}
	
	protected function getCurrentSiteId(){
	    
	    if($this->getRequest()->getModule() == 'website'){
            $site_id = constant('SM_CMS_PAGE_SITE_ID');
        }else if(is_object($this->getSite())){
            // make sure the site object exists
            $site_id = $this->getSite()->getId();
        }
        
        return $site_id;
	}
	
	public function getLastQuery(){
	    return $this->_last_query;
	}
	
	public function getDbConnectionLastQuery(){
	    return $this->database->getLastQuery();
	}
	
}