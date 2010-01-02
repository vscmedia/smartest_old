<?php

class SmartestPlaceholderDefinition extends SmartestAssetIdentifier{
    
    protected $_placeholder;
    protected $_asset;
    protected $_page;
    protected $_is_linkable = false;
    
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('PlaceholderId', 'assetclass_id');
		$this->_table_prefix = 'assetidentifier_';
		$this->_table_name = 'AssetIdentifiers';
		
	}
	
	public function load($name, $page, $draft=false, $item_id=''){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            
            $placeholder = new SmartestPlaceholder;
            
            $sql = "SELECT * FROM AssetClasses WHERE assetclass_type != 'SM_ASSETCLASS_CONTAINER' AND assetclass_type != 'SM_ASSETCLASS_ITEM_SPACE' AND assetclass_name='".$name."' AND (assetclass_site_id='".$page->getSiteId()."' OR assetclass_shared=1)";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
            
                $placeholder->hydrate($result[0]);
                
                $this->_asset_class = $placeholder;
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_asset_class->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                
                if(is_numeric($item_id)){
                    $sql .= " AND assetidentifier_item_id='".$item_id."'";
                }else{
                    $sql .= " AND assetidentifier_item_id IS NULL";
                }
                
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    
                    $asset_types = SmartestDataUtility::getAssetTypes();
                    $assetclass_types = SmartestDataUtility::getAssetClassTypes();
                    
                    if($draft){
                        $asset_id = $this->getDraftAssetId();
                    }else{
                        $asset_id = $this->getLiveAssetId();
                    }
                    
                    $asset = new SmartestRenderableAsset;
                    $asset->find($asset_id);
                    
                    if(in_array($placeholder->getType(), array('SM_ASSETTYPE_IMAGE', 'SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_SL_TEXT'))){
                        $this->_is_linkable = true;
                    }else{
                        $this->_is_linkable = false;
                    }
                    
                    return $asset_id;
                    
                }else{
                    // Placeholder not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }else{
                // Placeholder by that name doesn't exist
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function loadForUpdate($name, $page, $item_id=false){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            
            $placeholder = new SmartestPlaceholder;
            
            if($placeholder->hydrateBy('name', $name)){
                
                $this->_asset_class = $placeholder;
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_asset_class->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                
                if(is_numeric($item_id)){
                    $sql .= " AND assetidentifier_item_id='".$item_id."'";
                }else{
                    $sql .= " AND assetidentifier_item_id IS NULL";
                }
                
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    
                    if($placeholder->getType() != "SM_ASSETTYPE_CONTAINER_TEMPLATE"){
                        
                        $asset = new SmartestAsset;
                        $this->_asset = $asset;
                        $this->_asset->setIsDraft($draft);
                        $this->_loaded = true;
                        return true;
                        
                    }else{
                        
                        // asset class being filled is a container
                        $this->_loaded = false;
                        return false;
                        
                    }
                    
                    
                }else{
                    
                    // placeholder not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }else{
                // Placeholder by that name doesn't exist
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function getAsset($draft=false){
        
        if(!$this->_asset){
            
            if($draft){
                $asset_id = $this->getDraftAssetId();
            }else{
                $asset_id = $this->getLiveAssetId();
            }
            
            $a = new SmartestRenderableAsset;
            
            if($a->find($asset_id)){
                $this->_asset = $a;
            }else{
                // no asset defined
            }
            
        }
        
        return $this->_asset;
        
    }
    
    public function getDefaultAssetRenderData($draft=false){
        
        $asset = $this->getAsset($draft);
        
        if(is_object($asset)){
            return unserialize($asset->getParameterDefaults());
        }
        
    }
    
    public function getPlaceholder(){
	    
	    if(!is_object($this->_asset_class)){
	        $ph = new SmartestPlaceHolder;
	        $ph->hydrate($this->getAssetClassId());
	        $this->_asset_class = $ph;
        }
	    
	    return $this->_asset_class;
	}
	
	public function getType(){
	    return $this->getPlaceholder()->getType();
	}
	
	public function hydrateFromGiantArray($array){
        
        $this->hydrate($array);
        
        if(isset($array['assetclass_id'])){
            $placeholder = new SmartestPlaceHolder;
            $placeholder->hydrate($array);
            $this->_asset_class = $placeholder;
        }
        
        if(isset($array['asset_id'])){
            $asset = new SmartestRenderableAsset;
            $asset->hydrate($array);
            $this->_asset = $asset;
        }
        
        if(isset($array['page_id'])){
            $page = new SmartestPage;
            $page->hydrate($array);
            $this->_page = $page;
        }
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "asset":
            return $this->_asset;
            case "page":
            return $this->_page;
            case "placeholder":
            return $this->_asset_class;
        }
        
        return parent::offsetGet($offset);
        
    }

}