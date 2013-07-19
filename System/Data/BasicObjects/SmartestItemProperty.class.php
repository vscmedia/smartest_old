<?php

class SmartestItemProperty extends SmartestBaseItemProperty implements SmartestTypedParameter{
	
	protected $_type_info;
	protected $_possible_values = array();
	protected $_possible_values_retrieval_attempted = false;
	protected $_option_set;
	protected $_model;
	protected $_property_info;
	
	protected function __objectConstruct(){
		
		/* $this->_table_prefix = 'itemproperty_';
		$this->_table_name = 'ItemProperties';
		$this->addPropertyAlias('VarName', 'varname'); */
		
	}
	
	public function hydrate($value){
	    
	    $result = parent::hydrate($value);
	    
	    if($result){
	        $this->getTypeInfo();
	    }
	    
	    if(isset($value['itemclass_id'])){
	        
	        $m = new SmartestModel;
	        
	        if($m->hydrate($value)){
	            $this->_model = $m;
	        }
	        
	    }
	    
	    return $result;
	    
	}
	
	public function __postHydrationAction(){
	    
	    $this->getInfo();
	    $this->_property_info = new SmartestParameterHolder("Settings for model '".$this->_properties['name']."'");
		$s = unserialize($this->_properties['info']);
		
		if(is_array($s)){
		    $this->_property_info->loadArray($s);
	    }else{
	        $this->_property_info->loadArray(array());
	    }
	    
	}
	
	public function delete($rebuild_cache=true){
	    
	    // clean up now-disused values for this property
	    $sql = "DELETE FROM ItemPropertyValues WHERE itempropertyvalue_property_id='".$this->getId()."'";
	    $this->database->rawQuery($sql);
	    
	    if($rebuild_cache){
	    
	        $model = new SmartestModel;
	    
            if($model->find($this->getItemclassId())){
    	        // delete property - this should done before any cache or code files are regenerated
    	        parent::delete();
    	        // clear the cache and rebuild auto object model file
    	        SmartestCache::clear('model_properties_'.$model->getId(), true);
    	        SmartestObjectModelHelper::buildAutoClassFile($model->getId(), $model->getName());
            }else{
                // log
            }
	    
        }
        
        parent::delete();
	    
	}

	public function getTypeInfo(){
	    
	    $datatypes = SmartestDataUtility::getDataTypes();
	    
	    if(!$this->_type_info){
	    
	        if(array_key_exists($this->getDatatype(), $datatypes)){
	            $this->_type_info = $datatypes[$this->getDatatype()];
            }
        
        }
        
        return $this->_type_info;
	    
	}
	
	//// URL Encoding is being used to work around a bug in PHP's serialize/unserialize. No actual URLS are necessarily in use here
	public function setInfoField($field, $new_data){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    $this->_property_info->setParameter($field, rawurlencode(utf8_decode($new_data)));
	    $this->_modified_properties['info'] = SmartestStringHelper::sanitize(serialize($this->_property_info->getArray()));
	    
	}
	
	public function getInfoField($field){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    
	    if($this->_property_info->hasParameter($field)){
	        return utf8_encode(stripslashes(rawurldecode($this->_property_info->getParameter($field))));
	    }else{
	        return null;
	    }
	}
	
	public function isForeignKey(){
	    $info = $this->getTypeInfo();
	    return $info['valuetype'] == 'foreignkey';
	}
	
	public function isManyToMany(){
	    $info = $this->getTypeInfo();
	    return $info['valuetype'] == 'manytomany';
	}
	
	public function getDraftValues($site_id, $raw=false, $unique=true){
	    
	    $site_id = (int) $site_id;
	    $sql = "SELECT itempropertyvalue_draft_content FROM ItemPropertyValues, Items WHERE itempropertyvalue_property_id='".$this->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND (Items.item_site_id='".$site_id."' OR Items.item_shared=1) AND Items.item_deleted=0";
	    $result = $this->database->queryToArray($sql);
	    $raw_values = array();
	    
	    foreach($result as $r){
	        $raw_values[] = $r['itempropertyvalue_draft_content'];
	    }
	    
	    if($unique){
	        $raw_values = array_unique($raw_values);
        }
	    
	    if($raw){
	        return $raw_values;
	    }
	    
	    $values = array();
	    
	    foreach($raw_values as $rv){
	        $values[] = SmartestDataUtility::objectize($rv, $this->getDatatype());
	    }
	    
	    return $values;
	    
	}
	
	public function getLiveValues($site_id, $raw=false, $unique=true){
	    
	    $site_id = (int) $site_id;
	    $sql = "SELECT itempropertyvalue_content FROM ItemPropertyValues, Items WHERE itempropertyvalue_property_id='".$this->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND (Items.item_site_id='".$site_id."' OR Items.item_shared=1) AND Items.item_deleted=0";
	    $result = $this->database->queryToArray($sql);
	    $raw_values = array();
	    
	    foreach($result as $r){
	        $raw_values[] = $r['itempropertyvalue_content'];
	    }
	    
	    if($unique){
	        $raw_values = array_unique($raw_values);
        }
	    
	    if($raw){
	        return $raw_values;
	    }
	    
	    $values = array();
	    
	    foreach($raw_values as $rv){
	        $values[] = SmartestDataUtility::objectize($rv, $this->getDatatype());
	    }
	    
	    return $values;
	    
	}
	
	public function getValuesCount($site_id){
	    
	    $site_id = (int) $site_id;
	    $sql = "SELECT itempropertyvalue_id FROM ItemPropertyValues, Items WHERE itempropertyvalue_property_id='".$this->getId()."' AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND (Items.item_site_id='".$site_id."' OR Items.item_shared=1) AND Items.item_deleted=0";
	    return count($this->database->queryToArray($sql));
	    
	}
	
	public function getAllValues($site_id, $raw=false){
	    
	    $all_raw_values = array_unique(array_merge($this->getDraftValues($site_id, true), $this->getLiveValues($site_id, true)));
	    
	}
	
	public function getValueSpread($site_id, $live=true){
	    
	    if($this->isForeignKey()){
	        
	        if($live){
	            $raw_values = $this->getLiveValues($site_id, true, false);
	        }else{
	            $raw_values = $this->getDraftValues($site_id, true, false);
	        }
	        
	        $value_counter = array();
	        $total_values = count($raw_values);
	        
	        foreach($raw_values as $v){
	            if(isset($value_counter[$v])){
	                $value_counter[$v]['count']++;
	            }else{
	                $value_counter[$v]['count'] = 1;
	                if(strlen($v)){
	                    $value_counter[$v]['value'] = SmartestDataUtility::objectize($v, $this->getDatatype());
	                    $value_counter[$v]['is_null'] = false;
                    }else{
                        $value_counter[$v]['value'] = new SmartestString("No Value");
                        $value_counter[$v]['is_null'] = true;
                    }
	            }
	        }
	        
	        foreach($value_counter as $k=>$v){
	            $value_counter[$k]['percent'] = round($v['count']/$total_values, 4)*100;
	        }
	        
	        return $value_counter;
	        
	    }
	    
	}
	
	public function getDataReUseRate($site_id, $live=true){
	    
	    if($this->isForeignKey()){
	        
	        if($live){
	            $all_values = $this->getLiveValues($site_id, true, false);
	            $unique_values = $this->getLiveValues($site_id, true, true);
	        }else{
	            $all_values = $this->getDraftValues($site_id, true, false);
	            $unique_values = $this->getDraftValues($site_id, true, true);
	        }
	        
	        return round(count($all_values)/count($unique_values), 4);
	        
	    }
	    
	}
	
	public function getPossibleValues(){
	    
	    if($this->_possible_values_retrieval_attempted){
	    
	        return $this->_possible_values;
	    
        }else{
            
            if($this->isForeignKey() || $this->isManyToMany()){
	            
	            $info = $this->getTypeInfo();
	            $filter = $this->getForeignKeyFilter();
	            
	            if($info['filter']['entitysource']['type'] == 'db'){
	                
	                if($this->getDatatype() == 'SM_DATATYPE_ASSET' || $this->getDatatype() == 'SM_DATATYPE_ASSET_SELECTION'){
	                    
	                    if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_ASSETGROUP'){
	                        
	                        $group = new SmartestAssetGroup;
                        
	                        if($group->find($this->getOptionSetId())){
	                            
	                            $this->_option_set = $group;
	                            
	                            // Force groups that have been selected as a filter to be available to this site by making them shared
	                            if(!$group->getShared() && $group->getSiteId() != $this->getCurrentSiteId()){
                                    $group->setShared(1);
                                    $group->save();
                                }
                                
                                $this->_possible_values = $this->_option_set->getMembers(1, $this->getCurrentSiteId(), false);
                            
                            }else{
                                
                                // the nominated group no longer exists, so get rid of the reference to it and just load all files
                                
                                SmartestLog::getInstance('system')->log("The file group of ID ".$this->getFilterValue()." that is used as a filter for item property {$this->getName()} can no longer be found. This property has been set back to allow all files of the appropriate type.");
                                $prop = $this->copy();

                                $this->_properties['option_set_id'] = '';
                                $this->_properties['option_set_type'] = 'SM_PROPERTY_FILTERTYPE_NONE';

                                $prop->seOptionSetId('');
                                $prop->seOptionSetType('SM_PROPERTY_FILTERTYPE_NONE');
                                $prop->save();
                                
                                $alh = new SmartestAssetsLibraryHelper;
    	                        $assets = $alh->getAssetClassOptions($filter, $this->getSiteId(), 1);
    	                        $this->_possible_values = $assets;
                                
                            }
                        
                        }else{
                            
                            // Assets are limited by type, but not by a specific group
                            
                            if(is_object($this->getSite())){
        	                    $site_id = $this->getSite()->getId();
        	                }
        	                
        	                if(substr($filter, 0, 13) == 'SM_ASSETCLASS'){
                                
                                // Assets are limited to a placeholder type, so multiple asset types
                                
                                $alh = new SmartestAssetsLibraryHelper;
        	                    $this->_possible_values = $alh->getAssetClassOptions($filter, $site_id, 1);

                            }else{
                                
                                $alh = new SmartestAssetsLibraryHelper;
                                $this->_possible_values = $alh->getAssetsByTypeCode($filter, $site_id, 1);
                                
                            }
                            
                        }
	                
	                }else if($this->getDatatype() == 'SM_DATATYPE_TEMPLATE'){
	                    
	                    $alh = new SmartestAssetsLibraryHelper;
	                    $this->_possible_values = $alh->getAssetsByTypeCode('SM_ASSETTYPE_SINGLE_ITEM_TEMPLATE', $site_id, 1);
	                    // Todo: take account of template groups here
	                    
	                }else if($this->getDatatype() == 'SM_DATATYPE_CMS_ITEM' || $this->getDatatype() == 'SM_DATATYPE_CMS_ITEM_SELECTION'){
	                    
	                    if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_NONE'){
	                        $model = new SmartestModel;
	                        if($model->find($filter)){
	                            $this->_possible_values = $model->getSimpleItems($this->getCurrentSiteId(), SM_STATUS_CURRENT);
	                        }else{
	                            // Model id not recognized
	                        }
	                    }else if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_DATASET'){
	                        $set = new SmartestCmsItemSet;
	                        if($set->find($this->getOptionSetId())){
	                            $this->_possible_values = $set->getSimpleMembers(SM_QUERY_ALL_DRAFT_CURRENT);
	                        }else{
	                            // Set Id not recognized
	                        }
	                    }
	                    
	                }else{ // Values are DB based, but not assets
	                
    	                if(is_object($this->getSite())){
    	                    $site_id = $this->getSite()->getId();
    	                }
	                    
	                    // build in $info['filter']['optionsettype'] as SmartestAssetGroup, SmartestCmsItemSet or whatever
	                    
	                    if(isset($info['filter']['entitysource']['class']) && class_exists($info['filter']['entitysource']['class'])){
	                        // echo $info['filter']['entitysource']['class'];
	                        if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_NONE' || !isset($info['filter']['optionsettype'][$this->getOptionSetType()])){
	                
        	                    $sql = $this->getForeignKeySelectSql($info, $filter, $site_id);
                                
                                $result = $this->database->queryToArray($sql);
        	                    $options = array();
        	                    $class = $info['filter']['entitysource']['class'];
        	                    
        	                    foreach($result as $raw_array){
	                        
        	                        $option = new $class;
        	                        $option->hydrate($raw_array);
        	                        $options[] = $option;
                        
        	                    }
	                    
        	                    $this->_possible_values = $options;
    	                    
	                        }else{
	                            
	                            $ost = $info['filter']['optionsettype'][$this->getOptionSetType()];
	                            
	                            if(class_exists($ost['class'])){
	                                
	                                $set = new $ost['class'];
	                                
	                                if($set->find($this->getOptionSetId())){
	                                    
	                                    $this->_possible_values = $set->getMembers();
	                                    $this->_option_set = $set;
	                                    
	                                }else{
	                                    
	                                    SmartestLog::getInstance('system')->log("The option set with ID ".$this->getFilterValue()." that is used as a filter for item property {$this->getName()} can no longer be found. This property has been set back to allow all options of the appropriate type.");
                                        $prop = $this->copy();

                                        $this->_properties['option_set_id'] = '';
                                        $this->_properties['option_set_type'] = 'SM_PROPERTY_FILTERTYPE_NONE';

                                        $prop->seOptionSetId('');
                                        $prop->seOptionSetType('SM_PROPERTY_FILTERTYPE_NONE');
                                        $prop->save();

	                                }
	                                
	                            }else{
	                                throw new SmartestException("Item property option set type data object class '".$ost['class']."' not defined or doesn't exist for property datatype '".$this->getDatatype()."', option set type '".$ost['id']."'.");
	                            }
	                            
	                        }
	                
    	                }else{
	                        
    	                    throw new SmartestException("Foreign key data object class '".$info['filter']['entitysource']['class']."' not defined or doesn't exist for property datatype: ".$this->getDatatype());
	                        
    	                }
	                
    	            }
	            
	            }else{
	                
    	            // non-database entity types? to be continued...
    	            $this->_possible_values = array();
	                
    	        }
	            
	            $this->_possible_values_retrieval_attempted = true;
	            return $this->_possible_values;
	            
	        }else{
	            $this->_possible_values_retrieval_attempted = true;
	            return array();
	        }
            
        }
	    
	}
	
	public function getPossibleValuesAsArrays(){
	    
	    $arrays = array();
	    $pv = $this->getPossibleValues();
	    
	    foreach($pv as $pvo){
	        $arrays[] = $pvo->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getDefaultValue(){
	    if($this->_properties['defaultvalue']){
	        try{
	            $v = SmartestDataUtility::objectize($this->_properties['defaultvalue'], $this->_properties['datatype']);
	            return $v;
            }catch(SmartestException $e){
                return false;
            }
        }else{
            return false;
        }
	}
	
	public function getForeignKeySelectSql($info, $filter, $site_id=null){
	    
	    $sql = "SELECT DISTINCT * FROM ".$info['filter']['entitysource']['table']." WHERE 1=1";
        
        if($filter && $info['filter']['entitysource']['matchfield']){
            $sql .= " AND ".$info['filter']['entitysource']['matchfield']." ='".$filter."'";
        }

        if($site_id && $info['filter']['entitysource']['sitefield']){
            if($info['filter']['entitysource']['sharedfield']){
                $sql .= " AND (".$info['filter']['entitysource']['sitefield']."='".$site_id."' OR ".$info['filter']['entitysource']['sharedfield']."='1')";
            }else{
                $sql .= " AND ".$info['filter']['entitysource']['sitefield']."='".$site_id."'";
            }
        }
        
        if(isset($info['filter']['entitysource']['filterfield'])){
            $sql .= " AND ".$info['filter']['entitysource']['filterfield']."='".$this->getForeignKeyFilter()."'";
        }
    
        if(isset($info['filter']['condition'])){
        
            foreach($info['filter']['condition'] as $condition){
                $sql .= ' AND '.$this->convertFieldCondition($condition);
            }
        
        }

        if(isset($info['filter']['entitysource']['sortfield'])){
            $sql .= " ORDER BY ".$info['filter']['entitysource']['sortfield'];
        }
        
        return $sql;
	    
	}
	
	public function getPossibleFileGroups($site_id){
	    
	    $groups = array();
	    
	    if($this->isForeignKey()){
            
            $info = $this->getTypeInfo();
            $filter = $this->getForeignKeyFilter();
            
            if(substr($filter, 0, 13) == 'SM_ASSETCLASS'){
                $alh = new SmartestAssetsLibraryHelper;
                $groups = $alh->getAssetGroupsByPlaceholderType($filter, $site_id);
            }else{
                $alh = new SmartestAssetsLibraryHelper;
                $groups = $alh->getTypeSpecificAssetGroupsByType($filter, $site_id);
            }
            
        }
	    
	    return $groups;
	    
	}
	
	public function getPossibleTemplateGroups($site_id){
	    
	    $groups = array();
	    
	    if($this->isForeignKey()){
            
            $info = $this->getTypeInfo();
            $filter = $this->getForeignKeyFilter();
            
            $tlh = new SmartestTemplatesLibraryHelper;
            $groups = $tlh->getTemplateGroups($filter, $site_id);
            
        }
	    
	    return $groups;
	    
	}
	
	public function getPossibleDataSets($site_id){
	    
	    $groups = array();
	    
	    if($this->isForeignKey() || $this->isManyToMany()){
            
            $info = $this->getTypeInfo();
            $filter = $this->getForeignKeyFilter();
            
            $model = new SmartestModel;
            
            if(is_numeric($filter) && $model->find($filter)){
                $sets = $model->getDataSets($site_id);
            }else{
                $sets = array();
            }
            
        }
	    
	    return $sets;
	    
	}
	
	public function convertFieldCondition($condition){
	    
	    $sql = $condition['field'];
	    
	    if(!isset($condition['operator'])){$condition['operator'] = 'EQUAL';}
	    
	    switch($condition['operator']){
	        case "NOT_EQUAL":
	        $sql .= ' != ';
	        break;
	        
	        case "NOT_LIKE":
	        $sql .= ' NOT LIKE ';
	        break;
	        
	        case "LIKE":
	        $sql .= ' LIKE ';
	        break;
	        
	        default:
	        $sql .= ' = ';
	        break;
	        
	    }
	    
	    $sql .= "'".$condition['value']."'";
	    
	    return $sql;
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "_type_info":
	        $p = new SmartestParameterHolder('Data type info for property: '.$this->getName());
	        $p->loadArray($this->getTypeInfo());
	        return $p;
	        
	        case "_options":
	        // print_r(count($this->getPossibleValues()));
	        return $this->getPossibleValues();
	        
	        case "_model":
	        return $this->getModel();
	        
	        case "input_html":
	        return $this->renderInput();
	        
	        case "hint":
	        return new SmartestString($this->getHint());
	        
	        case "default_value":
	        return $this->getDefaultValue();
	        
	        case "is_foreign_key":
	        case "is_fk":
	        return $this->isForeignKey();
	        
	        case "is_many_to_many":
	        case "is_m2m":
	        return $this->isManyToMany();
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
	public function getModel(){
	    
	    if(!$this->_model){
	        
	        $model = new SmartestModel;
	        $model->find($this->getItemclassId());
	        $this->_model = $model;
	        
	    }
	    
	    return $this->_model;
	    
	}
	
	public function getManyToManyRelationshipType(){
	    
	    if($this->isManyToMany()){
	        $info = $this->getTypeInfo();
	        if(isset($info['manytomany']['relationshipcode'])){
	            return $info['manytomany']['relationshipcode'];
            }else{
                throw new SmartestException("Datatype ".$info['id']." is identified as a many-to-many relationship but does not specify a many-to-many relationship type code.", SM_ERROR_CONFIG);
            }
	    }else{
	        throw new SmartestException("SmartestItemProperty::getManyToManyRelationshipType() can only be called on many-to-many properties.", SM_ERROR_USER);
	    }
	    
	}
	
	public function getManyToManyRelationshipItemEntityIndex(){
	    
	    if($this->isManyToMany()){
	        $info = $this->getTypeInfo();
	        if(isset($info['manytomany']['ipventityindex'])){
	            return $info['manytomany']['ipventityindex'];
            }else{
                throw new SmartestException("Datatype ".$info['id']." is identified as a many-to-many relationship but does not specify which entity represents the item property value record.", SM_ERROR_CONFIG);
            }
	    }else{
	        throw new SmartestException("SmartestItemProperty::getManyToManyRelationshipType() can only be called on many-to-many properties.", SM_ERROR_USER);
	    }
	    
	}
	
	public function getManyToManyRelationshipMappedObjectEntityIndex(){
	    
	    if($this->isManyToMany()){
	        $info = $this->getTypeInfo();
	        if(isset($info['manytomany']['mappedentityindex'])){
	            return $info['manytomany']['mappedentityindex'];
            }else{
                throw new SmartestException("Datatype ".$info['id']." is identified as a many-to-many relationship but does not specify which entity represents the mapped objects.", SM_ERROR_CONFIG);
            }
	    }else{
	        throw new SmartestException("SmartestItemProperty::getManyToManyRelationshipType() can only be called on many-to-many properties.", SM_ERROR_USER);
	    }
	    
	}
	
	public function usesAssets(){
	    
	    $info = $this->getTypeInfo();
	    return ($info['id'] == 'SM_DATATYPE_ASSET');
	    
	}
	
	public function getPossibleFileTypes(){
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    $filter = $this->getForeignKeyFilter();
	    
	    if(substr($filter, 0, 13) == 'SM_ASSETCLASS'){
            
            $types = $alh->getTypesByPlaceholderType($filter);

        }else{
            
            $t = $alh->getTypes($filter);
            $types = array($t[$filter]);
            
        }
        
        return $types;
	    
	}
	
	public function getInputDataForForm(){
	    
	}
	
	public function renderInput($form_name=false, $existing_value=false){
	    
	    // if is foreign key, get values
	    $info = $this->getTypeInfo();
	    $parameters = new SmartestParameterHolder('Item property input render parameters: '.$this->getName());
	    $parameters->setParameter('type',  $info['id']);
	    // $info['id'], $form_name, $existing_value, $values
	    if(!$form_name){
	        $form_name = $this->getVarname();
	    }
	    
	    $parameters->setParameter('name',  $form_name);
	    
	    if(!$existing_value){
	        $existing_value = $this->getDefaultValue();
	    }
	    
	    $parameters->setParameter('value',  $existing_value);
	    
	    if($this->isForeignKey()){
	        $options = $this->getPossibleValues();
	        $parameters->setParameter('options', $options);
	    }
	    
	    $m = new SmartyManager('InterfaceBuilder');
	    $renderer = $m->initialize('input');
	    // $renderer->assign('_input_data', $this->getInputDataForForm());
	    
	    return $renderer->renderBasicInput($parameters);
	}
	
	public function getSuggestionsForFormBasedOnIncomplete($string, $site_id){
	    
	    $values = array();
	    $sql = "SELECT ItemPropertyValues.itempropertyvalue_draft_content, ItemPropertyValues.itempropertyvalue_content FROM ItemPropertyValues, Items WHERE ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted !='1' AND Items.item_is_archived !='1' AND (Items.item_site_id='".$site_id."' OR Items.item_shared=1) AND (ItemPropertyValues.itempropertyvalue_draft_content LIKE '".$string."%' OR ItemPropertyValues.itempropertyvalue_content LIKE '".$string."%') AND ItemPropertyValues.itempropertyvalue_property_id='".$this->getId()."'";
	    $results = $this->database->queryToArray($sql);
	    
	    foreach($results as $r){
	        
	        if(isset($r['itempropertyvalue_draft_content']{1}) && !in_array($r['itempropertyvalue_draft_content'], $values)){
	            $values[] = $r['itempropertyvalue_draft_content'];
	        }
	        
	        if(isset($r['itempropertyvalue_content']{1}) && !in_array($r['itempropertyvalue_content'], $values)){
	            $values[] = $r['itempropertyvalue_content'];
	        }
	    }
	    
	    sort($values);
	    return $values;
	    
	}
	
	public function getStoredValues($site_id=null){
	    
	    if(is_numeric($site_id)){
	        $sql = "SELECT ItemPropertyValues.* FROM Items, ItemPropertyValues WHERE ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND (Items.item_shared=1 OR Items.item_site_id='".$site_id."') AND ItemPropertyValues.itempropertyvalue_property_id='".$this->getId()."'";
	    }else{
	        $sql = "SELECT * FROM ItemPropertyValues WHERE itempropertyvalue_property_id='".$this->getId()."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    $values = array();
	    
	    foreach($result as $r){
	        $v = new SmartestItemPropertyValue;
	        $v->hydrate($r);
	        $values[] = $v;
	    }
	    
	    return $values;
	    
	}
	
	// 
	
	public function getHint(){
	    return $this->getInfoField('hint');
	}
	
	public function setHint($hint){
	    return $this->setInfoField('hint', $hint);
	}
	
}