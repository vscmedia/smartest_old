<?php

class SmartestItemProperty extends SmartestBaseItemProperty{
	
	protected $_type_info;
	protected $_possible_values = array();
	protected $_possible_values_retrieval_attempted = false;
	protected $_option_set;
	protected $_model;
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'itemproperty_';
		$this->_table_name = 'ItemProperties';
		$this->addPropertyAlias('VarName', 'varname');
		
	}
	
	public function hydrate($value){
	    
	    $result = parent::hydrate($value);
	    
	    if($result){
	        $this->getTypeInfo();
	    }
	    
	    return $result;
	    
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
	
	public function isForeignKey(){
	    $info = $this->getTypeInfo();
	    return $info['valuetype'] == 'foreignkey';
	}
	
	public function isManyToMany(){
	    $info = $this->getTypeInfo();
	    return $info['valuetype'] == 'manytomany';
	}
	
	public function getPossibleValues(){
	    
	    if($this->_possible_values_retrieval_attempted){
	    
	        return $this->_possible_values;
	    
        }else{
            
            if($this->isForeignKey() || $this->isManyToMany()){
	            
	            $info = $this->getTypeInfo();
	            $filter = $this->getForeignKeyFilter();
	            
	            if($info['filter']['entitysource']['type'] == 'db'){
	                
	                if($this->getDatatype() == 'SM_DATATYPE_ASSET'){
	                    
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
                                
                                // if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_NONE'){

        	                        $alh = new SmartestAssetsLibraryHelper;
        	                        $this->_possible_values = $alh->getAssetClassOptions($filter, $site_id, 1);

        	                    // }

                            }else{
                                
                                $alh = new SmartestAssetsLibraryHelper;
                                $this->_possible_values = $alh->getAssetsByTypeCode($filter, $site_id, 1);
                                
                            }
                            
                        }
	                
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
	                    
	                    // echo "hello";
	                    
	                }else{ // Values are DB based, but not assets
	                
    	                if(is_object($this->getSite())){
    	                    $site_id = $this->getSite()->getId();
    	                }
	                    
	                    // build in $info['filter']['optionsettype'] as SmartestAssetGroup, SmartestCmsItemSet or whatever
	                    
	                    if(isset($info['filter']['entitysource']['class']) && class_exists($info['filter']['entitysource']['class'])){
	                        
	                        if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_NONE' || !isset($info['filter']['optionsettype'][$this->getOptionSetType()])){
	                
        	                    $sql = $this->getForeignKeySelectSql($info, $filter, $site_id);
                                
                                $result = $this->database->queryToArray($sql);
        	                    $options = array();
        	                    $class = $info['filter']['entitysource']['class'];
        	                    
        	                    foreach($result as $raw_array){
	                        
        	                        $option = new $class;
        	                        // $option = new stdClass;
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
	
	public function getForeignKeySelectSql($info, $filter, $site_id=null){
	    
	    $sql = "SELECT * FROM ".$info['filter']['entitysource']['table']." WHERE 1=1";
        
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
                $groups = $alh->getPlaceholderAssetGroupsByType($filter, $site_id);
            }else{
                $alh = new SmartestAssetsLibraryHelper;
                $groups = $alh->getTypeSpecificAssetGroupsByType($filter, $site_id);
            }
            
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
	        return $this->getPossibleValues();
	        case "_model":
	        return $this->getModel();
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
            // print_r($t);
            $types = array($t[$filter]);
            
        }
        
        return $types;
	    
	}
	
}