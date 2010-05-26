<?php

class SmartestItemProperty extends SmartestBaseItemProperty{
	
	protected $_type_info;
	protected $_possible_values = array();
	protected $_possible_values_retrieval_attempted = false;
	protected $_option_set;
	
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
	    
	    if($info['valuetype'] == 'foreignkey'){
	        return true;
	    }else{
	        return false;
	    }
	    
	}
	
	public function getPossibleValues(){
	    
	    if($this->_possible_values_retrieval_attempted){
	    
	        return $this->_possible_values;
	    
        }else{
            
            if($this->isForeignKey()){
	            
	            $info = $this->getTypeInfo();
	            $filter = $this->getForeignKeyFilter();
	            // echo $filter.' ';
	            
	            if($info['filter']['entitysource']['type'] == 'db'){
	                
	                if($this->getDatatype() == 'SM_DATATYPE_ASSET' && substr($filter, 0, 13) == 'SM_ASSETCLASS'){
	                    
	                    if(is_object($this->getSite())){
    	                    $site_id = $this->getSite()->getId();
    	                }
	                    
	                    if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_NONE'){
	                        
	                        $alh = new SmartestAssetsLibraryHelper;
	                        $assets = $alh->getAssetClassOptions($filter, $this->getSiteId(), 1);
	                        $this->_possible_values = $assets;
	                        
	                    }else if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_ASSETGROUP'){
	                        
	                        $group = new SmartestAssetGroup;
	                        
	                        if($group->find($this->getOptionSetId())){
	                            
	                            if(!$group->getShared() && $group->getSiteId() != $this->getCurrentSiteId()){
	                                $group->setShared(1);
	                                $group->save();
	                            }
	                            
	                            $assets = $group->getMembers();
	                            $this->_possible_values = $assets;
	                            $this->_option_set = $group;
	                            
	                        }else{
	                            
	                            // the nominated set no longer exists, so get rid of the reference to it and just load all files
	                            
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
	                        
	                    }
	                    
	                }else{
	                
    	                if(is_object($this->getSite())){
    	                    $site_id = $this->getSite()->getId();
    	                }
	                    
	                    // build in $info['filter']['optionsettype'] as SmartestAssetGroup, SmartestCmsItemSet or whatever
	                    
	                    if(isset($info['filter']['entitysource']['class']) && class_exists($info['filter']['entitysource']['class'])){
	                        
	                        if($this->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_NONE' || !isset($info['filter']['optionsettype'][$this->getOptionSetType()])){
	                
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
        	                    
        	                    // echo $sql;
                                
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
	        case "_options":
	        return $this->getPossibleValues();
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
}