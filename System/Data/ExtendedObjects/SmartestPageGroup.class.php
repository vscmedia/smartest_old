<?php

class SmartestPageGroup extends SmartestSet{

    public function __objectConstruct(){
        
        $this->_membership_type = 'SM_MTMLOOKUP_PAGE_GROUP_MEMBERSHIP';
        
    }
    
    public function getMembers($draft_mode=false){
        
        if(!count($this->_members)){
        
            $q = new SmartestManyToManyQuery($this->_membership_type);
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
            $q->addForeignTableConstraint('Pages.page_type', 'NORMAL');
    	    $q->addForeignTableConstraint('Pages.page_deleted', 'FALSE');
    	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	    
    	    if($draft_mode){
    	        $q->addForeignTableConstraint('Pages.page_is_published', 'TRUE');
    	    }
	    
            $this->_members = $q->retrieve(true);
        
        }
        
        return $result;
        
    }
    
    public function getMemberIds($draft_mode=false){
        
        $ids = array();
        
        foreach($this->getMembers($draft_mode) as $p){
            $ids[] = $p->getId();
        }
        
        return $ids;
        
    }
    
    public function getNonMembers($draft_mode=false){
        
        $s = new SmartestSite;
        $s->find($this->getSiteId());
        
        $all_pages = $s->getNormalPagesList($draft_mode);
        
        foreach($all_pages as $key=>$value){
            if(in_array($value['info']['id'], $member_ids)){
                unset($all_pages[$key]);
            }
        }
        
        return array_values($all_pages);
        
    }
    
    public function addPageById($id, $strict_checking=true){
        
        if(!$strict_checking || !in_array($id, $this->getMemberIds())){
            
            $m = new SmartestPageGroupMembership;
            $m->setPageId($id);
            $m->setGroupId($this->getId());
            $m->save();
            
        }
        
    }
    
    public function removePageById($id){
        
        $id = (int) $id;
        
        if(in_array($id, $this->getMemberIds(0, null))){
            
            $sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='SM_MTMLOOKUP_PAGE_GROUP_MEMBERSHIP' AND mtmlookup_entity_1_foreignkey='".$id."' AND mtmlookup_entity_2_foreignkey='".$this->getId()."' LIMIT 1";
            $this->database->rawQuery($sql);
            
        }
        
    }
    
    public function getNextMemberOrderIndex(){
        
        $index = 0;
        
        /* $sql = "SELECT DISTINCT dropdownvalue_order FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$this->getId()."' ORDER BY dropdownvalue_order DESC LIMIT 1";
        $result = $this->database->queryToArray($sql); */
        
        if(count($result)){
            $index = $result[0]['dropdownvalue_order']+1;
        }
        
        return $index;
        
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_SET_PAGEGROUP_PERMANENT');
        }
        
        return parent::save();
        
    }

}