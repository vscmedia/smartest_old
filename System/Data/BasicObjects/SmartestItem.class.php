<?php

class SmartestItem extends SmartestBaseItem{
	
	protected $_model;
	protected $_model_properties = array();
	protected $_meta_page_id;
	protected $_meta_page;
	
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
	
	public function getModelId(){
	    return $this->_properties['itemclass_id'];
	}
	
	public function __toArray($include_foreign_object_data=false){
	    
	    $array = $this->_properties;
	    
	    if($include_foreign_object_data){
	        $array['model'] = $this->getModel()->__toArray();
	        $array['link_contents'] = $this->getCmsLinkContents();
        }
        
	    return $array;
	}
	
	public function offsetGet($offset){
	    
	    $offset = strtolower($offset);
	    
	    switch($offset){
	        
	        case "title":
	        return $this->getName();
	        break;
	        
	        case "url":
	        return $this->getUrl();
	        break;
	        
	        case "link_contents":
	        
	        if($this->getMetapageId()){
    	        
    	        return 'metapage:id='.$this->getMetapageId().':id='.$this->getId();
                
    	    }
            
	        break;
	        
	        case "model":
	        return $this->getModel();
	        break;
	        
	        case "tags":
	        return $this->getTags();
	        break;
	        
	        case "authors":
	        return $this->getAuthors();
	        break;
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
	public function offsetExists($offset){
	    
	    return parent::offsetExists($offset) || in_array($offset, array('title', 'url', 'link_contents', 'model', 'tags', 'authors'));
	    
	}
	
	public function getParentItemForMetaPage($metapage_id){
	    return SmartestSystemSettingHelper::load('item_parent_metapage_'.$metapage_id.'_'.$this->_properties['id'].'_site_'.$this->getCurrentSiteId());
	}
	
	public function setParentItemForMetaPage($metapage_id, $parent_item_id){
	    
	}
	
	public function delete($remove=false){
	    if($remove){
		    $sql = "DELETE FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='".$this->_properties['id']."' LIMIT 1";
		    $this->database->rawQuery($sql);
		    $sql = "DELETE FROM ItemPropertyValues WHERE itempropertyvalue_item_id='".$this->_properties['id']."'";
		    $this->database->rawQuery($sql);
		    $this->_came_from_database = false;
	    }else{
	        $this->setField('Deleted', 1);
	        $this->save();
	    }
	}
	
	public function getInfoForPageTree($draft){
	    
	    $item = array();
	    $data = array();
	    
	    $data['asset_id'] = $this->getId();
	    $data['asset_webid'] = $this->getWebid();
	    $data['asset_type'] = $this->getType();
	    $data['assetclass_name'] = SmartestStringHelper::toVarName($this->getName());
	    $data['assetclass_id'] = 'item_'.$this->getId();
	    $data['defined'] = 'PUBLISHED';
	    $data['exists'] = 'true';
	    $data['type'] = 'item';
	    
	    $item['info'] = $data;
	    
	    $item['children'] = $this->getUsedAssetsForPageTree($draft);
	    
	    return $item;
	    
	}
	
	public function getUsedAssetsForPageTree($draft=false){
	    
	    $assets = $this->getUsedAssets($draft);
	    $arrays = array();
	    
	    foreach($assets as $a){
	        $arrays[] = $a->getArrayForElementsTree(1);
	    }
	    
	    // print_r($arrays);
	    return $arrays;
	    
	}
	
	public function getUsedAssets($draft=false){
	    
	    $field = $draft ? 'itempropertyvalue_draft_content' : 'itempropertyvalue_content';
	    
	    $sql = "SELECT Assets.* FROM ItemPropertyValues, ItemProperties, Items, Assets WHERE Items.item_id='".$this->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND ItemPropertyValues.itempropertyvalue_property_id=ItemProperties.itemproperty_id AND ItemPropertyValues.".$field."=Assets.asset_id AND ItemProperties.itemproperty_datatype IN ('SM_DATATYPE_ASSET')";
	    $result = $this->database->queryToArray($sql);
	    $assets = array();
	    
	    foreach($result as $record){
	        
	        $a = new SmartestAsset;
	        $a->hydrate($record);
	        $assets[] = $a;
	        
	    }
	    
	    return $assets;
	    
	}
	
	// Tags
	
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
	    
	    // print_r($tags);
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
	
	// Related items and pages
	
	public function getRelatedItems($draft_mode=false){
	    
	    $ids_array = $this->getRelatedItemIds($draft_mode);
	    
	    $ds = new SmartestQueryResultSet($this->getModel()->getId(), $this->getModel()->getClassName(), $draft_mode);
    
        foreach($ids_array as $item_id){
	        $ds->insertItemId($item_id);
	    }
    
        return $ds->getItems();
	    
	}
	
	public function getRelatedSimpleItems($draft_mode=false){
	    
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
	    
	    $items = $this->getRelatedSimpleItems($draft_mode);
	    $arrays = array();
	    
	    foreach($items as $i){
	        $arrays[] = $i->__toArray(true);
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedItemIds($draft_mode=false){
	    
	    $items = $this->getRelatedSimpleItems($draft_mode);
	    $ids = array();
	    
	    foreach($items as $i){
	        $ids[] = $i->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function getRelatedForeignSimpleItems($draft_mode=false, $model_id=''){
	    
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
	
	public function getRelatedForeignItems($draft_mode=false, $model_id=''){
	    
	    $ids_array = $this->getRelatedForeignItemIds($model_id);
	    
	    $model = new SmartestModel;
	    
	    if($model->hydrate($model_id)){
	    
	        $ds = new SmartestQueryResultSet($model->getId(), $model->getClassName(), $draft_mode);
	    
	        foreach($ids_array as $item_id){
		        $ds->insertItemId($item_id);
		    }
	    
	        return $ds->getItems();
	    
        }else{
            return array();
        }
	    
	}
	
	public function getRelatedForeignItemsAsArrays($draft_mode=false, $model_id=''){
	    
	    $items = $this->getRelatedForeignSimpleItems($draft_mode, $model_id);
	    $arrays = array();
	    
	    foreach($items as $i){
	        $arrays[] = $i->__toArray(true);
	    }
	    
	    return $arrays;
	    
	}
	
	public function getRelatedForeignItemIds($draft_mode=false, $model_id=''){
	    
	    $items = $this->getRelatedForeignSimpleItems($draft_mode, $model_id);
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
	
	//// Authors and page credit
	
	public function getAuthors(){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_ITEM_AUTHORS');
	    $q->setTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $this->_properties['id']);
	    
	    $q->addSortField('Users.user_lastname');
	    
	    $result = $q->retrieve();
	    
	    return $result;
	    
	}
	
	public function getAuthorsAsArrays(){
	    
	    $authors = $this->getAuthors();
	    $arrays = array();
	    
	    foreach($authors as $a){
	        $arrays[] = $a->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getAuthorIds(){
	    
	    $authors = $this->getAuthors();
	    $ids = array();
	    
	    foreach($authors as $a){
	        $ids[] = $a->getId();
	    }
	    
	    return $ids;
	    
	}
	
	public function addAuthorById($user_id){
	    
	    $user_id = (int) $user_id;
	    
	    $link = new SmartestManyToManyLookup;
	    $link->setEntityForeignKeyValue(2, $this->_properties['id']);
	    $link->setEntityForeignKeyValue(1, $user_id);
	    $link->setType('SM_MTMLOOKUP_ITEM_AUTHORS');
	    
	    $link->save();
	    
	}
	
	public function removeAuthorById($user_id){
	    
	    $user_id = (int) $user_id;
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_ITEM_AUTHORS');
	    $q->setTargetEntityByIndex(1);
	    $q->addQualifyingEntityByIndex(2, $this->_properties['id']);
	    $q->addForeignTableConstraint('Users.user_id', $user_id);
	    
	    $q->delete();
	    
	    // print_r($q);
	    
	}
	
	// CMS Display stuff
	
	public function getDescriptionField(){
	    
	    // default_description_property_id
	    if($this->getModel()->getDefaultDescriptionPropertyId()){
	        $property_id = $this->getModel()->getDefaultDescriptionPropertyId();
	        $property = $this->getPropertyByNumericKey($property_id);
	        return $property;
	    }else{
	        return null;
	    }
	    
	}
	
	public function getDescriptionFieldContents(){
	    
	    $property = $this->getDescriptionField();
	    
	    if(is_object($property)){
	        
	        $type_info = $property->getTypeInfo();
	        
	        if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
	            $asset = new SmartestAsset;
	            
	            if($asset->hydrate($this->getPropertyValueByNumericKey($property->getId()))){
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
	        throw new SmartestException(sprintf("Specified model description property with ID '%s' is not an object.", $property_id));
	    }
	    
	}
	
	public function getPropertyByNumericKey($key){
	    
	    /* $this->getProperties(); */
	    
	    if(!$this->_model_properties[$key]){
	    
	        $sql = "SELECT * FROM ItemProperties WHERE itemproperty_id='".$key."'";
	        $result = $this->database->queryToArray($sql);
	        
	        if(count($result)){
	            $property = new SmartestItemPropertyValueHolder;
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
	    
	    if(array_key_exists($key, $this->_model_properties)){
	        
	        $property_value_object = $this->_model_properties[$key]->getData();
	        // print_r($property_value_object);
	        
	        if(is_object($property_value_object)){
	            if($draft){
    	            return $property_value_object->getDraftContent();
                }else{
                    return $property_value_object->getContent();
                }
	        }else{
	            throw new SmartestException("Accessing property ID '".$key."' failed because SmartestItemPropertyValue object was not loaded.");
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
	        $page_id = $this->getMetapageId();
	        return 'metapage:id='.$page_id.':id='.$this->_properties['id'];
	    }else{
	        return null;
	    }
	    
	}
    
    public function getMetapageId(){
        
        if(!$this->_meta_page_id){
        
            if($this->_properties['metapage_id']){
	            $this->_meta_page_id = $this->_properties['metapage_id'];
            }else if($this->getModel()->getDefaultMetapageId($this->getCurrentSiteId())){
                $this->_meta_page_id = $this->getModel()->getDefaultMetapageId($this->getCurrentSiteId());
            }else{
                $this->_meta_page_id = null;
            }
        
        }
        
        return $this->_meta_page_id;
        
	}
	
	public function getMetapage(){
	    
	    if(!$this->_meta_page){
	    
	        if($this->getMetaPageId()){
	            
	            $page = new SmartestPage;
	            
	            if($page->hydrate($this->getMetaPageId())){
	                $this->_meta_page = $page;
	            }
	            
            }
        
        }
        
        return $this->_meta_page;
	    
	}
	
	public function getItemSpaceDefinitions($draft=false){
	    
	    $defs = array();
	    
	    $sql = "SELECT * FROM AssetIdentifiers, AssetClasses, Pages WHERE AssetIdentifiers.assetidentifier_assetclass_id=AssetClasses.assetclass_id AND AssetIdentifiers.assetidentifier_page_id=Pages.page_id AND Pages.page_deleted != 'TRUE'";
	    
	    if($draft){
	        $match_field = "AssetIdentifiers.assetidentifier_draft_asset_id";
	    }else{
	        $match_field = "AssetIdentifiers.assetidentifier_live_asset_id";
	    }
	    
	    $sql .= " AND ".$match_field."='".$this->getId()."'";
	    
	    // echo $sql;
	    
	    $result = $this->database->queryToArray($sql);
	    
	    foreach($result as $record){
	        $definition = new SmartestItemSpaceDefinition;
	        $definition->hydrateFromGiantArray($record);
	        $defs[] = $definition;
	    }
	    
	    return $defs;
	    
	}
	
}
