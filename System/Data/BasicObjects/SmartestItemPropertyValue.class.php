<?php

class SmartestItemPropertyValue extends SmartestBaseItemPropertyValue{
    
    protected $_item;
    protected $_property = null;
    protected $_value_object = null;
    
    const OMISSION_ERROR = 100;
    
    public function init($item_id, $property_id){
        
        /* $sql = "SELECT * FROM ItemPropertyValues WHERE itempropertyvalue_item_id='".$item_id."' AND itempropertyvalue_property_id='".$property_id."'";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $this->_item = SmartestCmsItem::retrieveByPk($result[0]['itempropertyvalue_item_id']);
            $this->_property = new SmartestItemProperty;
            $this->_property->hydrate($result[0]['itempropertyvalue_property_id']);
            return $this->hydrate($result[0]['itempropertyvalue_id']);
        }else{
            $this->_item = new SmartestCmsItem;
            $this->_property = new SmartestItemProperty;
            return false;
        } */
    }
    
    public function __toString(){
        return ''.$this->getContent();
    }
    
    public function getProperty(){
        
        if(!$this->_property){
            $property = new SmartestItemProperty;
            $property->hydrate($this->_properties['property_id']);
            $this->_property = $property;
        }
        
        return $this->_property;
    }
    
    public function hydratePropertyFromExteriorArray($array){
        if(!$this->_property){
            $property = new SmartestItemProperty;
            $property->hydrate($array);
            $this->_property = $property;
        }
    }
    
    public function hasItem(){
        return ($this->_item instanceof SmartestCmsItem);
    }
    
    public function setItem(SmartestCmsItem $item){
        $this->_item = $item;
    }
    
    public function getItem(){
        if($this->hasItem()){
            return $this->_item;
        }else{
            if($this->getItemId()){
                $this->_item = SmartestCmsItem::retrieveByPk($this->getItemId());
            }else{
                // item not provided and IPV has no item ID so can't work it out for itself
            }
        }
    }
    
    public function getRawValue($draft=false){
        
        if($draft){
            return $this->_properties['draft_content'];
        }else{
            return $this->_properties['content'];
        }
        
    }
    
    protected function getValueObject($draft=false){
        
        if(!$this->_value_object){
            
            $p = $this->getProperty();
            
            if($p->getId()){
                
                $t = $p->getTypeInfo();
                $class = $t['class'];
                
                if(!class_exists($class)){
                    throw new SmartestException("Class ".$class." required for handling properties of type ".$t['id']." does not exist.");
                }
                
                if($draft){
                    $raw_data = $this->_properties['draft_content'];
                }else{
                    $raw_data = $this->_properties['content'];
                }
                
                if($t['valuetype'] == 'foreignkey'){
                
                    // these first two options are both hacks, but will be fixed in the future
                    if($class == 'SmartestCmsItem'){
                        // get model id
                        $model_id = $this->getProperty()->getForeignKeyFilter();
                        $model = new SmartestModel;
                        $model->hydrate($model_id);
                        $class = $model->getClassName();
                    }
                
                    $obj = new $class;
                
                    if($obj instanceof SmartestDualModedObject){
                        $obj->setDraftMode($draft);
                    }else if($obj instanceof SmartestCmsItemSet){
                        $obj->setRetrieveMode($draft ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT);
                        $obj->setConstituentItemChaining(true);
                    }
                
                    if($class == 'SmartestDropdownOption'){
                        // $obj->hydrateByValueWithDropdownId($raw_data, $this->getProperty()->getForeignKeyFilter());
                        $obj->hydrateFromStorableFormat($raw_data);
                    }else{
                        // get the asset, dropdown menu option or what have you
                        if($obj instanceof SmartestCmsItem){
                            // only bother trying to hydrate the SmartestCmsItem subclass if we have an actual foreign key to use:
                            if(strlen($raw_data) && is_numeric($raw_data)){
                                $obj->find($raw_data);
                            }
                        }else{
                            if($this->getProperty()->isManyToMany()){
                                // Do nothing
                            }else{
                                $obj->find($raw_data);
                            }
                        }
                    }
                    
                    if($class == 'SmartestRenderableAsset'){
                        $obj->setAdditionalRenderData($this->getInfo($draft));
                    }
                    
                    if($class == 'SmartestRenderableSingleItemTemplateAsset'){
                        $this->getItem();
                        if($this->hasItem()){
                            $this->getItem()->disableTemplateProperty($this->_properties['property_id']);
                            $obj->setItem($this->getItem());
                        }
                    }
                    
                    if($class == 'SmartestCmsItemSet'){
                        if($draft){
                            $obj->setDefaultFetchMode(2);
                        }else{
                            $obj->setDefaultFetchMode(11);
                        }
                    }
                
                }else if($t['valuetype'] == 'manytomany'){
                    
                    if($draft){
                        $mode = constant('SM_MTMLOOKUPMODE_DRAFT');
                    }else{
                        $mode = constant('SM_MTMLOOKUPMODE_PUBLIC');
                    }
                    
                    $r = new SmartestManyToManyMappedRelationship($this->getProperty()->getManyToManyRelationshipType());
                    $r->setCentralEntityObjectId($this->getId());
                    $r->setCentralEntityByIndex($this->getProperty()->getManyToManyRelationshipItemEntityIndex());
                    $r->setTargetEntityByIndex($this->getProperty()->getManyToManyRelationshipMappedObjectEntityIndex());
                    
                    if(!$draft){
                        $r->addConstraint('Items.item_public', 'TRUE');
                    }
                    
                    // fix goes here
                    
                    $obj = new $class;
                    $obj->hydrateFromStoredIdsArray($r->getIds($mode), $draft);
                    return $obj;
                    
                }else if($t['valuetype'] == 'auto'){
                    
                    if($t['id'] == 'SM_DATATYPE_AUTO_ITEM_FK'){
                    
                        $ids = array();
                    
                        $p = $this->getProperty();
                    
                        $field = $draft ? 'itempropertyvalue_draft_content' : 'itempropertyvalue_content';
                    
                        $sql = "SELECT item_id FROM Items, ItemProperties, ItemPropertyValues WHERE item_deleted !=1 AND item_itemclass_id=itemproperty_itemclass_id AND itempropertyvalue_item_id=item_id AND itempropertyvalue_property_id = itemproperty_id AND ".$field."='".$this->getItemId()."' AND itemproperty_id='".$p->getForeignKeyFilter()."'";
                        $result = $this->database->queryToArray($sql);
                        
                        foreach($result as $r){
                            $ids[] = $r['item_id'];
                        }
                        
                        $obj = new $class;
                        $obj->hydrateFromStoredIdsArray($ids, $draft);
                        return $obj;
                    
                    }
                    
                }else{
                    // get a SmartestBasicType object
                    $obj = new $class;
                    $obj->hydrateFromStorableFormat($raw_data);
                }
            
                $this->_value_object = $obj;
            
            }else{
                
                SmartestLog::getInstance('system')->log("Item property found for non existent property ID: ".$this->getPropertyId(), SmartestLog::WARNING);
                
            }
        
        }
        
        return $this->_value_object;
        
    }
    
    protected function assignNewValueToValueObject($raw_data){
        
        
        
    }
    
    // function that processes data for output/GET functions
    // Now no longer used
    protected function processContent($draft=false){
        
        /* switch($this->getProperty()->getDatatype()){
            
            default:
                $data = 
                break;
        } */
        
	    return $this->getValueObject($draft);
        
    }
    
    // function that processes data for input/SET functions
    // converts data to storeable format
    protected function filterNewContent($raw_data){
        
        switch($this->getProperty()->getDatatype()){
            
            case "SM_DATATYPE_DATE":
                
                if(is_array($raw_data) && isset($raw_data['Y']) && isset($raw_data['M']) && isset($raw_data['D'])){
                    $time = mktime(0, 0, 0, $raw_data['M'], $raw_data['D'], $raw_data['Y']);
                }
                
                $data = $time;
                break;
            
            case "SM_DATATYPE_BOOLEAN":
                $data = $raw_data;
                break;
                
            default:
                $data = SmartestStringHelper::sanitize($raw_data);
                break;
        }
        
        return $data;
        
    }
    
    public function publish(){
        
        if(is_object(SmartestSession::get('user'))){
            
            $user = SmartestSession::get('user');
            
            if($user->hasToken('publish_approved_items') || $user->hasToken('publish_all_items')){
                
                if($this->getProperty()->isManyToMany()){
                    
                    // publish many-to-many relationship
                    $r = new SmartestManyToManyMappedRelationship($this->getProperty()->getManyToManyRelationshipType());
                    $r->setCentralEntityObjectId($this->getId());
                    $r->setCentralEntityByIndex($this->getProperty()->getManyToManyRelationshipItemEntityIndex());
                    $r->setTargetEntityByIndex($this->getProperty()->getManyToManyRelationshipMappedObjectEntityIndex());
                    $r->publish();
                    
                }else{
                    
                    $this->_properties['content'] = $this->_properties['draft_content'];
                    $this->_modified_properties['content'] = addslashes($this->_properties['draft_content']);
                    $this->_properties['live_info'] = $this->_properties['draft_info'];
                    $this->_modified_properties['live_info'] = str_replace("'", "\\'", $this->_properties['draft_info']);
                    $this->save();
                    
                    if($this->getProperty()->getDatatype() == 'SM_DATATYPE_ASSET'){

                        $asset = new SmartestAsset;
                        $asset->hydrate($this->_properties['content']);

                        if($asset->isEditable() && $asset->isParsable()){
                            $asset->getTextFragment()->publish();
                        }

                    }
                    
                }
            }
            
            
            
        }
    }
    
    public function getContent(){
        
        return $this->getValueObject();
        
    }
    
    public function getDraftContent(){
        
        return $this->getValueObject(true);
        
    }
    
    public function getInfoField($field_name){
	    
	    $field_name = SmartestStringHelper::toVarName($field_name);
	    $data = $this->getInfo();
	    
	    if(isset($data[$field_name])){
	        return $data[$field_name];
	    }else{
	        return null;
	    }
	}
	
	public function setInfoField($field_name, $data){
	    
	    $field_name = SmartestStringHelper::toVarName($field_name);
	    $existing_data = $this->getInfo(true);
	    $existing_data[$field_name] = $data;
	    $this->setInfo($existing_data);
	    
	}
	
	public function getInfo($draft_mode=false){
	    
	    $data = @unserialize($this->_getInfo($draft_mode));
	    
	    if(is_array($data)){
	        return $data;
	    }else{
	        return array($data);
	    }
	    
	}
	
	public function setInfo($data){
	    
	    if(!is_array($data)){
	        $data = array($data);
	    }
	    
	    $this->_setInfo(serialize($data));
	    
	}
	
	protected function _getInfo($draft_mode=false){
	    if($draft_mode){
	        return $this->_properties['draft_info'];
        }else{
            return $this->_properties['live_info'];
        }
	}
	
	protected function _setInfo($serialized_data){
	    $this->setField('draft_info', $serialized_data);
	}
	
	/* public function prepareDataForStorage($data){
	    if(is_object($data)){
	        
	    }
	} */
    
    public function setContent($data, $save=true, $force_live=false){
        
        if(is_object($data)){
            $filtered_data = $data->getStorableFormat();
        }else{
            if($value_obj = SmartestDataUtility::objectizeFromRawFormData($data, $this->getProperty()->getDatatype())){
                if($this->getProperty()->isManyToMany()){
                    
                }else{
                    $filtered_data = $value_obj->getStorableFormat();
                }
            }else{
                SmartestLog::getInstance('system')->log("Could not set content of SmartestItemPropertyValue object, because a value object was not given and none could be created.");
            }
        }
        
        if($this->getProperty()->isManyToMany()){
            
            // add ids to many to many table at draft status if they aren't already there
            $r = new SmartestManyToManyMappedRelationship($this->getProperty()->getManyToManyRelationshipType());
            $r->setCentralEntityObjectId($this->getId());
            $r->setCentralEntityByIndex($this->getProperty()->getManyToManyRelationshipItemEntityIndex());
            $r->setTargetEntityByIndex($this->getProperty()->getManyToManyRelationshipMappedObjectEntityIndex());
            $r->updateTo($data);
            
        }else{
            
            $field = $force_live ? 'content' : 'draft_content';
            
            $this->_properties[$field] = $data;
            $this->_modified_properties[$field] = addslashes($filtered_data);

            if(isset($this->_properties['item_id']) && is_numeric($this->_properties['item_id']) && $save){
                $this->save();
            }
        }
        
        return true;
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

		$sql .= " WHERE ".$this->_table_prefix."property_id='".$this->_properties['property_id']."' AND ".$this->_table_prefix."item_id='".$this->_properties['item_id']."'";
		
		return $sql;
	    
	}
    
    public function setDraftContent($data){
        return $this->setContent($data);
    }
    
    public function _setContent($v, $draft=true){
        $field = $draft ? 'draft_content' : 'content';
        $this->setField($field, $v);
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "storable_format":
            return $this->getValueObject()->getStorableFormat();
            case "raw":
            return $this->getRawValue();
        }
        
        return parent::offsetGet($offset);
        
    }
    
}
