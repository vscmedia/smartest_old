<?php

class SmartestItemPublicComment extends SmartestComment{
  
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
    
    public function getItemId($id){
        
        return $this->getObjectId();
        
    }
    
    public function getSimpleItem(){
        
        $item = new SmartestItem;
        
        if($item->find($this->getObjectId())){
            
            return $item;
            
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
  
}