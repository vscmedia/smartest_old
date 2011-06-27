<?php

class SmartestTemplateGroup extends SmartestSet implements SmartestSetApi{
    
    protected $_helper;
    
    public function __objectConstruct(){
        $this->_membership_type = 'SM_MTMLOOKUP_TEMPLATE_GROUP_MEMBERSHIP';
    }
    
    public function delete(){
        
        if($this->_properties['is_system']){
            return false;
        }else{
        
            // delete memberships
            $q = new SmartestManyToManyQuery($this->_membership_type);
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
            $q->delete();
            parent::delete();
	    
        }
        
    }
    
    protected function getHelper(){
        
        if(!$this->_helper){
            $this->_helper = new SmartestTemplatesLibraryHelper;
        }
        
        return $this->_helper;
    }
    
    public function getMembers($site_id='', $refresh=false){
        
        if($refresh || !count($this->_members)){
        
            $memberships = $this->getMemberships($site_id, $refresh);
	        
	        $assets = array();
	        
	        foreach($memberships as $m){
	            $assets[] = $m->getTemplate();
	        }
	        
	        $this->_members = $assets;
        
        }
        
        return $this->_members;
        
    }
    
    public function getMemberships($site_id='', $refresh=false){
        
        $q = new SmartestManyToManyQuery($this->_membership_type);
        $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
	    $q->addForeignTableConstraint('Assets.asset_deleted', 0);
	    
	    if(is_numeric($site_id)){
	        $q->addForeignTableOrConstraints(
	            array('field'=>'Assets.asset_site_id', 'value'=>$site_id),
	            array('field'=>'Assets.asset_shared', 'value'=>'1')
	        );
	    }
	    
	    if($this->_properties['itemclass_id'] > 0){
	        /* $q->addForeignTableOrConstraints(
	            array('field'=>'Assets.asset_model_id', 'value'=>'0'),
	            array('field'=>'Assets.asset_model_id', 'value'=>$this->_properties['itemclass_id'])
	        ); */
	        // $q->addForeignTableConstraint('Assets.asset_model_id', $this->_properties['itemclass_id']);
	    }
	    
	    // $q->addForeignTableConstraint('Assets.asset_model_id', 0);
	    
	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	    $q->addSortField('Assets.asset_label');
    
        $result = $q->retrieve(true);
        
        return $result;
        
    }
    
    public function getMemberIds($site_id='', $refresh=false){
        
        if($refresh || !count($this->_member_ids)){
        
            $ids = array();
        
            foreach($this->getMemberShips() as $m){
                $ids[] = $m->getTemplateId();
            }
            
            $this->_member_ids = $ids;
        
        }
        
        return $this->_member_ids;
        
    }
    
    public function getOptions($site_id=''){
        
        $member_ids = $this->getMemberIds();
        $alh = new SmartestAssetsLibraryHelper;
        
        // only gets non-archived assets
        /* if($this->getFilterType() == 'SM_SET_FILTERTYPE_NONE'){
            $options = $alh->getAssets($site_id, 1, $member_ids, true); 
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETCLASS'){
            $options = $alh->getAssetClassOptions($this->getFiltervalue(), $site_id, 1, $member_ids);
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETTYPE'){
            $options = $alh->getAssetsByTypeCode($this->getFiltervalue(), $site_id, 1, $member_ids);
        } */
        
        $options = $alh->getAssetsByTypeCode($this->getFiltervalue(), $site_id, 1, $member_ids, $this->getItemclassId());
        return $options;
        
    }
    
    public function addTemplateById($id, $strict_checking=true){
        
        if(!$strict_checking || !in_array($id, $this->getMemberIds())){
            
            $m = new SmartestTemplateGroupMembership;
            $m->setTemplateId($id);
            $m->setGroupId($this->getId());
            $m->save();
            
        }
        
    }
    
    public function removeTemplateById($id){
        
        $id = (int) $id;
        
        if(in_array($id, $this->getMemberIds(0, null))){
            
            $sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='SM_MTMLOOKUP_TEMPLATE_GROUP_MEMBERSHIP' AND mtmlookup_entity_1_foreignkey='".$id."' AND mtmlookup_entity_2_foreignkey='".$this->getId()."' LIMIT 1";
            $this->database->rawQuery($sql);
            
        }
        
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_SET_TEMPLATEGROUP');
        }
        
        return parent::save();
    }
    
}