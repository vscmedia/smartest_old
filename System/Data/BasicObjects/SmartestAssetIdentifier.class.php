<?php

// this class is not supposed to be instantiated directly.

class SmartestAssetIdentifier extends SmartestDataObject{
    
    protected $_ancestor_chain = array();
    protected $_level;
    protected $_loaded = false;
    
	protected function __objectConstruct(){
		
		throw new SmartestException('SmartestAssetIdentifier is not supposed to be instantiated directly. Please use SmartestPlaceholderDefinition or SmartestContainerDefinition.');
		
	}
	
	public function getLevel(){
	    return $this->_level;
	}
	
	protected function setLevel(){
	    
	}
	
	public function isLoaded(){
        return $this->_loaded;
    }
    
    public function hasChanged(){
        return $this->_properties['draft_asset_id'] == $this->_properties['live_asset_id'];
    }
    
    public function setRenderDataField($field_name, $new_data){ 
	    
	    $field_name = SmartestStringHelper::toVarName($field_name);
	    $data = $this->getRenderData(true);
	    $data[$field_name] = $new_data;
	    $this->setRenderData($data);
	    
	}
	
	public function getRenderDataField($field_name, $draft_mode=false){
	    
	    $data = $this->getRenderData($draft_mode);
	    
	    $field_name = SmartestStringHelper::toVarName($field_name);
	    
	    if(isset($data[$field_name])){
	        return $data[$field_name];
	    }else{
	        return null;
	    }
	}
	
	public function getRenderData($draft_mode=false){
	    
	    if($data = @unserialize($this->_getRenderData($draft_mode))){
	        
	        if(is_array($data)){
	            return $data;
            }else{
                return array($data);
            }
	    }else{
	        return array();
	    }
	}
	
	public function setRenderData($data){
	    
	    if(!is_array($data)){
	        $data = array($data);
	    }
	    
	    $this->_setRenderData(serialize($data));
	    
	    // echo $this->_modified_properties['render_data'];
	    
	}
	
	protected function _getRenderData($draft_mode=false){
	    if($draft_mode){
	        return $this->getDraftRenderData();
	    }else{
	        return $this->getLiveRenderData();
	    }
	}
	
	protected function _setRenderData($serialized_data){
	    $this->_properties['draft_render_data'] = $serialized_data;
		$this->_modified_properties['draft_render_data'] = $serialized_data;
	}
	
	public function getDraftRenderData(){
	    return $this->_properties['draft_render_data'];
	}
	
	public function getLiveRenderData(){
	    return $this->_properties['live_render_data'];
	}

}