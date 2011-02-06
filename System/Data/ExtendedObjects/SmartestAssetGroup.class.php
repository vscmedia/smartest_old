<?php

class SmartestAssetGroup extends SmartestSet implements SmartestSetApi, SmartestStorableValue, SmartestSubmittableValue{
    
    public function __objectConstruct(){
        $this->_membership_type = 'SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP';
    }
    
    public function delete(){
        
        // delete memberships
        $q = new SmartestManyToManyQuery($this->_membership_type);
        $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
	    $q->delete();
	    
	    parent::delete();
        
    }
    
    public function getMembers($mode=1, $site_id='', $refresh=false){
        
        if($refresh || !count($this->_members)){
        
            $memberships = $this->getMemberShips($mode, $site_id, $refresh);
	        
	        $assets = array();
	        
	        foreach($memberships as $m){
	            $assets[] = $m->getAsset();
	        }
	        
	        $this->_members = $assets;
        
        }
        
        return $this->_members;
        
    }
    
    public function getMemberships($mode=1, $site_id='', $refresh=false, $approved_only=false){
        
        $q = new SmartestManyToManyQuery($this->_membership_type);
        $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
	    $q->addForeignTableConstraint('Assets.asset_deleted', 0);
	    
	    if($mode == 1){
	        $q->addForeignTableConstraint('Assets.asset_is_archived', '0');
	    }else if($mode == 2){
	        $q->addForeignTableConstraint('Assets.asset_is_archived', '1');
	    }
	    
	    if(is_numeric($site_id)){
	        $q->addForeignTableOrConstraints(
	            array('field'=>'Assets.asset_site_id', 'value'=>$site_id),
	            array('field'=>'Assets.asset_shared', 'value'=>'1')
	        );
	    }
	    
	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	    
	    if($approved_only){
	        $q->addForeignTableConstraint('Asset.asset_is_approved', 'TRUE');
	    }
	    
	    $q->addSortField('Assets.asset_label');
    
        $result = $q->retrieve(true);
        
        return $result;
        
    }
    
    public function getMemberIds($mode=1, $site_id='', $refresh=false){
        
        if($refresh || !count($this->_member_ids)){
        
            $ids = array();
        
            foreach($this->getMemberShips($mode) as $m){
                $ids[] = $m->getAssetId();
            }
            
            $this->_member_ids = $ids;
        
        }
        
        return $this->_member_ids;
        
    }
    
    public function getApprovedMembers($refresh=false, $mode=1){
        
        if($refresh || !count($this->_members)){
        
            $memberships = $this->getMemberShips($refresh, $mode, true);
	        
	        $assets = array();
	        
	        foreach($memberships as $m){
	            $assets[] = $m->getAsset();
	        }
	        
            $this->_members = $assets;
        
        }
        
        return $this->_members;
        
    }
    
    public function getApprovedMemberIds($refresh=false, $mode=1){
        
        $ids = array();
        
        foreach($this->getMemberShips($refresh, $mode, true) as $m){
            $ids[] = $m->getAssetId();
        }
        
        return $ids;
        
    }
    
    public function getOptions($site_id=''){
        
        $member_ids = $this->getMemberIds();
        $alh = new SmartestAssetsLibraryHelper;
        
        // only gets non-archived assets
        if($this->getFilterType() == 'SM_SET_FILTERTYPE_NONE'){
            $options = $alh->getAssets($site_id, 1, $member_ids, true); 
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETCLASS'){
            $options = $alh->getAssetClassOptions($this->getFiltervalue(), $site_id, 1, $member_ids);
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETTYPE'){
            $options = $alh->getAssetsByTypeCode($this->getFiltervalue(), $site_id, 1, $member_ids);
        }
        
        return $options;
        
    }
    
    public function getTypes(){
        
        $du = new SmartestAssetsLibraryHelper;
        
        if($this->getFilterType() == 'SM_SET_FILTERTYPE_NONE'){
            return $du->getTypes();
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETCLASS'){
            $du = new SmartestAssetsLibraryHelper;
            return $du->getTypesByPlaceholderType($this->getFilterValue());
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETTYPE'){
            // print_r($this->getFilterValue());
            return $du->getSelectedTypes(array($this->getFilterValue()));
        }
        
    }
    
    public function getFileTypeLabel(){
        if($this->getFilterType() == 'SM_SET_FILTERTYPE_NONE'){
            return 'All file types';
        }else{
            
            $types = $this->getTypes();
            $labels = array();
            
            foreach($types as $t){
                $labels[] = $t['label'];
            }
            
            $l = SmartestStringHelper::toCommaSeparatedList($labels).' files';
            
            if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETTYPE'){
                return $l.' only';
            }else{
                return $l;
            }
        }
    }
    
    public function addAssetById($id, $strict_checking=true){
        
        if(!$strict_checking || !in_array($id, $this->getMemberIds())){
            
            $m = new SmartestAssetGroupMembership;
            $m->setAssetId($id);
            $m->setGroupId($this->getId());
            $m->save();
            
        }
        
    }
    
    public function removeAssetById($id){
        
        $id = (int) $id;
        
        if(in_array($id, $this->getMemberIds(0, null))){
            
            $sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP' AND mtmlookup_entity_1_foreignkey='".$id."' AND mtmlookup_entity_2_foreignkey='".$this->getId()."' LIMIT 1";
            $this->database->rawQuery($sql);
            
        }
        
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_SET_ASSETGROUP');
        }
        
        return parent::save();
    }
    
    public function isUsableForPlaceholders(){
        return ($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETTYPE');
    }
    
    public function getPlaceholdersWhereUsed(){
        
        $sql = "SELECT * FROM AssetClasses WHERE AssetClasses.assetclass_type='SM_ASSETCLASS_PLACEHOLDER' AND AssetClasses.assetclass_filter_type='SM_ASSETCLASS_FILTERTYPE_ASSETGROUP' AND AssetClasses.assetclass_filter_value='".$this->getId()."'";
        $result = $this->database->queryToArray($sql);
        
        $placeholders = array();
        
        foreach($result as $r){
            $placeholder = new SmartestPlaceholder;
            $placeholder->hydrate($r);
            $placeholders[] = $placeholder;
        }
        
        return $placeholders;
        
    }
    
    public function isUsedForPlaceholders(){
        return (bool) count($this->getPlaceholdersWhereUsed());
    }
    
    public function allowNonShared(){
        
        $sql = "SELECT ItemClasses.*, ItemProperties.*, Sets.set_id FROM Sets, ItemProperties, ItemClasses WHERE ItemClasses.itemclass_shared='1' AND ItemProperties.itemproperty_itemclass_id=ItemClasses.itemclass_id AND Sets.set_type='SM_SET_ASSETGROUP' AND ItemProperties.itemproperty_option_set_type='SM_PROPERTY_FILTERTYPE_ASSETGROUP' AND ItemProperties.itemproperty_option_set_id=Sets.set_id AND Sets.set_id='".$this->getId()."'";
        echo $sql;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "types":
            return $this->getTypes();
            case "type_labels_list":
            return $this->getFileTypeLabel();
        }
        
        return parent::offsetGet($offset);
        
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_properties['id'];
    }
    
    public function hydrateFromStorableFormat($v){
        if(is_numeric($v)){
            return $this->find($v);
        }
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        if(is_numeric($v)){
            return $this->find($v);
        }
    }

}