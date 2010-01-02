<?php

class SmartestItemPublicComment extends SmartestComment{
    
    protected $_simple_item;
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_COMMENTTYPE_ITEM_PUBLIC');
        }
        
        if(!$this->getStatus()){
            $this->setStatus('SM_COMMENTSTATUS_PENDING');
        }
        
        parent::save();
        
    }
    
    public function setItemId($id){
        
        $this->setObjectId($id);
        
    }
    
    public function getItemId(){
        
        return $this->getObjectId();
        
    }
    
    public function hydrateWithSimpleItem($data){
        
        if($this->hydrate($data)){
            
            $item = new SmartestItem;
            
            if($item->hydrate($data)){
                $this->_simple_item = $item;
                return true;
            }else{
                return false;
            }
            
        }else{
            return false;
        }
        
    }
    
    public function getSimpleItem(){
        
        if(!$this->_simple_item instanceof SmartestItem){
        
            $item = new SmartestItem;
        
            if($item->find($this->getObjectId())){
                
                $this->_simple_item = $item;
                return $item;
            
            }
        
        }else{
            
            return $this->_simple_item;
            
        }
        
    }
    
    public function approve(){
        
        $this->setStatus('SM_COMMENTSTATUS_APPROVED');
        $this->save();
        
        $item = $this->getSimpleItem();
        $item->setNumComments(((int) $item->getNumComments()) + 1);
        $item->save();
        
        $item->refreshCache();
        
    }
    
    public function makePending(){
        
        $old_status = $this->getStatus();
        
        $this->setStatus('SM_COMMENTSTATUS_PENDING');
        $this->save();
        
        $item = $this->getSimpleItem();
        $item->setNumComments(((int) $item->getNumComments()) - 1);
        $item->save();
        
        if($old_status == 'SM_COMMENTSTATUS_APPROVED'){
            $item->refreshCache();
        }
        
    }
    
    public function reject(){
        
        $old_status = $this->getStatus();
        
        $this->setStatus('SM_COMMENTSTATUS_REJECTED');
        $this->save();
        
        $item = $this->getSimpleItem();
        $item->setNumComments(((int) $item->getNumComments()) - 1);
        $item->save();
        
        if($old_status == 'SM_COMMENTSTATUS_APPROVED'){
            $item->refreshCache();
        }
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "item":
            return $this->_simple_item;
        }
        
        return parent::offsetGet($offset);
        
    }
  
}