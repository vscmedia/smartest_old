<?php

class SmartestTemplateGroupMembership extends SmartestManyToManyLookup{
    
    protected $_template;
    protected $_group;
    
    public function hydrate($raw_data){
        
        if(isset($raw_data['asset_id'])){
            $template = new SmartestTemplateAsset;
            $template->hydrate($raw_data);
            $this->_template = $template;
        }
        
        if(isset($raw_data['set_id'])){
            $group = new SmartestTemplateGroup;
            $group->hydrate($raw_data);
            $this->_group = $group;
        }
        
        return parent::hydrate($raw_data);
        
    }
    
    public function getTemplate(){
        
        if(!$this->_template){
            $template = new SmartestTemplateAsset;
            if($template->find($this->getAssetId())){
                $this->_template = $template;
            }
        }
        
        return $this->_template;
    }
    
    public function getTemplateId(){
        return $this->getEntityForeignKeyValue(1);
    }
    
    public function setTemplateId($id){
        return $this->setEntityForeignKeyValue(1, (int) $id);
    }
    
    public function getGroup(){
        
        if(!$this->_group){
            $group = new SmartestTemplateGroup;
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
            $this->setType('SM_MTMLOOKUP_TEMPLATE_GROUP_MEMBERSHIP');
        }
        
        return parent::save();
    }
    
}