<?php

class SmartestAssetGalleryMembership extends SmartestManyToManyLookup{
    
    protected $_asset;
    protected $_group;
    
    public function hydrate($raw_data){
        
        if(isset($raw_data['asset_id'])){
            $asset = new SmartestRenderableAsset;
            $asset->hydrate($raw_data);
            $this->_asset = $asset;
        }
        
        if(isset($raw_data['set_id'])){
            $group = new SmartestAssetGroup;
            $group->hydrate($raw_data);
            $this->_group = $group;
        }
        
        return parent::hydrate($raw_data);
        
    }
    
    public function getAsset(){
        
        if(!$this->_asset){
            $asset = new SmartestRenderableAsset;
            if($asset->find($this->getAssetId())){
                $this->_asset = $asset;
            }
        }
        
        return $this->_asset;
    }
    
    public function getAssetId(){
        return $this->getEntityForeignKeyValue(1);
    }
    
    public function setAssetId($id){
        return $this->setEntityForeignKeyValue(1, (int) $id);
    }
    
    public function getGroup(){
        
        if(!$this->_group){
            $group = new SmartestAssetGroup;
            if($group->find($this->getGroupId())){
                $this->_group = $group;
            }
        }
        
        return $this->_group;
    }
    
    public function getGroupId(){
        return $this->getEntityForeignKeyValue(2);
    }
    
    public function setGroupId($id){
        return $this->setEntityForeignKeyValue(2, (int) $id);
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_MTMLOOKUP_ASSET_GALLERY_MEMBERSHIP');
        }
        
        return parent::save();
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "file":
            case "asset":
            return $this->getAsset();
            
            case "caption":
            return new SmartestString($this->getContextDataField('caption'));
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}