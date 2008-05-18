<?php

class SmartestItem extends SmartestDataObject{
	
	protected $_model;
	protected $_model_properties = array();
	
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('ModelId', 'itemclass_id');
		$this->_table_prefix = 'item_';
		$this->_table_name = 'Items';
		
	}
	
	public function getIsPublic(){
		return ($this->getPublic() == 'TRUE') ? true : false;
	}
	
	public function getModel(){
	    
	    if(!is_object($this_model)){
	        $m = new SmartestModel;
	        $m->hydrate($this->_properties['itemclass_id']);
	        $this->_model = $m;
	    }
	    
	    return $this->_model;
	    
	}
	
	public function __toArray($include_foreign_object_data=false){
	    
	    $array = $this->_properties;
	    
	    if($include_foreign_object_data){
	        $array['model'] = $this->getModel()->__toArray();
	        $array['link_contents'] = $this->getCmsLinkContents();
        }
        
	    return $array;
	}
	
	public function delete($remove=false){
	    if($remove){
		    $sql = "DELETE FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='".$this->_properties['id']."' LIMIT 1";
		    $this->database->rawQuery($sql);
		    $this->_came_from_database = false;
	    }else{
	        $this->setField('Deleted', 1);
	        $this->save();
	    }
	}
	
	public function clearTags(){
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_type='SM_ITEM_TAG_LINK'";
	    $this->database->rawQuery($sql);
	}
	
	public function getTagIdsArray(){
	    
	    $sql = "SELECT taglookup_tag_id FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_type='SM_ITEM_TAG_LINK'";
	    $result = $this->database->queryToArray($sql);
	    $ids = array();
	    
	    foreach($result as $tl){
	        if(!in_array($tl['taglookup_object_id'], $ids)){
	            $ids[] = $tl['taglookup_tag_id'];
	        }
	    }
	    
	    return $ids;
	    
	}
	
	public function getTags(){
	    
	    $sql = "SELECT * FROM Tags, TagsObjectsLookup WHERE TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id='".$this->_properties['id']."' AND TagsObjectsLookup.taglookup_type='SM_ITEM_TAG_LINK' ORDER BY Tags.tag_name";
	    $result = $this->database->queryToArray($sql);
	    $ids = array();
	    $tags = array();
	    
	    foreach($result as $ta){
	        if(!in_array($ta['taglookup_tag_id'], $ids)){
	            $ids[] = $ta['taglookup_tag_id'];
	            $tag = new SmartestTag;
	            $tag->hydrate($ta);
	            $tags[] = $tag;
	        }
	    }
	    
	    return $tags;
	    
	}
	
	public function getTagsAsArrays(){
	    
	    $arrays = array();
	    $tags = $this->getTags();
	    
	    foreach($tags as $t){
	        $arrays[] = $t->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedItems($draft_mode=false){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS');
	    $q->setCentralNodeId($this->_properties['id']);
	    $q->addSortField('Items.item_created');
	    
	    if(!$draft_mode){
	        $q->addForeignTableConstraint('Items.item_public', 'TRUE');
	    }
	    
	    $related_items = $q->retrieve();
	    
	    return $related_items;
	    
	}
	
	public function getRelatedItemsAsArrays($draft_mode=false){
	    
	    $items = $this->getRelatedItems($draft_mode);
	    $arrays = array();
	    
	    foreach($items as $i){
	        $arrays[] = $i->__toArray(true);
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedItemIds($draft_mode=false){
	    
	    $items = $this->getRelatedItems($draft_mode);
	    $ids = array();
	    
	    foreach($items as $i){
	        $ids[] = $i->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function getRelatedForeignItems($draft_mode=false, $model_id=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS_OTHER');
	    $q->setCentralNodeId($this->_properties['id']);
	    $q->addSortField('Items.item_created');
	    
	    if(is_numeric($model_id)){
	        $q->addForeignTableConstraint('Items.item_itemclass_id', $model_id);
	    }
	    
	    if(!$draft_mode){
	        $q->addForeignTableConstraint('Items.item_public', 'TRUE');
	    }
	    
	    $related_items = $q->retrieve();
	    
	    return $related_items;
	    
	}
	
	public function getRelatedForeignItemsAsArrays($draft_mode=false, $model_id=''){
	    
	    $items = $this->getRelatedForeignItems($draft_mode, $model_id);
	    $arrays = array();
	    
	    foreach($items as $i){
	        $arrays[] = $i->__toArray(true);
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedForeignItemIds($draft_mode=false, $model_id=''){
	    
	    $items = $this->getRelatedForeignItems($draft_mode, $model_id);
	    $ids = array();
	    
	    foreach($items as $i){
	        $ids[] = $i->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function addRelatedItem($item_id){
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS');
	    $q->createNetworkLinkBetween($this->_properties['id'], $item_id);
	}
	
	public function removeRelatedItem($item_id){
	    $item_id = (int) $item_id;
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS');
	    $q->deleteNetworkLinkBetween($this->_properties['id'], $item_id);
	}
	
	public function addRelatedForeignItem($item_id){
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS_OTHER');
	    $q->createNetworkLinkBetween($this->_properties['id'], $item_id);
	}
	
	public function removeRelatedForeignItem($item_id){
	    $item_id = (int) $item_id;
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS_OTHER');
	    $q->deleteNetworkLinkBetween($this->_properties['id'], $item_id);
	}
	
	public function removeAllRelatedItems($model_id){
	    
	    if($this->_properties['itemclass_id'] == $model_id){
	        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS');
	    }else{
	        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RELATED_ITEMS_OTHER');
	    }
	    
	    $q->deleteNetworkNodeById($this->_properties['id']);
	    
	}
	
	public function getRelatedPages($draft_mode=false){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGES_ITEMS');
	    
	    $q->setTargetEntityByIndex(2);
	    $q->addQualifyingEntityByIndex(1, $this->_properties['id']);
	    
	    if(!$draft_mode){
	        $q->addForeignTableConstraint('Pages.page_is_published', 'TRUE');
	    }
	    
	    $q->addForeignTableConstraint('Pages.page_type', 'NORMAL');
	    $q->addForeignTableConstraint('Pages.page_deleted', 'FALSE');
	    
	    $q->addSortField('Pages.page_created');
	    
	    $result = $q->retrieve();
	    
	    return $result;
	    
	}
	
	public function getRelatedPagesAsArrays($draft_mode=false){
	    
	    $pages = $this->getRelatedPages($draft_mode);
	    $arrays = array();
	    
	    foreach($pages as $p){
	        $arrays[] = $p->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedPageIds($draft_mode=false){
	    
	    $pages = $this->getRelatedPages($draft_mode);
	    $ids = array();
	    
	    foreach($pages as $p){
	        $ids[] = $p->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function addRelatedPage($page_id){
	    
	    $page_id = (int) $page_id;
	    
	    $link = new SmartestManyToManyLookup;
	    $link->setEntityForeignKeyValue(1, $this->_properties['id']);
	    $link->setEntityForeignKeyValue(2, $page_id);
	    $link->setType('SM_MTMLOOKUP_PAGES_ITEMS');
	    
	    $link->save();
	}
	
	public function removeRelatedPage($page_id){
	    
	    $page_id = (int) $page_id;
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGES_ITEMS');
	    $q->setTargetEntityByIndex(2);
	    $q->addQualifyingEntityByIndex(1, $this->_properties['id']);
	    $q->addForeignTableConstraint('Pages.page_id', $page_id);
	    
	    $q->delete();
	    
	}
	
	public function removeAllRelatedPages(){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGES_ITEMS');
	    $q->setTargetEntityByIndex(2);
	    $q->addQualifyingEntityByIndex(1, $this->_properties['id']);
	    
	    $q->delete();
	    
	}
	
	public function getDescriptionFieldContents(){
	    
	    // default_description_property_id
	    if($this->getModel()->getDefaultDescriptionPropertyId()){
	        $property_id = $this->getModel()->getDefaultDescriptionPropertyId();
	    }else{
	        return null;
	    }
	    
	    // echo $property_id;
	    
	    $property = $this->getPropertyByNumericKey($property_id);
	    
	    // print_r($property);
	    
	    if(is_object($property)){
	        
	        $type_info = $property->getTypeInfo();
	        
	        if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
	            $asset = new SmartestAsset;
	            // echo $property_id;
	            // print_r($this->getPropertyValueByNumericKey($property_id));
	            if($asset->hydrate($this->getPropertyValueByNumericKey($property_id))){
	                // get asset content
	                return $asset->getContent();
	            }else{
	                // throw new SmartestException(sprintf("Asset with ID %s was not found.", $this->getPropertyValueByNumericKey($property_id)));
	                return null;
	            }
	        }else{
	            return $this->getPropertyValueByNumericKey();
	        }
	        
	    }else{
	        throw new SmartestException(sprintf("Property with ID %s is not an object.", $property_id));
	    }
	    
	}
	
	public function getPropertyByNumericKey($key){
	    
	    /* $this->getProperties(); */
	    
	    
	    
	    if(!$this->_model_properties[$key]){
	    
	        $sql = "SELECT * FROM ItemProperties WHERE itemproperty_id='".$key."'";
	        $result = $this->database->queryToArray($sql);
	        
	        if(count($result)){
	            $property = new SmartestItemProperty;
	            $property->hydrate($result[0]);
	            $property->setContextualItemId($this->_properties['id']);
	            $this->_model_properties[$key] = $property;
	        }
	    
        }
	    
	    if(array_key_exists($key, $this->_model_properties)){
	        return $this->_model_properties[$key];
	    }else{
	        return null;
	    }
	}
	
	public function getPropertyValueByNumericKey($key, $draft=false){
	    
	    // $this->getProperties();
	    
	    // print_r($this->_model_properties);
	    // echo $key;
	    
	    // $this->
	    
	    // print_r($this->_model_properties[$key]->getData());
	    
	    // print_r(array_keys($this->_model_properties));
	    
	    // echo $key;
	    
	    if(array_key_exists($key, $this->_model_properties)){
	        
	        // echo 'has property. ';
	        
	        if($draft){
	            return $this->_model_properties[$key]->getData()->getDraftContent();
            }else{
                return $this->_model_properties[$key]->getData()->getContent();
            }
	    }else{
	        return null;
	    }
	}
	
	public function getUrl(){
	    
	    if($lc = $this->getCmsLinkContents()){
	        
	        $lh = new SmartestCmsLinkHelper;
    	    $lh->parse($lc);
            return $lh->getUrl();
	    }else{
	        return null;
	    }
	    
	}
	
	public function getCmsLinkContents(){
	    
	    if($this->getMetapageId()){
	        $page_id = $this->getMetapageId($this->getCurrentSiteId());
	        return 'metapage:id='.$page_id.':id='.$this->_properties['id'];
	    }else if($this->getModel()->getDefaultMetapageId($this->getCurrentSiteId())){
	        $page_id = $this->getModel()->getDefaultMetapageId($this->getCurrentSiteId());
	        return 'metapage:id='.$page_id.':id='.$this->_properties['id'];
	    }else{
	        return null;
	    }
	    
	}
	
	public function tag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->hydrate($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->hydrateBy('name', $tag_name)){
                // create tag
    	        $tag->setLabel($tag_identifier);
    	        $tag->setName($tag_name);
    	        $tag->save();
    	    }
	    }
	    
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag->getId()."', '".$this->_properties['id']."', 'SM_ITEM_TAG_LINK')";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	public function untag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->hydrate($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->hydrateBy('name', $tag_name)){
                return false;
    	    }
	    }
	    
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag->getId()."' AND taglookup_type='SM_ITEM_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	public function getTextFragments(){
	    
	    $sql = "SELECT TextFragments.* FROM Items, Assets, ItemPropertyValues, TextFragments WHERE Items.item_id=ItemPropertyValues.itempropertyvalue_item_id AND Assets.asset_id=AssetIdentifiers.assetidentifier_live_asset_id AND TextFragments.textfragment_id=Assets.asset_fragment_id AND Pages.page_id='".$this->_properties['id']."'";
		/* $result = $this->database->queryToArray($sql);
		$objects = array();
		
		foreach($result as $tfarray){
		    $tf = new SmartestTextFragment;
		    $tf->hydrate($tfarray);
		    $objects[] = $tf;
		}
		
		return $objects; */
	}
	
	public function getParsableTextFragments(){
	    
	    $helper = new SmartestAssetsLibraryHelper;
		$codes = $helper->getParsableAssetTypeCodes();
		
		$sql = "SELECT TextFragments.* FROM Pages, Assets, AssetIdentifiers, TextFragments WHERE Pages.page_id=AssetIdentifiers.assetidentifier_page_id AND Assets.asset_id=AssetIdentifiers.assetidentifier_live_asset_id AND TextFragments.textfragment_id=Assets.asset_fragment_id AND Pages.page_id='".$this->_properties['id']."' AND Assets.asset_type IN ('".implode("', '", $codes)."')";
		/* $result = $this->database->queryToArray($sql);
		$objects = array();
		
		foreach($result as $tfarray){
		    $tf = new SmartestTextFragment;
		    $tf->hydrate($tfarray);
		    $objects[] = $tf;
		}
		
		return $objects; */
		
	}
	
}