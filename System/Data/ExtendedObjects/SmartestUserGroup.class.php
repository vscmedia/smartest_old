<?php

class SmartestUserGroup extends SmartestSet{

    public function __objectConstruct(){
        $this->_membership_type = 'SM_MTMLOOKUP_USER_GROUP_MEMBERSHIP';
    }
    
    public function getMembers($sort=''){
        
        if(!$sort){
            $sort = SM_MTM_SORT_GROUP_ORDER;
        }
        
        if($refresh || !count($this->_members)){
        
            $q = new SmartestManyToManyQuery($this->_membership_type);
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
    	    $q->addSortField($sort);
	    
            $this->_members = $q->retrieve(true);
        
        }
        
        return $result;
        
    }
    
}