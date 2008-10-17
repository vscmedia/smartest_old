<?php

// this class is not supposed to be instantiated directly.

class SmartestAssetIdentifier extends SmartestBaseAssetIdentifier{
    
    protected $_ancestor_chain = array();
    protected $_level;
    protected $_loaded = false;
    protected $_page;
    protected $_asset_class;
    protected $_simple_item;
    
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'assetidentifier_';
		$this->_table_name = 'AssetIdentifiers';
				
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
        return $this->_properties['draft_asset_id'] != $this->_properties['live_asset_id'];
    }
    
    public function getAssetClass(){
        
        if(!$this->_asset_class){
            
            $sql = "SELECT * FROM AssetClasses WHERE assetclass_id='".$this->_properties['assetclass_id']."'";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
                $ac = new SmartestAssetClass;
                $ac->hydrate($result[0]);
                $this->_asset_class = $ac;
            }
            
            // print_r($result);
            
        }
        
        return $this->_asset_class;
        
    }
    
    public function getCurrentDefinitionId($draft=false){
        
        if($draft){
            $field = 'draft_asset_id';
        }else{
            $field = 'live_asset_id';
        }
        
        $this->_properties[$field];
        
    }
    
    public function getSimpleItem($draft=false){
        
        if($this->getAssetClass()->getType() == 'SM_ASSETCLASS_ITEM_SPACE'){
            
            if(!$this->_simple_item){
                if($this->_item){
                    return $this->_item->getSimpleItem();
                }else{
                    $item = new SmartestItem;

                    if($item->hydrate($this->getCurrentDefinitionId($draft))){
                        $this->_simple_item = $item;
                    }else{
                        return $item;
                    }

                }
            }

            return $this->_simple_item;
            
        }
        
    }
    
    public function getPage(){
        
        if(!$this->_page){
            
            $p = new SmartestPage;
            
            if($p->hydrate($this->_properties['page_id'])){
                $this->_page = $p;
            }
            
        }
        
        return $this->_page;
        
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
	
	public function publish($do_save=true){
	    
	    $this->setField('LiveRenderData', $this->_properties['draft_render_data']);
		$this->setField('LiveAssetId', $this->_properties['draft_asset_id']);
		
		// print_r($this->_properties['draft_asset_id']);
		
		// print_r($this->_modified_properties);
		
		if($do_save){
		    $this->save();
	    }
	    
	    // print_r($this);
	    
	}
	
	public function hydrateFromGiantArray($array){
        
        $this->hydrate($array);
        
        $assetclass = new SmartestAssetClass;
        $assetclass->hydrate($array);
        $this->_asset_class = $assetclass;
        
        $asset = new SmartestAsset;
        $asset->hydrate($array);
        $this->_asset = $asset;
        
    }

}