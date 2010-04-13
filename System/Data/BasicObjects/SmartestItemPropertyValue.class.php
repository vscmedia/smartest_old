<?php

class SmartestItemPropertyValue extends SmartestBaseItemPropertyValue{
    
    protected $_item;
    protected $_property = null;
    protected $_value_object = null;
    
    const OMISSION_ERROR = 100;
    
    function init($item_id, $property_id){
        
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
        return $this->getContent();
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
                
                    if(method_exists($obj, 'setDraftMode')){
                        $obj->setDraftMode($draft);
                    }
                
                    if($class == 'SmartestRenderableAsset'){
                        $obj->setAdditionalRenderData($this->getInfo);
                    }
                
                    if($class == 'SmartestDropdownOption'){
                        $obj->hydrateByValueWithDropdownId($raw_data, $this->getProperty()->getForeignKeyFilter());
                    }else{
                        // get the asset, dropdown menu option or what have you
                        if($obj instanceof SmartestCmsItem){
                        
                            // only bother trying to hydrate the SmartestCmsItem subclass if we have an actual foreign key to use:
                            if(strlen($raw_data)){
                                $obj->hydrate($raw_data);
                            }
                        
                        }else{
                            $obj->find($raw_data);
                        }
                    }
                
                }else{
                    // get a SmartestBasicType object
                    $obj = new $class;
                    $obj->setValue($raw_data);
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
        
        switch($this->getProperty()->getDatatype()){
            
            default:
                $data = $this->getValueObject($draft);
                break;
        }
        
	    return $data;
        
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
                $this->_properties['content'] = $this->_properties['draft_content'];
                $this->_modified_properties['content'] = addslashes($this->_properties['draft_content']);
                $this->_properties['live_info'] = $this->_properties['draft_info'];
                $this->_modified_properties['live_info'] = str_replace("'", "\\'", $this->_properties['draft_info']);
                $this->save();
            }
            
            if($this->getProperty()->getDatatype() == 'SM_DATATYPE_ASSET'){
                
                $asset = new SmartestAsset;
                $asset->hydrate($this->_properties['content']);
                
                if($asset->isEditable() && $asset->isParsable()){
                    $asset->getTextFragment()->publish();
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
    
    public function setContent($data){
        
        $filtered_data = $this->filterNewContent($data);
        
        $this->_properties['draft_content'] = $filtered_data;
        $this->_modified_properties['draft_content'] = addslashes($filtered_data);
        
        if(isset($this->_properties['item_id']) && is_numeric($this->_properties['item_id'])){
            // $this->save();
        }
        
        return true;
    }
    
    public function setDraftContent($data){
        return $this->setContent($data);
    }
    
}
