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
		return ($this->getPublic() == 'TRUE') ? TRUE : FALSE;
	}
	
	public function getModel(){
	    
	    if(!is_object($this_model)){
	        $m = new SmartestModel;
	        $m->hydrate($this->getModelId());
	        $this->_model = $m;
	    }
	    
	    return $this->_model;
	    
	}
	
	public function __toArray(){
	    $array = $this->_properties;
	    $array['model_name'] = $this->getModel()->getName();
	    return $array;
	}
	
	public function delete($remove=false){
	    if($remove){
		    $sql = "DELETE FROM ".$this->_table_name." WHERE ".$this->_table_prefix."id='".$this->getId()."' LIMIT 1";
		    $this->database->rawQuery($sql);
		    $this->_came_from_database = false;
	    }else{
	        $this->setField('Deleted', 1);
	        $this->save();
	    }
	}
	
	public function clearTags(){
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->getId()."' AND taglookup_type='SM_ITEM_TAG_LINK'";
	    $this->database->rawQuery($sql);
	}
	
	public function getTagIdsArray(){
	    
	    $sql = "SELECT taglookup_tag_id FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->getId()."' AND taglookup_type='SM_ITEM_TAG_LINK'";
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
	    
	    $sql = "SELECT * FROM Tags, TagsObjectsLookup WHERE TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id='".$this->getId()."' AND TagsObjectsLookup.taglookup_type='SM_ITEM_TAG_LINK' ORDER BY Tags.tag_name";
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
	            $property->setContextualItemId($this->getId());
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
	    
	    if($this->getMetapageId()){
	        $page_id = $this->getMetapageId($this->getCurrentSiteId());
	    }else if($this->getModel()->getDefaultMetapageId($this->getCurrentSiteId())){
	        $page_id = $this->getModel()->getDefaultMetapageId($this->getCurrentSiteId());
	    }else{
	        return null;
	    }
	    
	    $lh = new SmartestCmsLinkHelper;
	    $lh->parse('metapage:id='.$page_id.':id='.$this->getId());
	    
	    return $lh->getUrl();
	    
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
	    
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag->getId()."', '".$this->getId()."', 'SM_ITEM_TAG_LINK')";
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
	    
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->getId()."' AND taglookup_tag_id='".$tag->getId()."' AND taglookup_type='SM_ITEM_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	public function getTextFragments(){
	    
	    $sql = "SELECT TextFragments.* FROM Items, Assets, ItemPropertyValues, TextFragments WHERE Items.item_id=ItemPropertyValues.itempropertyvalue_item_id AND Assets.asset_id=AssetIdentifiers.assetidentifier_live_asset_id AND TextFragments.textfragment_id=Assets.asset_fragment_id AND Pages.page_id='".$this->getId()."'";
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
		
		$sql = "SELECT TextFragments.* FROM Pages, Assets, AssetIdentifiers, TextFragments WHERE Pages.page_id=AssetIdentifiers.assetidentifier_page_id AND Assets.asset_id=AssetIdentifiers.assetidentifier_live_asset_id AND TextFragments.textfragment_id=Assets.asset_fragment_id AND Pages.page_id='".$this->getId()."' AND Assets.asset_type IN ('".implode("', '", $codes)."')";
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