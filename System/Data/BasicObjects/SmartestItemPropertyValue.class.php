<?php

class SmartestItemPropertyValue extends SmartestDataObject{
    
    protected $_item;
    protected $_property = null;
    
    const OMISSION_ERROR = 100;
    
    protected function __objectConstruct(){
        
        $this->_table_prefix = 'itempropertyvalue_';
		$this->_table_name = 'ItemPropertyValues';
		
    }
    
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
    
    protected function processContent($draft=false){
        
        if($draft){
            $raw_data = $this->_properties['draft_content'];
        }else{
            $raw_data = $this->_properties['content'];
        }
	
       // var_dump($raw_data.' ');
        // print_r($this->_properties);
	
        switch($this->getProperty()->getDatatype()){
            
            case "SM_DATATYPE_DATE":
                
                if(!is_numeric($raw_data)){
                    $time = strtotime($raw_data);
                }else{
                    $time = $raw_data;
                }
                
                $data = array(
                    'Y'=>date('Y', $time),
                    'M'=>date('m', $time),
                    'D'=>date('d', $time)
                );
                
                break;
            
            case "SM_DATATYPE_BOOLEAN":
                
                if($raw_data == "FALSE" || $raw_data == "0"){
                    $data = false;
                }else{
                    $data = true;
                }
                
                // echo $raw_data;
                
                // var_dump($data);
                
                break;
                
            default:
                $data = $raw_data;
                break;
        }
        
	// var_dump($data);
	
        return $data;
        
    }
    
    protected function filterNewContent($raw_data){
        
        // echo $this->getProperty()->getDatatype();
        
        switch($this->getProperty()->getDatatype()){
            
            case "SM_DATATYPE_DATE":
                
                // print_r($raw_data);
                
                if(is_array($raw_data) && isset($raw_data['Y']) && isset($raw_data['M']) && isset($raw_data['D'])){
                    $time = mktime(0, 0, 0, $raw_data['M'], $raw_data['D'], $raw_data['Y']);
                    // echo $time;
                }
                
                $data = $time;
                break;
            
            case "SM_DATATYPE_BOOLEAN":
                // echo $raw_data;
                $data = $raw_data;
                break;
                
            default:
                $data = SmartestStringHelper::sanitize($raw_data);
                
                break;
        }
        
        return $data;
        
    }
    
    public function publish(){
        
        if(is_object(SmartestPersistentObject::get('user'))){
            
            $user = SmartestPersistentObject::get('user');
            
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
        
        return $this->processContent();
        
    }
    
    public function getDraftContent(){
        
        return $this->processContent(true);
        
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
	    $this->setField('DraftInfo', $serialized_data);
	}
    
    public function setContent($data){
        
        $filtered_data = $this->filterNewContent($data);
        
        $this->_properties['draft_content'] = $filtered_data;
        $this->_modified_properties['draft_content'] = addslashes($filtered_data);
        
        if(isset($this->_properties['item_id']) && is_numeric($this->_properties['item_id'])){
            $this->save();
        }
        
        return true;
    }
    
    public function setDraftContent($data){
        return $this->setContent($data);
    }
    
}
