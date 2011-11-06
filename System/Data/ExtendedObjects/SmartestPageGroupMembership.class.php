<?php

class SmartestPageGroupMembership extends SmartestManyToManyLookup{
    
    protected $_page;
    protected $_group;
    
    public function hydrate($raw_data){
        
        if(isset($raw_data['page_id'])){
            $page = new SmartestPage;
            $page->hydrate($raw_data);
            $this->_page = $page;
        }
        
        if(isset($raw_data['set_id'])){
            $group = new SmartestPageGroup;
            $group->hydrate($raw_data);
            $this->_group = $group;
        }
        
        return parent::hydrate($raw_data);
        
    }
    
    public function getPage(){
        
        if(!$this->_page){
            $page = new SmartestPage;
            if($page->find($this->getPageId())){
                $this->_page = $page;
            }
        }
        
        return $this->_page;
    }
    
    public function getPageId(){
        return $this->getEntityForeignKeyValue(1);
    }
    
    public function setPageId($id){
        return $this->setEntityForeignKeyValue(1, (int) $id);
    }
    
    public function getGroup(){
        
        if(!$this->_group){
            $group = new SmartestPageGroup;
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
            $this->setType('SM_MTMLOOKUP_PAGE_GROUP_MEMBERSHIP');
        }
        
        return parent::save();
    }
    
}