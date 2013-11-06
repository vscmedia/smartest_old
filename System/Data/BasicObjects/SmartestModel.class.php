<?php

class SmartestModel extends SmartestBaseModel{

	protected $_model_properties = array();
	protected $_model_settings;
	protected $_site;
	protected $_temporary_fields = null;
	
	protected function __objectConstruct(){
		
		// $this->_table_prefix = 'itemclass_';
		// $this->_table_name = 'ItemClasses';
		$this->_model_settings = new SmartestParameterHolder("Settings for new model");
		
	}
	
	public function find($id){
	    $bool = parent::find($id);
	    $this->buildPropertyMap();
	    return $bool;
	}
	
	protected function buildPropertyMap(){
		
		if(!count($this->_model_properties)){
	    
	        if(SmartestCache::hasData('model_properties_'.$this->_properties['id'], true)){
    		    $result = SmartestCache::load('model_properties_'.$this->_properties['id'], true);
    	    }else{
    		    $sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_properties['id']."' ORDER BY itemproperty_order_index ASC";
    		    $result = $this->database->queryToArray($sql);
    		    SmartestCache::save('model_properties_'.$this->_properties['id'], $result, -1, true);
    	    }
		
		    foreach($result as $db_property){
    			$property = new SmartestItemProperty;
    			$property->hydrate($db_property);
    			$this->_model_properties[] = $property;
    		}
		
	    }
		
	}
	
	public function refreshProperties($rebuild_auto_class=true){
	    
	    SmartestCache::clear('model_properties_'.$this->getId(), true);
	    $this->_model_properties = array();
	    $this->buildPropertyMap();
	    $this->buildAutoClassFile();
	    
	}
	
	public function getNextPropertyOrderIndex(){
	    
	    $next_index = 0;
	    $sql = "SELECT itemproperty_order_index FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_properties['id']."' ORDER BY itemproperty_order_index DESC LIMIT 1";
	    $result = $this->database->queryToArray($sql);
	    
	    if(count($result)){
	        $next_index = ((int) $result[0]['itemproperty_order_index']) + 1;
	    }
	    
	    return $next_index;
	}
	
	public function __postHydrationAction(){
	    
	    if(!$this->_model_settings){
	        $this->_model_settings = new SmartestParameterHolder("Settings for model '".$this->_properties['name']."'");
        }
        
		$s = unserialize($this->getSettings());
		
		if(is_array($s)){
		    $this->_model_settings->loadArray($s);
	    }else{
	        $this->_model_settings->loadArray(array());
	    }
	    
	}
	
	public function getProperties(){
	    
	    $this->buildPropertyMap();
	    return $this->_model_properties;
	    
	}
	
	public function getPropertiesForReorder(){
	    
	    $this->buildPropertyMap();
	    $props = array();
	    
	    foreach($this->_model_properties as $p){
	        $props[$p->getId()] = $p;
	    }
	    
	    return $props;
	    
	}
	
	public function getPropertiesForQueryEngine(){
	    
	    $this->buildPropertyMap();
	    $props = array();
	    
	    foreach($this->_model_properties as $p){
	        $props[$p->getId()] = $p;
	    }
	    
	    return $props;
	    
	}
	
	public function getAvailablePrimaryProperties(){
	    
	    $properties = array();
	    
	    // TODO: Make other property types usable as primary (define-first) properties
	    
	    foreach($this->getProperties() as $p){
	        if($p->getDatatype() == 'SM_DATATYPE_ASSET'){
	            $properties[] = $p;
	        }
	    }
	    
	    return $properties;
	    
	}
	
	public function hasPrimaryProperty(){
	    return (bool) $this->getPrimaryPropertyId();
	}
	
	public function getPrimaryProperty(){
	    $property = new SmartestItemProperty;
	    if($property->find($this->getPrimaryPropertyId())){
	        return $property;
	    }
	}
	
	public function refresh(){
	    SmartestCache::clear('model_properties_'.$this->_properties['id'], true);
        SmartestObjectModelHelper::buildAutoClassFile($this->_properties['id'], SmartestStringHelper::toCamelCase($this->getName()));
	}
	
	
	public function setSettingValue($field, $new_data){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    // URL Encoding is being used to work around a bug in PHP's serialize/unserialize. No actual URLS are necessarily in use here:
	    $this->_model_settings->setParameter($field, rawurlencode(utf8_decode($new_data)));
	    // $this->_model_settings_modified = true;
	    $this->_modified_properties['settings'] = SmartestStringHelper::sanitize(serialize($this->_model_settings->getArray()));
	    
	}
	
	public function getSettingValue($field){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    
	    if($this->_model_settings->hasParameter($field)){
	        return utf8_encode(stripslashes(rawurldecode($this->_model_settings->getParameter($field))));
	    }else{
	        return null;
	    }
	}
	
	public function delete($remove=false){
	    
	    if($remove){
	        
	        // Delete properties and property values
    	    foreach($this->getProperties() as $p){
    	        $p->delete(false);
    	    }
	        
	        // TODO: FS#360 delete references
	        // Delete items, now bereft of any property values
	        // $sql = "DELETE FROM ManyToManyLookups WHERE (Items.item_id=ManyToManyLookups.mtmlookup_entity_1_foreignkey AND ManyToManyLookups.mtmlookup_type='".SM_MTMLOOKUP_RECENTLY_EDITED_ITEMS."')item_itemclass_id='".$this->getId()."'";
    	    // $this->database->rawQuery($sql);
    	    
    	    if(is_file($this->getAutoClassFilePath())){
    	        unlink($this->getAutoClassFilePath());
    	    }
    	    
    	    if(is_file($this->getClassFilePath())){
    	        SmartestFileSystemHelper::move($this->getClassFilePath(), SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Documents/Deleted/'.$this->getClassName().'.class.php'));
    	    }
	        
    	    // Delete items, now bereft of any property values
    	    $sql = "DELETE FROM Items WHERE item_itemclass_id='".$this->getId()."'";
    	    $this->database->rawQuery($sql);
	    
    	    parent::delete();
	    
        }
	    
	}
	
	public function getStaticSets($site_id=null){
	    
	    $sql = "SELECT * FROM Sets WHERE Sets.set_itemclass_id='{$this->getId()}' AND Sets.set_type='STATIC'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND (Sets.set_site_id='".$site_id."' OR Sets.set_shared=1)";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $sets = array();
	    
	    foreach($result as $array){
	        $set = new SmartestCmsItemSet;
	        $set->hydrate($array);
	        $sets[] = $set;
	    }
	    
	    return $sets;
	    
	}
	
	public function getAutomaticSetsForNewItem($site_id=null){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_MODELS_STATIC_SETS');
	    $q->setTargetEntityByIndex(2);
	    $q->addQualifyingEntityByIndex(1, $this->getId());
	    $q->addForeignTableConstraint('Sets.set_type', 'STATIC');
	    
	    if(is_numeric($site_id)){
            $q->addForeignTableOrConstraints(
                array('field'=>'Sets.set_site_id', 'value'=> $site_id),
                array('field'=>'Sets.set_shared', 'value'=>'1')
            );
        }
        
        $q->addSortField('Sets.set_label');
        
        $result = $q->retrieve(false, null, false);
	    
	    return $result;
	}
	
	public function getAutomaticSetIdsForNewItem($site_id=null){
	    
	    $sets = $this->getAutomaticSetsForNewItem();
	    $ids = array();
	    
	    foreach($sets as $s){
	        $ids[] = $s->getId();
	    }
	    
	    return $ids;
	}
	
	public function addAutomaticSetForNewItemById($set_id){
	    
	    $set_id = (int) $set_id;
	    
	    $link = new SmartestManyToManyLookup;
	    $link->setEntityForeignKeyValue(1, $this->getId());
	    $link->setEntityForeignKeyValue(2, $set_id);
	    $link->setType('SM_MTMLOOKUP_MODELS_STATIC_SETS');
	    
	    $link->save();
	}
	
	public function removeAutomaticSetForNewItemById($set_id){
	    
	    $set_id = (int) $set_id;
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_MODELS_STATIC_SETS');
	    $q->setTargetEntityByIndex(2);
	    $q->addQualifyingEntityByIndex(1, $this->_properties['id']);
	    $q->addForeignTableConstraint('Sets.set_id', $set_id);
	    
	    $q->delete();
	    
	}
	
    public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "sets":
	        case "datasets":
	        return $this->getDataSets();
	        
	        case "default_metapage_id":
	        return $this->getDefaultMetaPageId($this->getCurrentSiteId());
	        
	        case "color":
	        case "colour":
	        return $this->getColor();
	        
	        case "item_name_field_name":
	        return $this->getItemNameFieldName();
	        
	        case "item_name_field_visible":
	        return $this->getItemNameFieldVisible();
	        
	        case "long_id_format":
	        return $this->getLongIdFormat();
	        
	        case "long_id_format_custom":
	        return $this->getLongIdFormatIsCustom();
	        
	        case "properties":
	        return new SmartestArray($this->getProperties());
	        
	        case 'default_sort_property':
	        return $this->getDefaultSortProperty();
	        
	        case 'default_sort_property_dir':
	        return $this->getDefaultSortPropertyDirection();
	        
	        case '_english_indefinite_article':
	        $n = $this->getName();
            $p = in_array(strtolower($n{0}), array('a', 'e', 'i', 'o', 'u')) ? 'An' : 'A';
            return new SmartestString($p);
            
            case '_related_items':
            // var_dump($this->getTemporaryRelatedItems());
            if(!is_null($this->getTemporaryRelatedItems())){
                // var_dump($this->getTemporaryRelatedItems()->count());
            }
            return $this->getTemporaryRelatedItems();
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
	public function __toArray(){
	    $data = parent::__toArray();
	    $data['default_metapage_id'] = $this->getDefaultMetaPageId($this->getCurrentSiteId());
	    return $data;
	}
	
    public function __toArrayLikeCmsItemArray(){
	    
	    $array = array();
	    $array['_model'] = $this->__toArray();
	    $array['_properties'] = array();
	    
	    foreach($this->_model_properties as $p){
	        
	        if(is_object($p)){
	            $array['_properties'][$p->getId()] = $p->__toArray();
	            $array['_properties'][$p->getId()]['_options'] = $p->getPossibleValuesAsArrays();
	            $array[$p->getId()] = '';
	        }
	    }
	    
	    return $array;
	    
	}
	
	public function getDataSets($site_id=''){
	    
	    $sql = "SELECT * FROM Sets WHERE set_itemclass_id='".$this->getId()."' AND (set_type='DYNAMIC' OR set_type='STATIC')";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND (set_site_id='".$site_id."' OR set_shared='1')";
	    }
	    
	    $sql .= " ORDER BY set_label";
	    
	    $result = $this->database->queryToArray($sql);
	    $sets = array();
	    
	    foreach($result as $r){
	        $s = new SmartestCmsItemSet;
	        $s->hydrate($r);
	        $sets[] = $s;
	    }
	    
	    return $sets;
	    
	}
	
	/* public function getPropertiesAsArrays(){
		
		$propertyarrays = array();
		
		foreach($this->getProperties() as $property){
		    $propertyarrays[] = $property->__toArray();
		}
		
		return $propertyarrays;
	} */
	
	public function getPropertyIds(){
		
		$ids = array();
		
		foreach($this->_model_properties as $mp){
			$ids[] = $mp->getId();
		}
		
		return $ids;
	}
	
	public function getPropertyNames($use_ids_as_keys=false){
		
		$propertynames = array();
		
		foreach($this->_model_properties as $mp){
		    if($use_ids_as_keys){
		        $propertynames[$mp->getId()] = $mp->getName();
		    }else{
			    $propertynames[] = $mp->getName();
		    }
		}
		
		return $propertynames;
	}
	
	public function getPropertyVarNames(){
		
		$propertynames = array();
		
		foreach($this->_model_properties as $mp){
			$propertynames[] = $mp->getVarName();
		}
		
		return $propertynames;
	}
	
	public function getPropertyVarNamesLookup(){
	    
	    $properties = array();
		
		foreach($this->_model_properties as $mp){
			$properties[$mp->getVarName()] = $mp->getId();
		}
		
		return $properties;
	}
    
    public function getClassName(){
	    return SmartestStringHelper::toCamelCase($this->getName());
    }
    
    public function getAutoClassName(){
        return 'auto'.$this->getClassName();
    }
    
    public function getSimpleItems($site_id='', $mode=0, $query='', $exclude=''){
        
        $mode = (int) $mode;
        
        $sql = "SELECT * FROM Items WHERE item_itemclass_id='".$this->_properties['id']."' AND item_deleted != 1";
        
        if(is_numeric($site_id)){
            $sql .= " AND item_site_id='".$site_id."'";
        }
        
        if($mode > 0){
            
            if(in_array($mode, array(4,5,6))){
                $sql .= " AND item_public='TRUE'";
            }else if(in_array($mode, array(1,2,3))){
                $sql .= " AND item_public='FALSE'";
            }
            
            if($mode == 8){
                $sql .= " AND item_is_archived='1'";
            }else{
                $sql .= " AND item_is_archived='0'";
            }
            
            if(in_array($mode, array(2,3,5,6))){
                $sql .= " AND item_modified > item_last_published";
            }
            
            if(in_array($mode, array(3,6))){
                $sql .= " AND item_changes_approved='1'";
            }else if(in_array($mode, array(2,5))){
                $sql .= " AND item_changes_approved='0'";
            }
            
        }
        
        if(strlen($query)){
            
            $words = explode(' ', $query);
            
            $sql .= ' AND (';
            $conditions = array();
            
            foreach($words as $w){
                $conditions[] = "(item_name LIKE '%".$w."%' OR item_search_field LIKE '%".$w."%')";
            }
            
            $sql .= implode(' AND ', $conditions).')';
            
        }
        
        if(is_array($exclude)){
            $sql .= " AND item_id NOT IN ('".implode("', '", $exclude)."')";
        }
        
        $sql .= " ORDER BY item_name";
        
        $result = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($result as $db_array){
            $item = new SmartestItem;
            $item->hydrate($db_array);
            $items[] = $item;
        }
        
        return $items;
    }
    
    public function getSimpleItemsAsArrays($site_id='', $mode=0, $query=''){
        
        $items = $this->getSimpleItems($site_id, $mode, $query);
        $arrays = array();
        
        foreach($items as $item){
            $arrays[] = $item->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getItemIds($site_id='', $mode=9){
        
        $sql = "SELECT item_id FROM Items WHERE item_itemclass_id='".$this->_properties['id']."' AND item_deleted != 1";
        
        if($mode > 5){
            $sql .= " AND Items.item_public='TRUE'";
        }
        
        if(in_array($mode, array(1,4,7,10))){
	    
	        $sql .= " AND Items.item_is_archived='1'";
	    
        }else if(in_array($mode, array(2,5,8,11))){
            
            $sql .= " AND Items.item_is_archived='0'";
            
        }
        
        if(is_numeric($site_id)){
            $sql .= " AND item_site_id='".$site_id."'";
        }
        
        $sql .= " ORDER BY item_name";
        
        $result = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($result as $db_array){
            $items[] = $db_array['item_id'];
        }
        
        return $items;
        
    }
    
    public function getItems($site_id='', $mode=9){
        
    }
    
    public function getPublishableSimpleItems($site_id, $user_id='', $include_unapproved_items=false){
        
        $sql = "SELECT Items.* FROM Items, ItemPropertyValues WHERE (Items.item_public='FALSE' OR (ItemPropertyValues.itempropertyvalue_content != ItemPropertyValues.itempropertyvalue_draft_content)) AND ItemPropertyValues.itempropertyvalue_item_id = Items.item_id AND Items.item_deleted='0' AND Items.item_site_id='".$site_id."' AND Items.item_itemclass_id='".$this->getId()."'";
        
        if(!$include_unapproved_items){
            $sql .= 'AND Items.item_changes_approved=\'1\'';
        }
        
        $sql .= ' ORDER BY Items.item_name';
        
        $results = $this->database->queryToArray($sql);
        $items = array();
        $ids = array();
        
        foreach($results as $r){
            if(!isset($ids[$r['item_id']])){
                $item = new SmartestItem;
                $item->hydrate($r);
                $items[] = $item;
                $ids[$r['item_id']] = 1;
            }
        }
        
        return $items;
        
    }
    
    public function getPublishableItemIds($site_id, $user_id='', $include_unapproved_items=false){
        
        $sql = "SELECT DISTINCT Items.item_id FROM Items, ItemPropertyValues WHERE (Items.item_public='FALSE' OR (ItemPropertyValues.itempropertyvalue_content != ItemPropertyValues.itempropertyvalue_draft_content AND ItemPropertyValues.itempropertyvalue_item_id = Items.item_id)) AND Items.item_deleted='0' AND Items.item_site_id='".$site_id."' AND Items.item_itemclass_id='".$this->getId()."'";
        
        if(!$include_unapproved_items){
            $sql .= 'AND Items.item_changes_approved=\'1\'';
        }
        
        $sql .= ' ORDER BY Items.item_name';
        
        $results = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($results as $r){
            // $item = new SmartestItem;
            // $item->hydrate($r);
            $items[] = $r['item_id'];
        }
        
        return $items;
        
    }
    
    public function getPublishableItems($site_id, $user_id='', $include_unapproved_items=false){
        
    }
    
    public function getMetaPages(){
        
        $sql = "SELECT * FROM Pages WHERE page_type='ITEMCLASS' AND page_dataset_id='".$this->_properties['id']."' and page_deleted != 'TRUE'";
        
        if(is_object($this->getSite())){
            $sql .= " AND page_site_id='".$this->getSite()->getId()."'";
        }
        
        $result = $this->database->queryToArray($sql);
        $pages = array();
        
        foreach($result as $record){
            $p = new SmartestPage;
            $p->hydrate($record);
            $pages[] = $p;
        }
        
        return $pages;
        
    }
    
    public function getMetaPagesAsArrays(){
        
        $pages = $this->getMetaPages();
        $arrays = array();
        
        foreach($pages as $p){
            $arrays[] = $p->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function hasMetaPageOnSiteId($site_id){
        
        $site_id = (int) $site_id;
        $sql = "SELECT page_id FROM Pages WHERE page_type='ITEMCLASS' AND page_dataset_id='".$this->_properties['id']."' AND page_deleted != 'TRUE' AND page_site_id='".$site_id."'";
        $result = $this->database->queryToArray($sql);
        return (bool) count($result);
        
    }
    
    public function hasPropertyWithId($id){
        return in_array($id, $this->getPropertyIds());
    }
    
    public function hasDefaultDescriptionPropertyId(){
        return is_numeric($this->getDefaultDescriptionPropertyId());
    }
    
    public function getAvailableDescriptionProperties(){
        
        $properties = $this->getProperties();
        $ok_properties = array();
        $asset_types = SmartestDataUtility::getAssetTypes();
        
        $text_asset_types = array();
        
        foreach($asset_types as $a){
            if($a['storage']['type'] == "database"){
                $text_asset_types[] = $a['id'];
            }
        }
        
        $assetclass_types = SmartestDataUtility::getAssetclassTypes();
        $text_assetclass_types = array();
        
        foreach($assetclass_types as $a){
            if(isset($a['descriptive']) && SmartestStringHelper::toRealBool($a['descriptive'])){
                $text_assetclass_types[] = $a['id'];
            }
        }
        
        $descriptive_filters = array_merge($text_asset_types, $text_assetclass_types);
        
        foreach($properties as $p){
            
            $info = $p->getTypeInfo();
            
            if((isset($info['long']) && strtolower($info['long']) != 'false') || ($p->getDatatype() == 'SM_DATATYPE_ASSET' && in_array($p->getForeignKeyFilter(), $descriptive_filters))){
                $ok_properties[] = $p;
            }
        }
        
        return $ok_properties;
        
    }
    
    public function getAvailableSortProperties(){
        
        // var_dump(SmartestDataUtility::getSortableDataTypeCodes('itemproperty'));
        
        $sql = "SELECT * FROM ItemProperties WHERE itemproperty_datatype IN ('".implode("', '", SmartestDataUtility::getSortableDataTypeCodes('itemproperty'))."') AND itemproperty_itemclass_id='".$this->getId()."'";
        $result = $this->database->queryToArray($sql);
        $properties = array();
        
        foreach($result as $r){
            $p = new SmartestItemProperty;
            $p->hydrate($r);
            $properties[] = $p;
        }
        
        return $properties;
        
    }
    
    public function getAvailableThumbnailProperties(){
        
        $sql = "SELECT * FROM ItemProperties WHERE itemproperty_datatype='SM_DATATYPE_ASSET' AND itemproperty_foreign_key_filter IN ('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE', 'SM_ASSETCLASS_STATIC_IMAGE') AND itemproperty_itemclass_id='".$this->getId()."'";
        $result = $this->database->queryToArray($sql);
        $properties = array();
        
        foreach($result as $r){
            $p = new SmartestItemProperty;
            $p->hydrate($r);
            $properties[] = $p;
        }
        
        return $properties;
        
    }
    
    public function getDefaultMetaPageId($site_id){
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::load('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id);
    }
    
    public function setDefaultMetaPageId($site_id, $id){
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::save('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id, $id);
    }
    
    public function getDefaultSortProperty(){
        
        $property_id = $this->getDefaultSortPropertyId();
        $property = new SmartestItemProperty;
        
        if($property->find($property_id)){
            return $property;
        }else{
            return null;
        }
        
    }
    
    public function getDefaultSortPropertyDirection(){
        return $this->getSettingValue('default_sort_property_dir');
    }
    
    public function setDefaultSortPropertyDirection($dir){
        $direction = in_array(strtoupper($dir), array('ASC', 'DESC')) ? $dir : 'ASC';
        return $this->setSettingValue('default_sort_property_dir', $direction);
    }
    
    public function clearDefaultMetaPageId($site_id){
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::clear('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id);
    }
    
    public function getAvailableDescriptionPropertiesAsArrays(){
        
        $properties = $this->getAvailableDescriptionProperties();
        $arrays = array();
        
        foreach($properties as $p){
            $arrays[] = $p->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getForeignKeyProperties($item_id=''){
        
        if(is_numeric($item_id)){
            
            $class = $this->getClassName();
            $item = new $class;
            
            if($item->hydrate($item_id)){
                $properties = $item->getPropertyValueHolders();
            }else{
                $properties = $this->getProperties();
            }
            
        }else{
            $properties = $this->getProperties();
        }
        
        $fk_properties = array();
        
        foreach($properties as $p){
            if($p->getDatatype() == 'SM_DATATYPE_CMS_ITEM'){
                $fk_properties[] = $p;
            }
        }
        
        return $fk_properties;
        
    }
    
    public function hasForeignKeyProperties(){
        
        return (bool) count($this->getForeignKeyProperties());
        
    }
    
    public function getForeignKeyPropertiesForModelId($model_id, $item_id=''){
        
        $model_id = (int) $model_id;
        
        $fk_properties = $this->getForeignKeyProperties($item_id);
        $properties = array();
        
        foreach($fk_properties as $p){
            if($p->getForeignKeyFilter() == $model_id){
                $properties[] = $p;
            }
        }
        
        return $properties;
        
    }
    
    public function hasForeignKeyPropertiesForModelId($model_id){
        
        return (bool) count($this->getForeignKeyPropertiesForModelId($model_id));
        
    }
    
    public function hasFeedProperties(){
        return (bool) count($this->getFeedProperties());
    }
    
    public function getFeedProperties(){
        
        $properties = $this->getProperties();
        $feed_properties = array();
    
        foreach($properties as $p){
            if($p->getDatatype() == 'SM_DATATYPE_FEED'){
                $feed_properties[] = $p;
            }
        }
    
        return $feed_properties;
    }
    
    public function getReferringProperties(){
        
        $sql = "SELECT ItemProperties.* FROM ItemProperties, ItemClasses WHERE ItemProperties.itemproperty_itemclass_id=ItemClasses.itemclass_id AND ItemProperties.itemproperty_datatype='SM_DATATYPE_CMS_ITEM' AND ItemProperties.itemproperty_foreign_key_filter='".$this->getId()."' ORDER BY ItemClasses.itemclass_plural_name";
        $result = $this->database->queryToArray($sql);
        $properties = array();
        
        foreach($result as $r){
            $p = new SmartestItemProperty;
            $p->hydrate($r);
            $properties[] = $p;
        }
        
        return $properties;
        
    }
    
    public function getMainSite(){
        
        $s = new SmartestSite;
        
        if($this->getSiteId() == '0'){
            
            $sql = "SELECT site_id FROM Sites ORDER BY site_id ASC";
            $result = $this->database->queryToArray($sql);
            $first_site_id = $result[0]['site_id'];
            
            // This code will be uncommented in future
            // It ensures the model is always attached to a site
            /* $copy = $this->copy();
            $copy->setSiteId($first_site_id);
            $copy->save(); */
            
            SmartestLog::getInstance('system')->log("\SmartestModel->getMainSite() was called on a model (".$this->getName().") that does not yet have a site attached to it.");
            $site_id = $first_site_id;
            
        }else{
            $site_id = $this->getSiteId();
        }
        
        if(!$s->find($site_id)){
            SmartestLog::getInstance('system')->log("Model ".$this->getName()." is attached to a site that no longer exists and must be reassigned.");
        }
        
        return $s;
    }
    
    public function getSitesWhereUsed(){
        
        $sql = str_replace('%', $this->getid(), "SELECT DISTINCT Sites.* FROM Sites, Items, ItemClasses, Pages WHERE (Items.item_itemclass_id=ItemClasses.itemclass_id AND Items.item_site_id=Sites.site_id AND ItemClasses.itemclass_id='%') OR (Pages.page_type='ITEMCLASS' AND Pages.page_dataset_id='%' AND Pages.page_site_id=Sites.site_id)");
        $result = $this->database->queryToArray($sql);
        
        $sites = array();
        
        foreach($result as $r){
            $s = new SmartestSite;
            $s->hydrate($r);
            $sites[] = $s;
        }
        
        return $sites;
    }
    
    /* public function getMainSite(){
        $n = new SmartestSite;
        if($n->find($this->getSiteId())){
            return $n;
        }
    } */
    
    public function getNumberOfSitesWhereUsed(){
        return count($this->getSitesWhereUsed());
    }
    
    public function isUsedOnMultipleSites(){
        return $this->getNumberOfSitesWhereUsed() > 1;
    }
    
    public function getOtherModelsWithSameName(){
        
        $sql = "SELECT * FROM ItemClasses WHERE itemclass_type='SM_ITEMCLASS_MODEL' AND itemclass_name='".$this->getName()."' AND itemclass_id != '".$this->getId()."'";
        $result = $this->database->queryToArray($sql);
        
        $model_objects = array();
		
		foreach($result as $model){
			$m = new SmartestModel;
			$m->hydrate($model);
			$model_objects[] = $m;
		}
		
		return $model_objects;
        
    }
    
    public function hasSameNameAsModelOnOtherSite(){
        
        return (bool) count($this->getOtherModelsWithSameName());
        
    }
    
    public function setShared($new_shared_status){
        
        $currently_shared = $this->getShared();
        
        // If the shared status is being changed
        if($currently_shared != $new_shared_status){
            if($new_shared_status == '0'){
                // Model is being made site-specific, so move class file to Sites/.../Library/ObjectModel/
                if(SmartestFileSystemHelper::move($this->getClassFilePath(1), $this->getClassFilePath(0))){
                    // echo 'moved to sites folder';
                    parent::setShared('0');
                    return true;
                }else{
                    // class file couldn't be moved
                    return false;
                }
            }else if($new_shared_status == '1'){
                // Model is being shared, so move class file to Library/ObjectModel/
                if(SmartestFileSystemHelper::move($this->getClassFilePath(0), $this->getClassFilePath(1))){
                    // echo 'moved to library folder';
                    parent::setShared('1');
                    return true;
                }else{
                    // class file couldn't be moved
                    return false;
                }
            }
        }else{
            return true;
        }
    }
    
    public function getFilesThatMustBeWrtableForSharingToggle(){
        
        return array(
            SmartestFileSystemHelper::dirName($this->getClassFilePath(1)),
            SmartestFileSystemHelper::dirName($this->getClassFilePath(0)),
            $this->getClassFilePath()
        );
        
    }
    
    public function getFilesThatMustBeWrtableForSharingToggleButAreNot(){
        
        $bad_files = array();
        
        foreach($this->getFilesThatMustBeWrtableForSharingToggle() as $file){
            if(!is_writable($file)){
                $bad_files[] = $file;
            }
        }
        
        return $bad_files;
        
    }
    
    public function isMovable(){
        
        return count($this->getFilesThatMustBeWrtableForSharingToggleButAreNot()) ? false : true;
        
    }
    
    public function save(){
        
        $r = parent::save();
        SmartestCache::clear('models_query_site_'.$this->getSiteId(), true);
        SmartestCache::clear('models_query'.$this->getSiteId(), true);
        return $r;
        
    }
    
    public function getColor(){
        
        $raw = $this->getSettingValue('color');
        if(!$raw) $raw = '222';
        return new SmartestRgbColor($raw);
        
    }
    
    public function setColor($color){
        
        $c = new SmartestRgbColor($color);
        $sf = $c->getStorableFormat();
        
        return $this->setSettingValue('color', $sf);
        
    }
    
    public function getItemNameFieldName(){
        
        $raw = $this->getSettingValue('item_name_field_name');
        if(!$raw) $raw = 'Name';
        return new SmartestString($raw);
        
    }
    
    public function setItemNameFieldName($infn){
        
        $c = new SmartestString($infn);
        $sf = $c->getStorableFormat();
        
        return $this->setSettingValue('item_name_field_name', $sf);
        
    }
    
    public function setLongIdFormat($format){
        return $this->setSettingValue('item_long_id_format', $format);
    }
    
    public function getLongIdFormat(){
        $format = $this->getSettingValue('item_long_id_format');
        if(strlen($format)){
            return $format;
        }else{
            return '_STD';
        }
    }
    
    public function getLongIdFormatIsCustom(){
        return !in_array($this->getLongIdFormat(), array('_STD', '_UUID', 'NNNNNNNNNNNNNNNN', 'my-NNNNNNNNNNNN', 'my-NNNNNNNN', 'NNNNNNNN', 'CCCCCC'));
    }
    
    public function getItemNameFieldVisible(){
        
        $raw = $this->getSettingValue('item_name_field_visible');
        $c = is_null($raw) || SmartestStringHelper::toRealBool($raw) ? true : false;
        // if(!$raw) $raw = 'Name';
        return $c;
        
    }
    
    public function setItemNameFieldVisible($infv){
        
        $c = !SmartestStringHelper::toRealBool($infv) ? false : true;
        // $sf = $c->getStorableFormat();
        
        return $this->setSettingValue('item_name_field_visible', $c);
        
    }
    
    public function initTemporaryFields(){
        if(!is_object($this->_temporary_fields)){
            $this->_temporary_fields = new SmartestParameterHolder('Temporary fields for model '.$this->getName());
        }
    }
    
    public function setTemporaryField($name, $value){
        $this->initTemporaryFields();
        $this->_temporary_fields->setParameter($name, $value);
    }
    
    public function getTemporaryField($name){
        $this->initTemporaryFields();
        return $this->_temporary_fields->getParameter($name);
    }
    
    public function clearTemporaryField($name){
        $this->initTemporaryFields();
        $this->_temporary_fields->clearParameter($name);
    }
    
    ///////////////////////////// Temporary fields ////////////////////////////
    
    public function setTemporaryRelatedItems($items){
        $this->setTemporaryField('related_items', $items);
    }
    
    public function getTemporaryRelatedItems(){
        return $this->getTemporaryField('related_items');
    }
    
    ///////////////////////////// Code for building and including model classes /////////////////////////////////
    
    public function init(){
        
        // Do constants first
	    $constant_name = SmartestStringHelper::toCamelCase($this->getName());
	    $new_constant_name = strtoupper(SmartestStringHelper::toVarName('Model '.$this->getName(), true));
		
		if(!defined($constant_name)){
			define($constant_name, $this->getId(), true);
			define($new_constant_name, $this->getId(), true);
		}
		
		// if(is_file(SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php')){
		if(class_exists($this->getAutoClassName())){ // $this->getAutoClassName()
		    
		}else{
        	if(is_file($this->getAutoClassFilePath())){
        		// include SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php';
        		include $this->getAutoClassFilePath();
        	}else{
        		// build auto class
        		if($this->buildAutoClassFile()){
        			// include SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php';
        			include $this->getAutoClassFilePath();
        		}else{
        			throw new SmartestException('Could not auto-generate model base class: '.$this->getName(), SM_ERROR_MODEL);
        		}
        	}
	    }
		
		if(class_exists($this->getClassName())){
		    
		}else{
		    if(is_file($this->getClassFilePath())){
        		// include SM_ROOT_DIR.'Library/ObjectModel/'.$class_name.'.class.php';
        		include $this->getClassFilePath();
        	}else{
        		// build extensible class
        		if($this->buildClassFile()){
        			// include SM_ROOT_DIR.'Library/ObjectModel/'.$class_name.'.class.php';
        			include $this->getClassFilePath();
        		}else{
        			throw new SmartestException('Could not auto-generate model class: '.$this->getName(), SM_ERROR_MODEL);
        		}
        	}
	    }
        
    }
    
    public function getAutoClassFilePath(){
        
        // if($this->getShared()){
            return SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$this->getClassName().$this->getId().'.class.php';
        // }else{
        //    return SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$this->getClassName().'.class.php';
        //}
        
    }
    
    public function getComments($status, $site_id=''){
        
        $sql = "SELECT * FROM Items, Comments WHERE Comments.comment_object_id=Items.item_id AND Comments.comment_type='SM_COMMENTTYPE_ITEM_PUBLIC' AND Comments.comment_status='".$status."' AND Items.item_itemclass_id='".$this->getId()."'";
        $result = $this->database->queryToArray($sql);
        
        $comments = array();
        
        foreach($result as $r){
            $c = new SmartestItemPublicComment;
            $c->hydrateWithSimpleItem($r);
            $comments[] = $c;
        }
        
        return $comments;
        
    }
    
    // returs a binary, rather that getShared() which returns a raw value from the database
    public function isShared(){
        return ($this->_properties['shared'] == '1');
    }
    
    public function getClassFilePath($shared_status=-1){
        
        if($shared_status == -1){
            $shared = $this->isShared();
            $specified = false;
        }else{
            $shared = $shared_status;
            $specified = true;
        }
        
        if($shared){
            return SM_ROOT_DIR.'Library/ObjectModel/'.$this->getClassName().'.class.php';
        }else{
            if(!is_dir(SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/') && !@mkdir(SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/')){
                SmartestLog::getInstance('system')->log('Site-specific model class could not be created because '.SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/ is not writable.', SmartestLog::WARNING);
                if(!$specified){
                    return SM_ROOT_DIR.'Library/ObjectModel/'.$this->getClassName().'.class.php';
                }
            }
            return SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/'.$this->getClassName().'.class.php';
        }
        
    }
    
    public function buildClassFile(){
		
		$className = $this->getClassName();
		$file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/object_template.txt');
		    
		if(strlen($file)){
	
			$file = str_replace('__THISCLASSNAME__', $className, $file);
			$file = str_replace('__AUTOCLASSNAME__', 'auto'.$className, $file);
			$file = str_replace('__TIME__', date("Y-m-d H:i:s"), $file);
			
			if(file_put_contents($this->getClassFilePath(), $file)){
			    // change the permissions so that FTP users can edit without messing about with permissions
			    chmod($this->getClassFilePath(), 0666);
			    return true;
			}
		
		}else{
			return false;
		}
		
	}
    
    public function buildAutoClassFile(){
		
		$className = $this->getClassName();
	    $properties = $this->getProperties();
	    $constants = '';
	
		foreach($properties as $property){
		
			$constant_name  = SmartestStringHelper::toConstantName($property->getName());
			$constant_value = $property->getId();
		
			if(is_numeric($constant_name{0})){
				$constant_name = '_'.$constant_name;
			}
		
			$new_constant = '    const '.$constant_name.' = '.$constant_value.";\n";
		    $constants .= $new_constant;
		
		}
	    
	    $file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/autoobject_template.txt');
		
		$functions = $this->buildAutoClassFunctionCode();
		$varnames_lookup = $this->buildAutoClassVarnameLookupCode();
	
		$file = str_replace('__THISCLASSNAME__', $this->getAutoClassName(), $file);
		$file = str_replace('__THECONSTANTS__', $constants, $file);
		$file = str_replace('__THEFUNCTIONS__', $functions, $file);
		$file = str_replace('__THEVARNAMELOOKUPS__', $varnames_lookup, $file);
		$file = str_replace('__MODEL_ID__', $this->getId(), $file);
		$file = str_replace('__TIME__', date("Y-m-d h:i:s"), $file);
	    
	    if(file_put_contents($this->getAutoClassFilePath(), $file)){
			return true;
		}else{
			return false;
		}
		
	}
	
	public function buildAutoClassVarnameLookupCode(){
	    
	    $varnames_lookup = '    protected $_varnames_lookup = array('."\n";
		$i = 1;
		
		$properties = $this->getProperties();
		
		foreach($properties as $property){
			
			$new_constant = "        '".$property->getVarname()."' => ".$property->getId();
			
			if($i < count($properties)){
			    $new_constant .= ',';
			}
			
			$new_constant .= "\n";
			
			$varnames_lookup .= $new_constant;
			$i++;
			
		}
		
		$varnames_lookup .= '    );'."\n\n";
		
		return $varnames_lookup;
	    
	}
	
	public function buildAutoClassFunctionCode(){
	    
	    $file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/autoobject_datafunctions.txt');
	    $code = '';
	    
	    foreach($this->getProperties() as $property){
			
			$constant_name  = SmartestStringHelper::toCamelCase($property->getName());
			$constant_value = $property->getId();
			$bool_typecast = $property->getDatatype() == '' ? '(bool) ' : '';
			
			$f = str_replace('__PROPNAME__', $constant_name, $file);
			$f = str_replace('__PROPID__', $constant_value, $f);
			$f = str_replace('__BOOLTYPECAST__', $bool_typecast, $f);
			
			$code .= $f;
			
		}
		
		return $code;
	    
	}
	
	public function getMembersListUrl(){
	    
	    $request = SmartestPersistentObject::get('controller')->getCurrentRequest();
	    
	    
	}
    
}
