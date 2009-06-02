<?php

class SmartestAssetComment extends SmartestComment{
    
    protected $_user;
    
    public function hydrate($array){
        
        parent::hydrate($array);
        
        if(isset($array['user_id']) && is_numeric($array['user_id'])){
            $u = new SmartestUser;
            $u->hydrate($array);
            $this->_user = $u;
        }
        
    }
    
    public function getAssetId(){
        return $this->getObjectId();
    }
    
    public function setAssetId($id){
        return $this->setObjectId($id);
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_COMMENTTYPE_ASSET_PRIVATE');
        }
        
        parent::save();
        
    }
    
}