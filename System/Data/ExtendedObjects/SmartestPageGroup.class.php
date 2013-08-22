<?php

class SmartestPageGroup extends SmartestSet{

    public function __objectConstruct(){
        
        $this->_membership_type = 'SM_MTMLOOKUP_PAGE_GROUP_MEMBERSHIP';
        
    }
    
    public function getMembers($draft_mode=false, $refresh=false){
        
        if(!count($this->_members)){
        
            $memberships = $this->getMemberships($draft_mode, $refresh);
	        
	        $pages = array();
	        
	        foreach($memberships as $m){
	            $pages[] = $m->getPage();
	        }
	        
	        $this->_members = $pages;
        
        }
        
        return $this->_members;
        
    }
    
    public function getMemberships($draft_mode=false, $refresh=false){
        
        $q = new SmartestManyToManyQuery($this->_membership_type);
        $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addForeignTableConstraint('Pages.page_type', 'NORMAL');
	    $q->addForeignTableConstraint('Pages.page_deleted', 'FALSE');
	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
    
	    if(!$draft_mode){
	        $q->addForeignTableConstraint('Pages.page_is_published', 'TRUE');
	    }
        
        $memberships = $q->retrieve(false, null, $refresh);
        
        return $memberships;
        
    }
    
    public function getMemberIds($draft_mode=false){
        
        $ids = array();
        
        foreach($this->getMembers($draft_mode) as $p){
            $ids[] = $p->getId();
        }
        
        return $ids;
        
    }
    
    public function fixOrderIndices(){
        // if($this->getIsGallery()){
            
            $i = 0;
            
            foreach($this->getMemberships(true, true) as $k => $m){
                // echo $k.' ';
                $m->setOrderIndex($i);
                $m->save();
                $i++;
            }
            // print_r($this->database->getDebugInfo());
        // }
    }
    
    public function getNonMembers($draft_mode=false){
        
        $s = new SmartestSite;
        
        if($s->find($this->getSiteId())){
        
            $member_ids = $this->getMemberIds($draft_mode);
        
            $all_pages = $s->getNormalPagesList($draft_mode);
        
            foreach($all_pages as $key=>$value){
                if(in_array($value['info']['id'], $member_ids)){
                    unset($all_pages[$key]);
                }
            }
            
            // print_r($all_pages);
            return array_values($all_pages);
        
        }
        
    }
    
    public function addPageById($id, $strict_checking=true){
        
        if(!$strict_checking || !in_array($id, $this->getMemberIds())){
            
            $m = new SmartestPageGroupMembership;
            $m->setPageId($id);
            $m->setGroupId($this->getId());
            $m->setOrderIndex($this->getNextMemberOrderIndex());
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
        
        $sql = "SELECT ManyToManyLookups.mtmlookup_order_index FROM Pages, Sets, ManyToManyLookups WHERE ManyToManyLookups.mtmlookup_type='SM_MTMLOOKUP_PAGE_GROUP_MEMBERSHIP' AND ManyToManyLookups.mtmlookup_entity_1_foreignkey=Pages.page_id AND (ManyToManyLookups.mtmlookup_entity_2_foreignkey='".$this->getId()."' AND ManyToManyLookups.mtmlookup_entity_2_foreignkey=Sets.set_id) AND Pages.page_deleted ='FALSE' ORDER BY ManyToManyLookups.mtmlookup_order_index DESC";
        $result = $this->database->queryToArray($sql, true);
        
        if(count($result)){
            $current_highest = (int) $result[0]['mtmlookup_order_index'];
            return $current_highest+1;
        }else{
            return 0;
        }
        
    }
    
    public function setNewOrderFromString($string){
        
        $ids = explode(',', $string);
        
        $memberships = $this->getMemberships(true, true);
        
        foreach($ids as $key => $value){
            if(isset($memberships[$value])){
                echo "Page with ID ".$value.' gets order '.$key;
                $memberships[$value]->setOrderIndex($key);
                $memberships[$value]->save();
            }
        }
        
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_SET_PAGEGROUP_PERMANENT');
        }
        
        return parent::save();
        
    }

}