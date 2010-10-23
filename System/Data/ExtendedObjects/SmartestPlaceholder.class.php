<?php

class SmartestPlaceholder extends SmartestAssetClass{
    
    protected $_asset_type;
    
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('TypeId', 'assettype_id');
		$this->_table_prefix = 'assetclass_';
		$this->_table_name = 'AssetClasses';
		
	}
	
	public function getAssetType(){
	    
	    if(!is_object($this->_asset_type)){
	        $type = new SmartestPlaceHolder;
	        $type->hydrate($this->getAssetTypeId());
	        $this->_asset_type = $type;
        }
	    
	    return $this->_asset_type;
	    
	}
	
	public function getPossibleAssets($site_id=''){
	    
	    if(!is_numeric($site_id)){
	        $site_id = $this->getSite()->getId();
	    }
	    
	    // print_r($type['accept']);
	    
	    /*if($this->getType() == 'SM_ASSETTYPE_IMAGE'){
            $types = array('SM_ASSETTYPE_IMAGE', 'SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE');
        }else{
            $types = array($this->getType());
        } */
        
        $assets = array();
        
        if($this->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_NONE'){
            
            $type = $this->getTypeInfo();
            $helper = new SmartestAssetsLibraryHelper;
            $assets = $helper->getAssetsByTypeCode($type['accept'], $site_id, 1);
        
        }else if($this->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_ASSETGROUP'){
            
            $group = new SmartestAssetGroup;
            
            if($group->find($this->getFilterValue())){
                
                $assets = $group->getMembers();
                
            }else{
                
                SmartestLog::getInstance('system')->log("The file group of ID ".$this->getFilterValue()." that is used as a filter for placeholder {$this->getName()} can no longer be found. This placeholder has been set back to allow all files.");
                $ph = $this->copy();
                
                $this->_properties['filter_type'] = 'SM_ASSETCLASS_FILTERTYPE_NONE';
                $this->_properties['filter_value'] = '';
                
                $ph->setFilterType('SM_ASSETCLASS_FILTERTYPE_NONE');
                $ph->setFilterValue('');
                $ph->save();
                
                $type = $this->getTypeInfo();
                $helper = new SmartestAssetsLibraryHelper;
                $assets = $helper->getAssetsByTypeCode($type['accept'], $site_id, 1);
                
            }
            
        }
        
        return $assets;
        
	}
	
	public function getPossibleAssetsAsArrays(){
	    
	    $objects = $this->getPossibleAssets();
	    $arrays = array();
	    
	    foreach($objects as $asset){
	        $arrays[] = $asset->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getPossibleFileGroups($site_id=''){
	    
	    $helper = new SmartestAssetClassesHelper;
	    $groups = $helper->getAssetGroupsForPlaceholderType($this->getType(), $site_id);
	    
	    return $groups;
	    
	}
	
	public function getPossibleFileTypes(){
	    
	    $h = new SmartestAssetClassesHelper;
	    return $h->getAssetTypesFromAssetClassType($this->getType());
	    
	}
	
	public function getPossibleFileTypeCodes(){
	    
	    $h = new SmartestAssetClassesHelper;
	    return $h->getAssetTypeCodesFromAssetClassType($this->getType());
	    
	}
	
	public function isLinkable(){
	    
	    $type = $this->getTypeInfo();
	    return (isset($type['linkable']) && $type['linkable'] && strtolower($type['linkable']) != 'false') ? true : false;
	    
	}
	
	public function isEditableFromPreview(){
	    
	    $type = $this->getTypeInfo();
	    return (isset($type['setfrompreview']) && SmartestStringHelper::toRealBool($type['setfrompreview'])) ? true : false;
	    // print_r($type);
	    
	}
	
	public function acceptsImages(){
	    
	    $type = $this->getTypeInfo();
        $type_codes = $type['accept'];
	    
	}
	
	public function getAssetsByType(){
	    
	    /* if(!is_numeric($site_id)){
	        $site_id = $this->getSite()->getId();
	    } */
	    
	    $args = func_get_args();
	    
	    if(count($args)){
	        
	        // detect whether we are being passed an array of types, or a list of arguments
	        if(is_array($args[0][0])){
	            $types = $args[0][0];
	        }else if(is_array($args[0])){
	            // print_r($args);
	            $types = $args[0];
            }else{
                $types = $args;
            }
        }else{
            // no types were selected
            $types = array();
        }
        
        $helper = new SmartestAssetsLibraryHelper;
        $assets = $helper->getAssetsByTypeCode($types, $site_id, 1);
        
        /* if(count($types)){
            
            $sql = "SELECT * FROM Assets WHERE asset_type IN (";
            
            foreach($types as $key => $t){
                
                if($key > 0){
                    $sql .= ', ';
                }
                
                $sql .= "'".$t."'";
                
            }
            
            $sql .= ') AND asset_deleted = 0';
            
            if(is_numeric($site_id)){
                $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared='1')";
            }
            
            $sql .= ' ORDER BY asset_stringid';
            
            $result = $this->database->queryToArray($sql);
            $official_types = SmartestDataUtility::getAssetTypes();
            $assets = array();
            
            foreach($result as $raw_asset){
                
                if(in_array($raw_asset['asset_type'], array_keys($official_types))){
                    
                    $try_class = $official_types[$raw_asset['asset_type']]['class'];
                    
                    if(class_exists($try_class)){
                        $class = $try_class;
                    }else{
                        $class = 'SmartestAsset';
                    }
                    
                }else{
                    $class = 'SmartestAsset';
                }
                
                $asset = new $class;
                $asset->hydrate($raw_asset);
                $assets[] = $asset;
                
            } 
            
            return $assets;
            
        }else{
            
            return array();
            
        }*/
        
        return $assets;
	}
	
	public function getAssetsByTypeAsArrays(){
	    
	    $types = func_get_args();
	    $objects = $this->getAssetsByType($types);
	    $array = array();
	    
	    foreach($objects as $asset){
	        $array[] = $asset->__toArray();
	    }
	    
	    return $array;
	    
	}
	
	public function getDefinitions($draft_mode=false, $site_id=''){
	    
	    $assetidentifier_field = $draft_mode ? 'assetidentifier_draft_asset_id' : 'assetidentifier_live_asset_id';
	    $sql = "SELECT * FROM AssetIdentifiers, AssetClasses, Assets, Pages WHERE AssetIdentifiers.".$assetidentifier_field." = Assets.asset_id AND AssetIdentifiers.assetidentifier_assetclass_id=AssetClasses.assetclass_id AND AssetClasses.assetclass_id='".$this->getId()."' AND AssetIdentifiers.assetidentifier_page_id=Pages.page_id";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND Pages.page_site_id='".$site_id."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    $definitions = array();
	    
	    // echo $sql;
	    
	    foreach($result as $r){
	        $d = new SmartestPlaceholderDefinition;
	        $d->hydrateFromGiantArray($r);
	        $definitions[] = $d;
	    }
	    
	    return $definitions;
	    
	}

}