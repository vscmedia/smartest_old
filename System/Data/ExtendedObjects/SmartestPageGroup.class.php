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

}