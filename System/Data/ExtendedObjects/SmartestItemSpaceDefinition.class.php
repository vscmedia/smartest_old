<?php

class SmartestItemSpaceDefinition extends SmartestAssetIdentifier{
    
    protected $_itemspace;
    protected $_item;
    protected $_simple_item;
    protected $_page;
    protected $_loaded = false;
    
    public function __objectConstruct(){
        
        $this->_table_prefix = 'assetidentifier_';
		$this->_table_name = 'AssetIdentifiers';
        
    }
    
    public function load($name, $page, $draft=false){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            $this->_itemspace = new SmartestItemSpace;
            
            if($this->_itemspace->exists($name, $page->getSiteId())){
                
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_itemspace->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    $this->_loaded = false;
                    return true;
                    
                }else{
                    // Placeholder not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }
            
        }
        
    }
    
    public function getSimpleItem($draft=false){
        
        if(!$this->_simple_item){
            if($this->_item){
                $this->_simple_item = $this->_item->getSimpleItem();
            }else{
                $item = new SmartestItem;
                if($draft){
                    $field = 'draft_asset_id';
                }else{
                    $field = 'live_asset_id';
                }
                
                if($item->hydrate($this->_properties['live_asset_id'])){
                    $this->_simple_item = $item;
                }else{
                    return $item;
                }
                
            }
        }
        
        return $this->_simple_item;
        
    }
    
    public function getItem($simple=true, $draft=false){
        
        if($simple){
            
            return $this->getSimpleItem();
            
        }else{
            
            if(!$this->_item){
                if($draft){
                    $this->_item = SmartestCmsItem::retrieveByPk($this->_properties['draft_asset_id']);
                }else{
                    $this->_item = SmartestCmsItem::retrieveByPk($this->_properties['live_asset_id']);
                }
            }
            
            return $this->_item;
        }
        
    }
    
    public function getItemSpaceId($id){
        
        return $this->_properties['assetclass_id'];
        
    }
    
    public function setItemSpaceId($id){
        
        $id = (int) $id;
        $this->setField('AssetclassId', $id);
        
    }
    
    public function setDraftItemId($id){
        
        $id = (int) $id;
        $this->setField('DraftAssetId', $id);
        
    }
    
    public function getItemId($draft){
        
        $id = (int) $id;
        
        if($draft){
            return $this->getField('DraftAssetId');
        }else{
            return $this->getField('LiveAssetId');
        }
        
    }
    
    public function getItemSpace(){
        return $this->_itemspace;
    }
    
    public function hydrateFromGiantArray($array){
        
        $this->hydrate($array);
        
        $itemspace = new SmartestItemSpace;
        $itemspace->hydrate($array);
        $this->_itemspace = $itemspace;
        
        $this->_item = SmartestCmsItem::retrieveByPk($array['item_id']);
        
    }
    
}