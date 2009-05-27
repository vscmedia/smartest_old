<?php

class SmartestAssetGroup extends SmartestSet{

    public function __objectConstruct(){
        $this->_membership_type = 'SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP';
    }
    
    public function getMembers($refresh=false){
        
        if($refresh || !count($this->_members)){
        
            $q = new SmartestManyToManyQuery($this->_membership_type);
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
    	    $q->addForeignTableConstraint('Assets.asset_deleted', 0);
    	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	    
            $this->_members = $q->retrieve(true);
        
        }
        
        return $result;
        
    }
    
    public function getApprovedMembers($refresh=false){
        
        if($refresh || !count($this->_members)){
        
            $q = new SmartestManyToManyQuery($this->_membership_type);
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
    	    $q->addForeignTableConstraint('Assets.asset_deleted', 0);
    	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	        $q->addForeignTableConstraint('Asset.asset_is_approved', 'TRUE');
	    
            $this->_members = $q->retrieve(true);
        
        }
        
        return $result;
        
    }

}