<?php

class SmartestAssetsLibraryHelper{
    
    protected $database;
    protected $types;
    protected $typesSuffixesMap = array();
    
    public function __construct(){
        $this->database = SmartestPersistentObject::get('db:main');
    }
    
    public function getTypes(){
        if(!$this->types){
            $this->types = SmartestDataUtility::getAssetTypes();
        }
        
        return $this->types;
    }
    
    public function getUploadLocations(){
        
        $locations = array();
        $location_types = array();
        
        foreach($this->getTypes() as $key => $t){
            if($t['storage']['type'] == 'file'){
                if(!in_array($t['storage']['location'], $locations)){
                    $locations[] = $t['storage']['location'];
                }
            }
        }
        
        return $locations;
        
    }
    
    public function getTypeCodesByStorageLocation(){
        
        $asset_types = SmartestDataUtility::getAssetTypes();
        $locations = array();
        $location_types = array();
        
        // get the folders where uploads are made to, and match those to types
        foreach($asset_types as $key => $t){
            if($t['storage']['type'] == 'file'){
                $location_types[$t['storage']['location']][] = $key;
            }
        }
        
        return $location_types;
        
    }
    
    public function getTypeInfoBySuffix($suffix){
        
        $suffix = strtolower($suffix);
        
        if(array_key_exists($suffix, $this->typesSuffixesMap)){
            return $this->typesSuffixesMap[$suffix];
        }else{
        
            foreach($this->getTypes() as $code => $type){
                
                foreach($type['suffix'] as $s){
                    if(strtolower($s['_content']) == $suffix){
                        $this->typesSuffixesMap[$suffix] = $type;
                        return $type;
                    }
                }
            }
        }
    }
    
    public function getTypeCodeBySuffix($suffix){
        
        $suffix = strtolower($suffix);
        
        if(array_key_exists($suffix, $this->typesSuffixesMap)){
            return $this->typesSuffixesMap[$suffix]['id'];
        }else{
        
            foreach($this->getTypes() as $code => $type){
                
                foreach($type['suffix'] as $s){
                    if(strtolower($s['_content']) == $suffix){
                        $this->typesSuffixesMap[$suffix] = $type;
                        return $type['id'];
                    }
                }
                
            }
        
        }
        
    }
    
    public function getParsableAssetTypeCodes(){
	    
	    $processed_xml_data = SmartestDataUtility::getAssetTypes();
	    $codes = array();
	    
	    foreach($processed_xml_data as $code=>$type){
	        if(isset($type['parsable']) && SmartestStringHelper::toRealBool($type['parsable'])){
	            $codes[] = $code;
	        }
	    }
	    
	    return $codes;
	    
	}
    
    public function getAttachableAssetTypeCodes(){
	    
	    $processed_xml_data = SmartestDataUtility::getAssetTypes();
	    $codes = array();
	    
	    foreach($processed_xml_data as $code=>$type){
	        if(isset($type['attachable']) && SmartestStringHelper::toRealBool($type['attachable'])){
	            $codes[] = $code;
	        }
	    }
	    
	    return $codes;
	    
	}
	
	public function getAttachableFiles($site_id=''){
	    
	    $attachable_type_codes = $this->getAttachableAssetTypeCodes();
	    $sql = "SELECT * FROM Assets WHERE asset_deleted!='1'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared='1')";
	    }
	    
	    $sql .= " AND asset_type IN ('".implode("', '", $attachable_type_codes)."') ORDER BY asset_stringid";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $assets = array();
	    
	    foreach($result as $a){
	        $asset = new SmartestAsset;
	        $asset->hydrate($a);
	        $assets[] = $asset;
	    }
	    
	    return $assets;
	    
	}
	
	public function getAttachableFilesAsArrays($site_id=''){
	    
	    $assets = $this->getAttachableFiles($site_id);
	    
	    $arrays = array();
	    
	    foreach($assets as $a){
	        $arrays[] = $a->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getAssets($site_id='', $mode=1, $avoid_ids='', $ignore_templates=false){
		
		$sql = "SELECT * FROM Assets WHERE asset_deleted != 1";
		
		if($mode == 1){
	        $sql .= " AND asset_is_archived=0";
	    }else if($mode == 2){
	        $sql .= " AND asset_is_archived=1";
	    }
	    
	    if(is_array($avoid_ids)){
	        $sql .= " AND asset_id NOT IN ('".implode("', '", $avoid_ids)."')";
	    }
	    
	    if($ignore_templates){
	        $sql .= " AND asset_type != 'SM_ASSETTYPE_CONTAINER_TEMPLATE'";
	    }
	    
	    if(is_numeric($site_id)){
		    $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1) ORDER BY asset_stringid";
		}
		
		$result = $this->database->queryToArray($sql);
		$assets = array();
		
		foreach($result as $r){
		    $a = new SmartestAsset;
		    $a->hydrate($r);
		    $assets[] = $a;
		}
		
		return $assets;
	}
	
	public function getAssetsByTypeCode($code, $site_id='', $mode=1, $avoid_ids=''){
		
		if(is_array($code)){
		    $sql = "SELECT * FROM Assets WHERE asset_type IN ('".implode("', '", $code)."') AND asset_deleted != 1";
	    }else{
		    $sql = "SELECT * FROM Assets WHERE asset_type='".$code."' AND asset_deleted != 1";
	    }
	    
	    if($mode == 1){
	        $sql .= " AND asset_is_archived=0";
	    }else if($mode == 2){
	        $sql .= " AND asset_is_archived=1";
	    }
	    
	    if(is_array($avoid_ids)){
	        $sql .= " AND asset_id NOT IN ('".implode("', '", $avoid_ids)."')";
	    }
	    
	    if(is_numeric($site_id)){
		    $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1) ORDER BY asset_stringid";
		}
		
		$result = $this->database->queryToArray($sql);
		$assets = array();
		
		foreach($result as $r){
		    $a = new SmartestAsset;
		    $a->hydrate($r);
		    $assets[] = $a;
		}
		
		return $assets;
	}
	
	public function getAssetsByTypeCodeAsArrays($code, $site_id=''){
	    
	    $assets = $this->getAssetsByTypeCode($code, $site_id);
	    $arrays = array();
	    
	    foreach($assets as $a){
	        $arrays[] = $a->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function getAssetClassOptions($code, $site_id='', $mode=1, $avoid_ids=''){
	    
	    $asset_classes = SmartestDataUtility::getAssetClassTypes();
	    
	    if(isset($asset_classes[$code])){
	        
	        $asset_types = $asset_classes[$code]['accept'];
	        return $this->getAssetsByTypeCode($asset_types, $site_id, $mode, $avoid_ids);
	        
	    }
	    
	}
	
	public function getAssetGroups($site_id=''){
	    
	    $sql = "SELECT * FROM Sets WHERE set_type='SM_SET_ASSETGROUP'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND set_site_id='".$site_id."'";
	    }
	    
	    $sql .= " ORDER BY set_name";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $groups = array();
	    
	    foreach($result as $r){
	        $g = new SmartestAssetGroup;
	        $g->hydrate($r);
	        $groups[] = $g;
	    }
	    
	    return $groups;
	    
	}
	
	public function getAssetClassesThatAcceptType(){
	    
	    $types = func_get_args();
	    
	    if(count($types)){
	        
	        if(is_array($types[0])){
	            $types = $types[0];
	        }
	        
	        $codes = $this->getAssetClassCodesThatAcceptType($types);
	        
	        $ok_asset_classes = array();
	        
	        $asset_classes = SmartestDataUtility::getAssetClassTypes();
	        
	        foreach($asset_classes as $ac){
	            if(in_array($ac['id'], $codes)){
	                $ok_asset_classes[] = $ac;
	            }
	        }
	        
	        return $ok_asset_classes;
	        
        }
	    
	}
	
	public function getAssetClassCodesThatAcceptType(){
	    
	    $types = func_get_args();
	    
	    if(count($types)){
	        
	        if(is_array($types[0])){
	            $types = $types[0];
	        }
	        
	        $asset_classes = SmartestDataUtility::getAssetClassTypes();
	        $ok_asset_class_codes = array();
	        
	        $i = 0;
	        
	        foreach($types as $t){
	            
	            $ok_asset_class_codes_this_assettype = array();
	            
	            foreach($asset_classes as $ac){
	            
	                if(in_array($t, $ac['accept'])){
	                    $ok_asset_class_codes_this_assettype[] = $ac['id'];
	                }
	            
	            }
	            
	            if($i == 0){
	                $ok_asset_class_codes = $ok_asset_class_codes_this_assettype;
	            }else{
	                $ok_asset_class_codes = array_intersect($ok_asset_class_codes, $ok_asset_class_codes_this_assettype);
	            }
	            
	            $i++;
	        
            }
	        
	        return $ok_asset_class_codes;
	        
        }
	    
	}
	
	public function getAssetGroupsThatAcceptType($types, $site_id=''){
	    
	    if(!is_array($types)){
	        $types = array($types);
	    }
	    
	    if(count($types)){
	        
	        $sql = "SELECT * FROM Sets WHERE set_type='SM_SET_ASSETGROUP'";
	        
	        if(is_numeric($site_id)){
    	        $sql .= " AND set_site_id='".$site_id."'";
    	    }
    	    
    	    if(count($types) > 1){
	            
	            // more than one type is being supplied
	            // find only groups that accept ALL of the given types
	            $ok_assetclass_types = $this->getAssetClassCodesThatAcceptType($types);
	            $sql .=  " AND (set_filter_type='SM_SET_FILTERTYPE_NONE' OR set_filter_value IN ('".implode($ok_assetclass_types, "', '")."'))";
	            
	        }else{
	            
	            // just one type
	            $ok_assetclass_types = $this->getAssetClassCodesThatAcceptType($types);
	            $sql .=  " AND (set_filter_type='SM_SET_FILTERTYPE_NONE' OR (set_filter_type='SM_SET_FILTERTYPE_ASSETTYPE' AND set_filter_value='".$types[0]."') OR set_filter_value IN ('".implode($ok_assetclass_types, "', '")."'))";
	            
	        }
	        
	        $result = $this->database->queryToArray($sql);
	        $groups = array();
	        
	        foreach($result as $r){
	            $g = new SmartestAssetGroup;
	            $g->hydrate($r);
	            $groups[] = $g;
	        }
	        
	        return $groups;
	        
        }
	    
	}
	
	public function getTypeSpecificAssetGroupsByType($asset_type, $site_id=''){
	    
	    $sql = "SELECT * FROM Sets WHERE set_type='SM_SET_ASSETGROUP'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND set_site_id='".$site_id."'";
	    }
	    
	    $sql .= " AND set_filter_type='SM_SET_FILTERTYPE_ASSETTYPE' AND set_filter_value='".$asset_type."'";
	    
	    $sql .= " ORDER BY set_name";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $groups = array();
	    
	    foreach($result as $r){
	        $g = new SmartestAssetGroup;
	        $g->hydrate($r);
	        $groups[] = $g;
	    }
	    
	    return $groups;
	    
	}
	
	public function getPlaceholderAssetGroups($site_id=''){
	    
	    $sql = "SELECT * FROM Sets WHERE set_type='SM_SET_ASSETGROUP'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND set_site_id='".$site_id."'";
	    }
	    
	    $sql .= " AND set_filter_type='SM_SET_FILTERTYPE_ASSETCLASS'";
	    
	    $sql .= " ORDER BY set_name";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $groups = array();
	    
	    foreach($result as $r){
	        $g = new SmartestAssetGroup;
	        $g->hydrate($r);
	        $groups[] = $g;
	    }
	    
	    return $groups;
	    
	}
	
	public function getPlaceholderAssetGroupsByType($placeholder_type, $site_id=''){
	    
	    $sql = "SELECT * FROM Sets WHERE set_type='SM_SET_ASSETGROUP'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND set_site_id='".$site_id."'";
	    }
	    
	    $sql .= " AND set_filter_type='SM_SET_FILTERTYPE_ASSETCLASS' AND set_filter_value='".$placeholder_type."'";
	    
	    $sql .= " ORDER BY set_name";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $groups = array();
	    
	    foreach($result as $r){
	        $g = new SmartestAssetGroup;
	        $g->hydrate($r);
	        $groups[] = $g;
	    }
	    
	    return $groups;
	    
	}
    
}