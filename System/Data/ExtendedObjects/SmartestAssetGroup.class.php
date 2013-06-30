<?php

class SmartestAssetGroup extends SmartestSet implements SmartestSetApi, SmartestStorableValue, SmartestSubmittableValue{
    
    public function __objectConstruct(){
        $this->setMembershipTypeFromInternalAttributes();
    }
    
    public function delete(){
        
        if($this->_properties['is_system']){
            
            return false;
            
        }else{
        
            // delete memberships
            $q = new SmartestManyToManyQuery($this->getMembershipType());
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
            $q->delete();
    
            parent::delete();
	    
        }
        
    }
    
    public function getIsGallery(){
        return $this->getType() == 'SM_SET_ASSETGALLERY';
    }
    
    public function isGallery(){
        return $this->getIsGallery();
    }
    
    public function setIsGallery($bool){
        
        if($bool){
            $this->setType('SM_SET_ASSETGALLERY');
            $this->setMembershipType('SM_MTMLOOKUP_ASSET_GALLERY_MEMBERSHIP');
        }else{
            $this->setType('SM_SET_ASSETGROUP');
            $this->setMembershipType('SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP');
        }
        
    }
    
    public function setMembershipTypeFromInternalAttributes(){
        if($this->getIsGallery()){
            $this->_membership_type = 'SM_MTMLOOKUP_ASSET_GALLERY_MEMBERSHIP';
        }else{
            $this->_membership_type = 'SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP';
        }
    }
    
    public function getMembershipType(){
        $this->setMembershipTypeFromInternalAttributes();
        return $this->_membership_type;
    }
    
    public function setMembershipType($type){
        if(in_array($type, array('SM_MTMLOOKUP_ASSET_GALLERY_MEMBERSHIP','SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP'))){
            $this->_membership_type = $type;
        }else{
            $this->_membership_type = 'SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP';
        }
    }
    
    public function getNextMembershipOrderIndex(){
        if($this->getIsGallery()){
            // $memberships = $this->getMemberships();
            $sql = "SELECT ManyToManyLookups.mtmlookup_order_index FROM Assets, Sets, ManyToManyLookups WHERE ManyToManyLookups.mtmlookup_type='SM_MTMLOOKUP_ASSET_GALLERY_MEMBERSHIP' AND ManyToManyLookups.mtmlookup_entity_1_foreignkey=Assets.asset_id AND (ManyToManyLookups.mtmlookup_entity_2_foreignkey='".$this->getId()."' AND ManyToManyLookups.mtmlookup_entity_2_foreignkey=Sets.set_id) AND Assets.asset_deleted ='0' AND Assets.asset_is_hidden ='0' AND Assets.asset_is_archived ='0' AND (Assets.asset_site_id ='".$this->getSiteId()."' OR Assets.asset_shared='1') ORDER BY ManyToManyLookups.mtmlookup_order_index DESC";
            $result = $this->database->queryToArray($sql, true);
            // echo $sql;
            if(count($result)){
                $current_highest = (int) $result[0]['mtmlookup_order_index'];
                return $current_highest+1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public function fixOrderIndices(){
        if($this->getIsGallery()){
            foreach($this->getMemberships(0, $this->getSiteId(), true) as $k => $m){
                // echo $k.' ';
                $m->setOrderIndex($k);
                $m->save();
            }
            // print_r($this->database->getDebugInfo());
        }
    }
    
    public function getMembers($mode=1, $site_id='', $refresh=false){
        
        if($this->getIsGallery()){
            // return $this->getMemberships();
        }
        
        if($refresh || !count($this->_members)){
        
            $memberships = $this->getMemberships($mode, $site_id, $refresh);
	        
	        $assets = array();
	        
	        foreach($memberships as $m){
	            $assets[] = $m->getAsset();
	        }
	        
	        $this->_members = $assets;
        
        }
        
        return $this->_members;
        
    }
    
    public function getMemberships($mode=1, $site_id='', $refresh=false, $approved_only=false, $numeric_indices=true){
        
        $q = new SmartestManyToManyQuery($this->getMembershipType());
        $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
	    $q->addForeignTableConstraint('Assets.asset_deleted', 0);
	    
	    if(!$this->getIsSystem()){
	        $q->addForeignTableConstraint('Assets.asset_is_hidden', 0);
	    }
	    
	    if($mode == 1){
	        $q->addForeignTableConstraint('Assets.asset_is_archived', '0');
	    }else if($mode == 2){
	        $q->addForeignTableConstraint('Assets.asset_is_archived', '1');
	    }
	    
	    if(!$this->getIsGallery()){
	        if(is_numeric($site_id)){
	            $q->addForeignTableOrConstraints(
	                array('field'=>'Assets.asset_site_id', 'value'=>$site_id),
	                array('field'=>'Assets.asset_shared', 'value'=>'1')
	            );
	        }
	    }
	    
	    if($this->getIsGallery()){
	        $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
        }else{
            $q->addSortField('Assets.asset_label');
        }
	    
	    if($approved_only){
	        $q->addForeignTableConstraint('Asset.asset_is_approved', 'TRUE');
	    }
	    
	    $result = $q->retrieve($numeric_indices, null, $refresh);
        
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
    
    public function getMembershipByAssetId($asset_id){
        
        if($this->getIsGallery()){
        
            $q = new SmartestManyToManyQuery($this->getMembershipType());
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
            $q->addForeignTableConstraint('Assets.asset_id', (int) $asset_id);
    	    // $q->addForeignTableConstraint('Assets.asset_deleted', 0);
	    
    	    /* if(!$this->getIsSystem()){
    	        $q->addForeignTableConstraint('Assets.asset_is_hidden', 0);
    	    } */
	    
    	    /* if($mode == 1){
    	        $q->addForeignTableConstraint('Assets.asset_is_archived', '0');
    	    }else if($mode == 2){
    	        $q->addForeignTableConstraint('Assets.asset_is_archived', '1');
    	    } */
	    
	    
	        $result = $q->retrieve(true);
	        
	        if(count($result)){
	            return $result[0];
	        }else{
	            return null;
	        }
        
            // return $result;
        
        }
        
    }
    
    public function &getIterator(){
        if($this->getIsGallery()){
            return new ArrayIterator($this->getMemberships());
        }else{
            return new ArrayIterator($this->getMembers());
        }
    }
    
    public function setNewOrderFromString($string){
        
        $ids = explode(',', $string);
        
        $memberships = $this->getMemberships(1, $this->getSiteId(), false, false, false);
        
        foreach($ids as $key => $value){
            if(isset($memberships[$value])){
                $memberships[$value]->setOrderIndex($key);
                $memberships[$value]->save();
            }
        }
        
    }
    
    public function getOptions($site_id=''){
        
        $member_ids = $this->getMemberIds();
        $alh = new SmartestAssetsLibraryHelper;
        
        // only gets non-archived assets
        if($this->getFilterType() == 'SM_SET_FILTERTYPE_NONE'){
            if($this->getIsGallery()){
                $options = $alh->getAssetsByTypeCode($alh->getGalleryAssetTypeIds(), $site_id, 1, $member_ids, true);
            }else{
                $options = $alh->getAssets($site_id, 1, $member_ids, true);
            }
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETCLASS'){
            $options = $alh->getAssetClassOptions($this->getFiltervalue(), $site_id, 1, $member_ids);
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETTYPE'){
            $options = $alh->getAssetsByTypeCode($this->getFiltervalue(), $site_id, 1, $member_ids);
        }else if($this->getFilterType() == 'SM_SET_FILTERTYPE_ASSETGROUP'){
            $g = new SmartestAssetGroup;
            if($g->find($this->getFiltervalue())){
                return $g->getMembers();
            }else{
                return array();
            }
        }
        
        return $options;
        
    }
    
    public function getThumbnailOptions(){
        
        if($this->getIsGallery()){
            // if($this->getThumbnailFileGroupId()){
                // If it were possible to attach asset groups to sets via a foreign key
            // }else{
                $alh = new SmartestAssetsLibraryHelper;
                return $alh->getAssetsByTypeCode(array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'), $this->getSiteId(), 1);
            // }
        }else{
            return array();
        }
        
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
    
    public function getTypeCodes(){
        
        $codes = array();
        
        foreach($this->getTypes() as $t){
            $codes[] = $t['id'];
        }
        
        return $codes;
        
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
            
            if($this->getIsGallery()){
                $m = new SmartestAssetGalleryMembership;
                $next_order_index = $this->getNextMembershipOrderIndex();
            }else{
                $m = new SmartestAssetGroupMembership;
                $next_order_index = 0;
            }
            
            $m->setAssetId($id);
            $m->setGroupId($this->getId());
            $m->setOrderIndex($next_order_index);
            $m->save();
            
        }
        
    }
    
    public function removeAssetById($id){
        
        $id = (int) $id;
        
        if(in_array($id, $this->getMemberIds(0, null))){
            
            $sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='".$this->getMembershipType()."' AND mtmlookup_entity_1_foreignkey='".$id."' AND mtmlookup_entity_2_foreignkey='".$this->getId()."' LIMIT 1";
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
            
            case "label":
            return new SmartestString($this->getLabel());
            
            case "is_gallery":
            return $this->getIsGallery();
            
            case "members":
            if($this->getIsGallery()){
                return new SmartestArray($this->getMemberships());
            }else{
                return new SmartestArray($this->getMembers());
            }
            break;
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