<?php

/**
* @package Smartest
* @subpackage CMS Object Model
* @author Marcus Gilroy-Ware <marcus@mjgw.com>
* SmartestCmsItem is the underlying class that is extended to create the objects that are created and edited in the CMS
* It is also used
*/

class SmartestCmsItem implements ArrayAccess{
	
	/** 
	* Description
	* @access protected
	* @var mixed
	*/
	protected $_item;
	
	/** 
	* Description
	* @access protected
	* @var SmartestModel
	*/
	protected $_model = null;
	
	/** 
	* A list of the actual properties of the loaded object. The numeric keys are the primary keys of the properties in the Properties table.
	* @access protected
	* @var array
	*/
	protected $_properties = array();
	
	/** 
	* A list of all those properties that have been modified which is generated and updated automatically so that when the object is saved, only the properties in this list will be updated.
	* @access protected
	* @var array
	*/
	protected $_modified_properties = array();
	
	/** 
	* A list of any properties that are referred to by the user's code, but aren't linked to actual properties in the structure of the model.
	* @access protected
	* @var array
	*/
	protected $_overloaded_properties = array();
	
	/** 
	* A mapping of the items' property names to the ids of the properties.
	* @access protected
	* @var array
	*/
	protected $_properties_lookup = array();
	
	/** 
	* A mapping of the varnames of the properties to the ids of the properties, for speed.
	* @access protected
	* @var array
	*/
	protected $_varnames_lookup = array();
	
	/** 
	* Description
	* @access protected
	* @var array
	*/
	protected $_property_values_lookup = array();
	
	/** 
	* Description
	* @access protected
	* @var boolean
	*/
	
	protected $_came_from_database = false;
	protected $_model_built = false;
	protected $_lookups_built = false;
	protected $_save_errors = array();
	protected $_draft_mode = false;
	
	/** 
	* Description
	* @access protected
	* @var SmartestMysql
	*/
	protected $database;
	
	const NAME = '_SMARTEST_ITEM_NAME';
	const ID = '_SMARTEST_ITEM_ID';
	
	const NOT_CHANGED = 100;
	const AWAITING_APPROVAL = 101;
	const CHANGES_APPROVED = 102;
	
	public function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		$this->_item = new SmartestItem;
		
		$this->generateModel();
		// $this->generatePropertiesLookup();
		
	}
	
	private function generateModel(){
		
		// $this->getModel();
		
		if(isset($this->_model_id) && !$this->_model_built){
		
		    if(SmartestCache::hasData('model_properties_'.$this->_model_id, true)){
		        $result = SmartestCache::load('model_properties_'.$this->_model_id, true);
		    }else{
			    // gotta get that from the database too
			    $sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_model_id."'";
			    $result = $this->database->queryToArray($sql);
			    SmartestCache::save('model_properties_'.$this->_model_id, $result, -1, true);
		    } 
		
		    // print_r($result);
			
		    $properties = array();
			
		    foreach($result as $key => $raw_property){
		        
		        $property = new SmartestItemPropertyValueHolder;
		        
		        // if(!){
		            $property->hydrate($raw_property);
		        // }
		        
			    $this->_properties[$raw_property['itemproperty_id']] = $property;
		    }
		    
		    $this->_model_built = true;
		
	    }
		
	}
	
	function __call($name, $args){
		
		throw new SmartestException("Call to undefined function: ".get_class($this).'->'.$name.'()');
		
	}
	
	function getPropertyVarNames(){
	    return array_keys($this->_varnames_lookup);
	}
	
	public function setDraftMode($mode){
	    $this->_draft_mode = (bool) $mode;
	}
	
	public function getDraftMode(){
	    return $this->_draft_mode;
	}
	
	public function offsetExists($offset){
	    
	    return ($this->_item->offsetExists($offset) || isset($this->_varnames_lookup[$offset]) || in_array($offset, array('_workflow_status', '_model', '_properties')));
	    
	}
	
	public function offsetGet($offset){
	    
	    if(defined('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && constant('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && defined('SM_CMS_PAGE_ID')){
		    $dah = new SmartestDataAppearanceHelper;
            $dah->setItemAppearsOnPage($this->getId(), constant('SM_CMS_PAGE_ID'));
		}
	    
	    if($this->_item->offsetExists($offset)){
	        
	        return $this->_item->offsetGet($offset);
	        
	    }else if(isset($this->_varnames_lookup[$offset])){
	        
	        return $this->getPropertyValueByNumericKey($this->_varnames_lookup[$offset]);
	        
	    }else{
	        
	        switch($offset){
	            
	            case "_workflow_status":
	            
	            switch($this->getWorkflowStatus()){
            	    
            	    case self::NOT_CHANGED:
            	    return 'Not changed';
            	    break;
            	    
            	    case self::CHANGES_APPROVED:
            	    return 'Approved and ready for publishing';
            	    break;
            	    
            	    default:
            	    return 'Awaiting approval';
            	    break;
            	}
            	
	            break;
	            
	            case '_model':
	            $this->getModel();
	            break;
	            
	            case '_properties':
	            $this->getProperties();
	            break;
	            
	        }
	        
	    }
	    
	}
	
	public function offsetSet($offset, $value){
	    // read only
	}
	
	public function offsetUnset($offset){
	    // read only
	}
	
	public function getCacheFiles(){
	    
	    $ending = '__id'.$this->getId().'.html';
	    $start = 0 - strlen($ending);
	    $files = array();
	    
	    foreach(SmartestFileSystemHelper::load(constant('SM_ROOT_DIR').'System/Cache/Pages/') as $file){
	        if(substr($file, $start) == $ending){
	            $files[] = constant('SM_ROOT_DIR').'System/Cache/Pages/'.$file;
	        }
	    }
	    
	    return $files;
	    
	}
	
	private function getField($field_name, $draft=false){
		if(array_key_exists($field_name, $this->_properties_lookup)){
		    if($this->_properties[$this->_properties_lookup[$field_name]] instanceof SmartestItemPropertyValueHolder){
			    // return $this->_properties[$this->_properties_lookup[$field_name]];
			    if($this->_properties[$this->_properties_lookup[$field_name]]->getData() instanceof SmartestItemPropertyValue){
		            if($draft){
		                return $this->_properties[$this->_properties_lookup[$field_name]]->getData()->getDraftContent();
		            }else{
		                return $this->_properties[$this->_properties_lookup[$field_name]]->getData()->getContent();
		            }
		        }else{
		            
		            // no value found, so create one
		            $ipv = new SmartestItemPropertyValue;
    	            $ipv->setPropertyId($this->_properties[$this->_properties_lookup[$field_name]]->getId());
    	            $ipv->setItemId($this->getItem()->getId());
    	            $ipv->setDraftContentId($this->_properties[$this->_properties_lookup[$field_name]]->getDefaultValue());
    	            $ipv->save();
    	            
    	            if($draft){
    	                return $ipv->getDraftContent();
    	            }else{
    	                return null;
    	            }
		        }
		    }
		}else if(array_key_exists($field_name, $this->_overloaded_properties)){
			return $this->_overloaded_properties[$field_name];
		}else{
			return null;
		}
	}
	
	private function setField($field_name, $value){
		if(array_key_exists($field_name, $this->_properties_lookup)){
			// field being set is part of the model and corresponds to a column in the db table
			
			// $this->_properties[$this->_properties_lookup[$field_name]]->setDraftContent($value);
			$this->setPropertyValueByNumericKey($this->_properties_lookup[$field_name], $value);
			
			// $this->_modified_properties[$this->_properties_lookup[$field_name]] = $value;
		}else{
			// field being set is an overloaded property, which is stored, but not retrieved from or stored in the db
			$this->_overloaded_properties[$field_name] = $value;
			
		}
		
		return true;
	}
	
	public function setModelId($id){
	    
	    if($this instanceof SmartestCmsItem && !$this->_model_built && is_numeric($id)){
	        
	        $this->_model_id = $id;
	        $this->_model = new SmartestModel;
	        
	        if(!$this->_model->hydrate($this->_model_id)){
	            throw new SmartestException('The model ID '.$this->_model_id.' doesn\'t exist.');
	        }
	        
	        if(!$this->_model_built){
    	        $this->generateModel();
    	    }
	        
	    }
	    
	}
	
	public function setSiteId($id){
	    if(is_object($this->_item)){
	        $this->_item->setSiteId($id);
        }
	}
    
    public function getMetapageId(){
	    
	    return $this->_item->getMetapageId();
	    
	}
	
	public function getMetaPage(){
	    
	    return $this->_item->getMetapage();
	    
	}
	
	public function getItemSpaceDefinitions($draft=false){
	    
	    return $this->_item->getItemSpaceDefinitions($draft);
	    
	}
	
	public function hydrateNewFromRequest($request_data){
	    
	    // print_r($request_data);
	    
	    // $this->_save_errors = array();
	    
	    if(is_array($request_data)){
	            
		$this->_item->setName(SmartestStringHelper::sanitize($request_data['_name']));
            
            //if(isset($request_data['_is_public']) && in_array($request_data['_is_public'], array("TRUE", "FALSE"))){
            //    $this->_item->setPublic($request_data['_is_public']);
            // }else{
            $this->_item->setPublic('FALSE');
            // }
            
            $this->_item->setItemclassId($this->_model_id);
            $this->_item->setSlug(SmartestStringHelper::toSlug($this->_item->getName()));
            $this->_item->setWebid(SmartestStringHelper::random(32));
            $this->_item->setCreated(time());
            $this->_item->setModified(time()+2); // this is to make it show up on the approval todo list
            
            if(SmartestPersistentObject::get('user') instanceof SmartestUser){
                $this->_item->setCreatedbyUserid(SmartestPersistentObject::get('user')->getId());
            }
	        
	        foreach($request_data as $key => $value){
	        
	            if(isset($this->_properties[$key]) && !in_array($key, array('_name', '_is_public')) && is_object($this->_properties[$key])){
	                
	                $this->setPropertyValueByNumericKey($key, $value);
                    
	            }else{
	                // echo "property value object not found<br />";
	                // property object doesn't exist
	                // $this->_save_errors[$key] = $value;
	                // TODO: decide what to do here and implement it here
	            }
	        }
	        
	        if(!count($this->_save_errors)){
	            return true;
	        }else{
	            return false;
	        }
	        
	    }else{
	        
	        // error - expecting data in associative array
	        
	    }
	}
	
	public function hydrate($id, $draft=false){
		
		if($this->_item->hydrate($id)){
		    
		    // echo($id.' was hydrated ');
		    $this->_came_from_database = true;
		    // var_dump($this->isHydrated());
		    
		    if(!$this->_model_built){
		        $this->_model_id = $this->_item->getItemclassId();
		        $this->generateModel();
		    }
		    
		    /* if(!$this->_lookups_built){
		        $this->generatePropertiesLookup();
		    } */
		    
		    if(SmartestCache::hasData('model_properties_'.$this->_model_id, true)){
			    $properties_result = SmartestCache::load('model_properties_'.$this->_model_id, true);
		    }else{
			    // gotta get that from the database too
			    $properties_sql = "SELECT itemproperty_id, itemproperty_name, itemproperty_varname FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_model_id."' AND itemproperty_varname !='hydrate'";
			    $properties_result = $this->database->queryToArray($sql);
			    SmartestCache::save('model_properties_'.$this->_model_id, $result, -1, true);
		    }
		    
		    // loop through properties first time, just setting up empty holder items
		    foreach($properties_result as $property){
		        
		        if(!isset($this->_properties[$property['itemproperty_id']]) || !is_object($this->_properties[$property['itemproperty_id']])){
		            SmartestCache::clear('model_properties_'.$this->_model_id, true);
		            $this->_properties[$property['itemproperty_id']] = new SmartestItemPropertyValueHolder;
		        }
		        
			    $this->_properties[$property['itemproperty_id']]->hydrate($property);
			    $this->_properties[$property['itemproperty_id']]->setContextualItemId($this->_item->getId());
		    }
		    
            $values_sql = "SELECT * FROM ItemPropertyValues WHERE itempropertyvalue_item_id='$id'";
		    $result = $this->database->queryToArray($values_sql);
		    
		    // then loop through properties again, making sure all are given either a ipv from the last db query, or given a new one if none was found.
		    // these ifs and buts shouldn't run very often if everything is working as it should
			
			foreach($result as $propertyvalue){
			    
			    $ipv = new SmartestItemPropertyValue;
			    $ipv->hydrate($propertyvalue);
			    
                // if the property object does not exist, create and hydrate it
                // var_dump($this->_item->getId());
                // var_dump(isset($this->_properties[$ipv->getPropertyId()]));
                // var_dump(is_object($this->_properties[$ipv->getPropertyId()]));
                
                if(!isset($this->_properties[$ipv->getPropertyId()]) || !is_object($this->_properties[$ipv->getPropertyId()])){
                    
			        $this->_properties[$ipv->getPropertyId()] = new SmartestItemPropertyValueHolder;
		            
			    }
			    
			    // var_dump($this->_properties[$ipv->getPropertyId()]->hasData());
			    
			    if(!$this->_properties[$ipv->getPropertyId()]->hasData()){
			        
		            $this->_properties[$ipv->getPropertyId()]->hydrateValueFromIpvObject($ipv);
		            
	            }
			    
			    // echo "<br />\n";
			    
			    // give the property the current item id, so that it knows which ItemPropertyValue record to retrieve in any future operations (though it isn't needed in this one)
			    $this->_properties[$ipv->getPropertyId()]->setContextualItemId($this->_item->getId());
			    // $this->_properties[$ipv->getPropertyId()]->hydrateValueFromIpvArray($propertyvalue);
			    
		    } 
		    
		    // all properties should now be represented.
		    // last jobs are:
		    //// 1. to make sure all property objects have value objects
		    //// 2. to give the value objects info about their properties, without doing more queries.
		    foreach($this->_properties as $pid=>$p){
		        // this function will automatically crate a value and save it
		        $p->getData()->hydratePropertyFromExteriorArray($p->getOriginalDbRecord());
		    }
		    
		    return true;
		
	    }else{
	        
	        return false;
	        
	    }
		
	}
	
	public function isHydrated(){
	    // var_dump($this->getItem()->isHydrated());
	    // return $this->getItem()->isHydrated();
	    return $this->_came_from_database;
	}
	
	public function getId(){
		return $this->getItem()->getId();
	}
	
	public function getName(){
		return $this->getItem()->getName();
	}
	
	public function getSlug(){
		return $this->getItem()->getSlug();
	}
	
	public function getWebid(){
		return $this->getItem()->getWebid();
	}
	
	public function getIsPublic(){
		return ($this->getItem()->getPublic() == 'TRUE') ? 'TRUE' : 'FALSE';
	}
	
	public function getItem(){
		return $this->_item;
	}
	
	public function getUrl(){
	    
	    if($this->getMetapageId()){
	        $page_id = $this->getMetapageId();
	    }else if($this->getModel()->getDefaultMetapageId()){
	        $page_id = $this->getModel()->getDefaultMetapageId();
	    }else{
	        return null;
	    }
	    
	    $lh = new SmartestCmsLinkHelper;
	    $lh->parse('metapage:id='.$page_id.':id='.$this->getId());
	    
	    return $lh->getUrl();
	    
	}
	
	public function getModel(){
	    
	    // print_r($this->_item);
	    
	    if(!$this->_model && is_object($this->_item) && $this->_item->getItemclassId()){
	        $model = new SmartestModel;
	        $model->hydrate($this->_item->getItemclassId());
	        $this->_model = $model;
	    }
	    
	    return $this->_model;
	    
	}
	
	public function getDescriptionField(){
	    
	    // default_description_property_id
	    if($this->getModel()->getDefaultDescriptionPropertyId()){
	        $property_id = $this->getModel()->getDefaultDescriptionPropertyId();
	        $property = $this->getPropertyByNumericKey($property_id);
	        return $property;
	    }else{
	        return null;
	    }
	    
	}
	
	public function getDescriptionFieldContents(){
	    
	    $property = $this->getDescriptionField();
	    
	    if(is_object($property)){
	        
	        $type_info = $property->getTypeInfo();
	        
	        if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
	            $asset = new SmartestAsset;
	            
	            if($asset->hydrate($this->getPropertyValueByNumericKey($property->getId()))){
	                // get asset content
	                return $asset->getContent();
	            }else{
	                // throw new SmartestException(sprintf("Asset with ID %s was not found.", $this->getPropertyValueByNumericKey($property_id)));
	                return null;
	            }
	            
	        }else{
	            return $this->getPropertyValueByNumericKey();
	        }
	        
	    }else{
	        throw new SmartestException(sprintf("Specified model description property with ID %s is not an object.", $property_id));
	    }
	    
	}
	
	public function compile($draft=false, $numeric_keys=false){
	    return $this->__toArray($draft, $numeric_keys);
	}
	
	public function __toArray($draft=false, $numeric_keys=false, $get_all_fk_property_options=false){
		// return associative array of property names and values
		$result = array();
		
		$result = $this->_item->__toArray(true);
		
		foreach($this->_varnames_lookup as $vn => $id){
		    
		    if($numeric_keys){
		        $key = $id;
		    }else{
		        $key = $vn;
		    }
		    
		    if($draft){
		        if(isset($this->_properties[$id]) && is_object($this->_properties[$id]->getData())){
		            $result[$key] = $this->_properties[$id]->getData()->getDraftContent();
	            }
	        }else{
	            if(isset($this->_properties[$id]) && is_object($this->_properties[$id]->getData())){
	                $result[$key] = $this->_properties[$id]->getData()->getContent();
                }
	        }
		}
		
		switch($this->getWorkflowStatus()){
		    case self::NOT_CHANGED:
		    $result['_workflow_status'] = 'Not changed';
		    break;
		    case self::CHANGES_APPROVED:
		    $result['_workflow_status'] = 'Approved and ready for publishing';
		    break;
		    default:
		    $result['_workflow_status'] = 'Awaiting approval';
		    break;
		}
		
		if(is_object($this->getModel())){
		    $result['_model'] = $this->getModel()->__toArray();
	    }
	    
		$result['_properties'] = $this->getPropertiesAsArrays($numeric_keys, $get_all_fk_property_options);
		
		ksort($result);
		
		return $result;
	}
	
	public function getProperties($numeric_keys=false){
	    
	    $result = array();
	    
	    foreach($this->_varnames_lookup as $vn => $id){
	    
	        if($numeric_keys){
	            $key = $id;
	        }else{
	            $key = $vn;
	        }
	    
	        $result[$key] = $this->_properties[$id];
	        
		}
		
	    return $result;
	    
	}
	
	public function getPropertyValueHolders(){
	    return $this->getProperties();
	}
	
	public function getPropertiesAsArrays($numeric_keys=false, $get_all_fk_property_options=false){
	    
	    $result = array();
	    
	    foreach($this->_varnames_lookup as $fn => $id){
	    
	        if($numeric_keys){
	            $key = $id;
	        }else{
	            $key = $vn;
	        }
	    
	        $result[$key] = $this->_properties[$id]->__toArray();
	        $result[$key]['_type_info'] = $this->_properties[$id]->getTypeInfo();
            
            /// var_dump($get_all_fk_property_options);
            
            if($this->_properties[$id]->isForeignKey() && $get_all_fk_property_options){
                $result[$key]['_options'] = $this->_properties[$id]->getPossibleValuesAsArrays();
            }
	        
		}
		
		return $result;
		
	}
	
	public function getTags(){
	    
	    return $this->_item->getTags();
	    
	}
	
	public function getTagsAsArrays(){
	    
	    return $this->_item->getTagsAsArrays();
	    
	}
	
	public function getAuthors(){
	    return $this->getItem()->getAuthors();
	}
	
	public function addAuthorById($user_id){
	    return $this->getItem()->addAuthorById($user_id);
	}
	
	public function getPropertyByNumericKey($key){
	    if(array_key_exists($key, $this->_properties)){
	        return $this->_properties[$key];
	    }else{
	        return null;
	    }
	}
	
	public function getPropertyValueByNumericKey($key, $draft=false){
	    if(array_key_exists($key, $this->_properties)){
	        if($this->getDraftMode()){
	            return $this->_properties[$key]->getData()->getDraftContent();
            }else{
                return $this->_properties[$key]->getData()->getContent();
            }
	    }else{
	        return null;
	    }
	}
	
	public function getPropertyValueByVarName($varname, $draft=false){
	    if(array_key_exists($varname, $this->_varnames_lookup)){
	        if($this->getDraftMode()){
	            return $this->_properties[$this->_varnames_lookup[$varname]]->getData()->getDraftContent();
            }else{
                return $this->_properties[$this->_varnames_lookup[$varname]]->getData()->getContent();
            }
	    }else{
	        return null;
	    }
	}
	
	public function setPropertyValueByNumericKey($key, $value){
	    
	    if(array_key_exists($key, $this->_properties)){
	        
	        if(!$this->_properties[$key]->getData()->getPropertyId()){
	            $this->_properties[$key]->getData()->setPropertyId($key);
	        }
	        
	        // print_r($this->_properties[$key]);
	        // print_r($value);
	        
	        return $this->_properties[$key]->getData()->setContent($value);
	        
	    }else{
	        return null;
	    }
	}
	
	public function __toString(){
		// return item's built-in name
		return $this->getItem()->getName();
	}
	
	/* public function __sleep(){
		$this->database = null;
	}
	
	public function __wakeUp(){
		$this->database =& SmartestPersistentObject::get('db:main');
	} */
	
	public function getWorkflowStatus(){
	    if($this->getItem()->getModified() > $this->getItem()->getLastPublished()){
	        
	        // page has changed since it was last published
	        if($this->getItem()->getChangesApproved()){
	            return self::CHANGES_APPROVED;
	        }else{
	            return self::AWAITING_APPROVAL;
	        }
	        
	    }else{
	        // page hasn't been modified
	        return self::NOT_CHANGED;
	    }
	}
	
	public function save(){
		
		$this->_save_errors = array();
		
		if(!$this->_came_from_database){
		    
		    // create web id for SmartestItem object first
		    $webid = SmartestStringHelper::random(32);
		    $this->_item->setWebId($webid);
		    
		    if($this->_item->getName()){
		        
		        $this->_item->save();
	            
	            foreach($this->_properties as $key => $value){
	                
	                $this->_properties[$key]->setContextualItemId($this->_item->getId());
	                $this->_properties[$key]->getData()->setItemId($this->_item->getId());
	                
	                if($this->_properties[$key]->getRequired() == 'TRUE'){
	                    if(!$this->_properties[$key]->getData()->getDraftContent()){
	                        // raise error
	                        $this->_save_errors[] = $key; // SmartestItemPropertyValue::OMISSION_ERROR;
	                    }else{
	                        // save the value
	                        $this->_properties[$key]->getData()->save();
	                    }
	                }else{
	                    // save the value regardless of whether it has a value
	                    $this->_properties[$key]->getData()->save();
	                }
	            }
	            
	        }else{
	            // raise error - the item had no name
	            $this->_save_errors[] = '_name';
	        }
	    }

        /* if($this->_came_from_database){
            
            // this is an update
            foreach($this->_properties as $id => $value){
                
            }
            
        }else{
            // we are inserting properties - item has just been created
            
        } */
        
        if(count($this->_save_errors)){
            return false;
        }else{
            return true;
        }
        
	}
	
	public function delete(){
		// mark as deleted
		if($this->_item instanceof SmartestItem && $this->_item->isHydrated()){
		    $this->_item->setDeleted(1);
		    $this->_item->save();
		}
	}
	
	public function publish(){
	    
	    // NOTE: the SmartestItemPropertyValue::publish() function checks the user's permission, so this one doesn't need to
	    foreach($this->_properties as $pid => $p){
	        
	        if($p instanceof SmartestItemPropertyValueHolder){
	            $p->getData()->publish();
	        }
	        
	    }
	    
	    $sql = "UPDATE TodoItems SET todoitem_is_complete='1' WHERE todoitem_type='SM_TODOITEMTYPE_PUBLISH_ITEM' AND todoitem_foreign_object_id='".$this->_item->getId()."'";
	    $this->database->rawQuery($sql);
	    
	    $this->_item->setChangesApproved(1);
	    $this->_item->setLastPublished(time());
	    $this->_item->setIsHeld(0);
	    $this->_item->setPublic('TRUE');
	    $this->_item->save();
	    
	    // print_r($this->getCacheFiles());
	    
	    foreach($this->getCacheFiles() as $file){
	        
	        unlink($file);
	        
	    }
	    
	}
	
	public function unPublish(){
	    $this->_item->setPublic('FALSE');
	    $this->_item->save();
	}
	
	public function isApproved(){
	    return ($this->_item->getChangesApproved() == 1) ? true : false;
	}
	
	public function getRelatedPagesAsArrays($draft_mode=false){
	    return $this->_item->getRelatedPagesAsArrays($draft_mode);
	}
    
    public static function getModelClassName($item_id){
	    
	    $item = new SmartestItem;
	    $item->hydrate($item_id);
	    $model_id = $item->getItemclassId();
	    
	    $model = new SmartestModel;
	    $model->hydrate($model_id);
	    return $model->getClassName();
	    
    }
    
    // builds a fully populated object of the correct type from just the primary key or webid
    public static function retrieveByPk($item_id, $dont_bother_with_class=false){
        
        /*  SELECT *
        FROM `ItemClasses` , `Items` , `ItemPropertyValues` , `ItemProperties`
        WHERE item_itemclass_id = itemclass_id
        AND itemproperty_itemclass_id = itemclass_id
        AND itempropertyvalue_item_id = item_id
        AND itempropertyvalue_property_id = itemproperty_id
        AND item_id =6166 */
        
        if(__CLASS__ == 'SmartestCmsItem'){
        
            if(!$dont_bother_with_class){
                $className = self::getModelClassName($item_id);
            }
        
            if(!$dont_bother_with_class && class_exists($className)){
                $object = new $className;
            }else{
                $object = new SmartestCmsItem;
            }
        
        }else{
            
            $className = __CLASS__;
            $object = new $className;
            
        }
        
        if($object->hydrate($item_id)){
            return $object;
        }else{
            return null;
        }
    }
    
    protected function getDataStore(){
        return SmartestPersistentObject::get('centralDataHolder');
    }
	
}
