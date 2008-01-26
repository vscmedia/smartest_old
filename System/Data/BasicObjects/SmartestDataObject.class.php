<?php

class SmartestDataObject{
	
	protected $_properties = array();
	protected $_modified_properties = array();
	protected $_overloaded_properties = array();
	protected $_foreign_key_objects = array();
	protected $_properties_lookup = array();
	protected $_no_prefix = array();
	protected $_table_prefix = '';
	protected $_table_name = '';
	protected $_came_from_database = false;
	protected $database;
	
	public function __construct(){
		
		// print_r(SmartestPersistentObject::getRegisteredNames());
		
		$this->database = SmartestPersistentObject::get('db:main');
		
		if(method_exists($this, '__objectConstruct')){
			$this->__objectConstruct();
		}
		
		try{
		    $this->generateModel();
		    $this->generatePropertiesLookup();
	    }catch(SmartestException $e){
	        throw new SmartestException($e->getMessage());
	    }
		
	}
	
	private function generateModel(){
		
		if($this->_table_name){
			
			if(SmartestCache::hasData('smartest_tables', true)){
				$tables = SmartestCache::load('smartest_tables', true);
			}else{
				$tables = $this->database->getTables($this->_table_name);
				SmartestCache::save('smartest_tables', $tables, -1, true);
			}
			
			if(in_array($this->_table_name, $tables)){
				
				// build model
				
				if(SmartestCache::hasData($this->_table_name.'_columns', true)){
					$columns = SmartestCache::load($this->_table_name.'_columns', true);
				}else{
					$columns = $this->database->getColumnNames($this->_table_name);
					SmartestCache::save($this->_table_name.'_columns', $columns, -1, true);
				}
				
				foreach($columns as $column){
					if(!in_array($column, $this->_no_prefix)){
						$this->_properties[substr($column, strlen($this->_table_prefix))] = '';
					}else{
						$this->_properties[$column] = '';
					}
				}
				
			}else{
				// ERROR: table doesn't exist
				throw new SmartestException('The table '.$this->_table_name.' doesn\'t exist. If you have just added a new table, try clearing the cache.');
			}
			
		}else{
			// ERROR: no table set
			throw new SmartestException('Tables list could not be found. Please check the database connection settings');
		}
		
	}
	
	public function isHydrated(){
	    return $this->_came_from_database;
	}
	
	public function refreshDataStructure(){
	    
	    SmartestCache::clear($this->_table_name.'_columns', true);
	    $columns = $this->database->getColumnNames($this->_table_name);
		SmartestCache::save($this->_table_name.'_columns', $columns, -1, true);
		
		try{
		    $this->generateModel();
		    $this->generatePropertiesLookup();
	    }catch(SmartestException $e){
	        throw new SmartestException($e->getMessage());
	    }
	}
	
	protected function generatePropertiesLookup(){
		
		$fields = array_keys($this->_properties);
		
		foreach($fields as $name){
			$this->_properties_lookup[SmartestStringHelper::toCamelCase($name)] = $name;
		}
		
	}
	
	public function addPropertyAlias($alias, $column){
		if(!array_key_exists($alias, $this->_properties_lookup)){
			$this->_properties_lookup[$alias] = $column;
		}
	}
	
	private function getCentralDataHolder(){
	    return SmartestPersistentObject::get('centralDataHolder');
	}
	
	protected function setTablePrefix($prefix){
		$this->_table_prefix = $prefix;
	}
	
	protected function setTableName($name){
		$this->_table_name = $name;
	}
	
	public function getTablePrefix(){
		return $this->_table_prefix;
	}
	
	public function getTableName($name){
		return $this->_table_name;
	}
	
	public function getUnprefixedFields(){
	    return $this->_no_prefix;
	}
	
	public function __toArray(){
		$data = $this->_properties;
		ksort($data);
		return $data;
	}
	
	public function compile(){
	    return $this->__toArray();
	}
	
	public function __toSimpleObject(){
	    
	    $obj = new stdClass;
	    
	    foreach($this->__toArray() as $property => $value){
	        $obj->$property = $value;
	    }
	    
	    return $obj;
	    
	}
	
	public function __toJson(){
	    
	    $obj = $this->__toSimpleObject();
	    
	    return json_encode($obj);
	    
	}
	
	public function getOriginalDbRecord(){
	    $neat_data = $this->_properties;
	    
	    $messy_data = array();
	    
	    foreach($neat_data as $key => $value){
	        if(in_array($key, $this->_no_prefix)){
	            $new_key = $key;
            }else{
                $new_key = $this->_table_prefix.$key;
            }
            
            $messy_data[$new_key] = $value;
            
	    }
	    
	    return $messy_data;
	    
	}
	
	public function __toString(){
		if($this->getLabel()){
			return $this->getLabel();
		}else if($this->getTitle()){
			return $this->getTitle();
		}else if($this->getName()){
			return $this->getName();
		}else{
			return $this->getId();
		}
	}
	
	public function getDataAccessMethods(){
	    
	    $methods = array();
	    
	    foreach($this->_properties_lookup as $property_name){
	        $methods[] = 'get'.$property_name;
	    }
	    
	    return $methods;
	    
	}
	
	/* public function __sleep(){
		$this->database = null;
	}
	
	public function __wakeUp(){
		$this->database =& $_SESSION['database'];
	} */
	
	public function __call($name, $args){
		
		if (strtolower(substr($name, 0, 3)) == 'get') {
			return $this->getField(substr($name, 3));
		}
    
		if ((strtolower(substr($name, 0, 3)) == 'set') && count($args)) {
			return $this->setField(substr($name, 3), $args[0]);
		}
	}
	
	protected function getField($field_name){
		if(array_key_exists($field_name, $this->_properties_lookup)){
			return $this->_properties[$this->_properties_lookup[$field_name]];
		}else if(array_key_exists($field_name.'Id', $this->_properties_lookup)){
			// retrieve foreign key object, getSite(), getModel(), etc...
			if(array_key_exists($this->_properties_lookup[$field_name.'Id'], $this->_foreign_key_objects)){
				return $this->_foreign_key_objects[$this->_properties_lookup[$field_name.'Id']];
			}else{
				$foreign_model_name = 'Smartest'.$field_name;
				if(class_exists($foreign_model_name)){
					$obj = new $foreign_model_name;
					$obj->hydrate($this->_properties[$this->_properties_lookup[$field_name.'Id']]);
					$this->_foreign_key_objects[$this->_properties_lookup[$field_name.'Id']] = $obj;
					return $this->_foreign_key_objects[$this->_properties_lookup[$field_name.'Id']];
				}else{
					return null;
				}
			}
		}else if(array_key_exists($field_name, $this->_overloaded_properties)){
			return $this->_overloaded_properties[$field_name];
		}else{
			return null;
		}
	}
	
	protected function setField($field_name, $value){
		if(array_key_exists($field_name, $this->_properties_lookup)){
			// field being set is part of the model and corresponds to a column in the db table
			
			$this->_properties[$this->_properties_lookup[$field_name]] = $value;
			
			$this->_modified_properties[$this->_properties_lookup[$field_name]] = $value;
			
			
		}else{
			// field being set is an overloaded property, which is stored, but not retrieved from or stored in the db
			$this->_overloaded_properties[$field_name] = $value;
			
		}
		
		return true;
	}
	
	public function hydrate($id, $file='', $line=''){
		
		if(is_array($id)){
			
			foreach($id as $key => $value){
				if(substr($key, 0, strlen($this->_table_prefix)) == $this->_table_prefix){
					$this->_properties[substr($key, strlen($this->_table_prefix))] = $value;
				}else if(in_array($name, $this->_no_prefix)){
					$this->_properties[$name] = $value;
				}
			}
			
			$this->_came_from_database = true;
			
			/* if(!$this->getCentralDataHolder()->has($this->_table_name.':'.$this->getId())){
			    $this->getCentralDataHolder()->set($this->_table_name.':'.$this->getId(), $this);
			} */
			
			return true;
			
		}else{
		    
		    // if($this->getCentralDataHolder()->has($this->_table_name.':'.$this->getId())){
		        
		        // $this =& $this->getCentralDataHolder()->set($this->_table_name.':'.$this->getId());
		        
		    // }else{
		    
			    $sql = "SELECT * FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='$id'";
		
			    $result = $this->database->queryToArray($sql, $file, $line);
		
			    if(count($result)){
			
				    foreach($result[0] as $name => $value){
					    if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
						    $this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
						    $this->_properties_lookup[SmartestStringHelper::toCamelCase(substr($name, strlen($this->_table_prefix)))] = substr($name, strlen($this->_table_prefix));
					    }else if(in_array($name, $this->_no_prefix)){
						    $this->_properties[$name] = $value;
					    }
				    }
			
				    $this->_came_from_database = true;
				    
				    return true;
			    }else{
				    return false;
			    }
			
	        // }
		}
		
	}
	
	public function hydrateBy($field, $value, $draft=false){
	    
	    $sql = "SELECT * FROM ".$this->_table_name." WHERE ".$this->_table_prefix.$field." = '".$value."'";
	    
	    $result = $this->database->queryToArray($sql, $file, $line);

	    if(count($result)){
	
		    foreach($result[0] as $name => $value){
			    if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
				    $this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
				    $this->_properties_lookup[SmartestStringHelper::toCamelCase(substr($name, strlen($this->_table_prefix)))] = substr($name, strlen($this->_table_prefix));
			    }else if(in_array($name, $this->_no_prefix)){
				    $this->_properties[$name] = $value;
			    }
		    }
	
		    $this->_came_from_database = true;
		    
		    return true;
	    }else{
		    return false;
	    }
	    
	}
	
	public function save(){
		
		if($this->_came_from_database){
			
			$sql = "UPDATE ".$this->_table_name." SET ";
			
			$i = 0;
			
			foreach($this->_modified_properties as $name => $value){
				
				if($i > 0){
					$sql .= ', ';
				}
				
				if(!in_array($name, $this->_no_prefix)){
					$sql .= $this->_table_prefix.$name."='".$value."'";
				}else{
					$sql .= $name."='".$value."'";
				}
				
				$i++;
			}
		
			$sql .= " WHERE ".$this->_table_prefix."id='".$this->_properties['id']."' LIMIT 1";
			
			$this->database->rawQuery($sql);
			
		}else{
			
			$sql = "INSERT INTO ".$this->_table_name."(";
			$fields = array();
			
			foreach($this->_modified_properties as $key => $value){
			    if(!in_array($key, $this->_no_prefix)){
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
				
				$sql .= "'$value'";
				$i++;
			}
			
			$sql .= ')';
			
			$id = $this->database->query($sql);
			$this->_properties['id'] = $id;
			$this->generatePropertiesLookup();
			$this->_came_from_database = true;
		}
		
		$this->_modified_properties = array();
	}
	
	public function delete(){
		$sql = "DELETE FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='".$this->getId()."' LIMIT 1";
		$this->database->rawQuery($sql);
		$this->_came_from_database = false;
	}
	
	protected function getSite(){
	    
	    return SmartestPersistentObject::get('current_open_project');
	    
	}
	
	protected function getCurrentSiteId(){
	    
	    if(SM_CONTROLLER_MODULE == 'website'){
            $site_id = constant('SM_CMS_PAGE_SITE_ID');
        }else if(is_object($this->getSite())){
            // make sure the site object exists
            $site_id = $this->getSite()->getId();
        }
        
        return $site_id;
	}
	
	/* public static function retrieveAllAsRawArrays(){
	    
	    $sql = "SELECT * FROM ".$this->_table_name;
	    $result = SmartestPersistentObject::get('db:main')->queryToArray($sql);
	    return $result;
	    
	}*/
	
}