<?php

class SmartestAssetsLibraryHelper{
    
    protected $database;
    protected $types;
    protected $categories;
    protected $typesSuffixesMap = array();
    
    const ASSET_TYPE_UNKNOWN = -1024;
    const MISSING_DATA = -512;
    
    public function __construct(){
        $this->database = SmartestPersistentObject::get('db:main');
    }
    
    public function getTypes(){
        
        if(!$this->types){
            $this->types = SmartestDataUtility::getAssetTypes();
        }
        
        return $this->types;
    }
    
    public function getSelectedTypes($type_codes=''){
        
        $selected_types = array();
        
        if(!is_array($type_codes)){
            $type_codes = array();
        }
        
        foreach($this->getTypes() as $t){
            if(in_array($t['id'], $type_codes)){
                $selected_types[] = $t;
            }
        }
        
        return $selected_types;
        
    }
    
    public function getCategories($importable_only=false){
        
        if(!$this->categories){
            $cats_data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/assettypecategories.yml');
    		$this->categories = $cats_data['categories'];
        }
        
        $cats = $this->categories;
        
        if($importable_only){
            foreach($cats as $k=>$cat){
                if(!$cat['importable']){
                    unset($cats[$k]);
                }
            }
        }
        
        return $cats;
    }
    
    public function getCategoryShortNames($importable_only=false){
        
        $names = array();
        
        foreach($this->getCategories($importable_only) as $cat){
            $names[] = $cat['short_name'];
        }
        
        return $names;
    }
    
    public function getTypesByCategory($exclude_categories=''){
		
		/* $types = array(
		    "user_text" => array(),
		    "image" => array(),
		    "browser_instructions" => array(),
		    "embedded" => array(),
		    "other" => array()
		); */
		
		
		
		$types = $this->getCategories();
		
		if(!is_array($exclude_categories)){
		    $exclude_categories = array();
		}
		
		foreach($exclude_categories as $ec){
		    if(isset($types[$ec])){
		        unset($types[$ec]);
		    }
		}
		
		//  && !in_array($type_array['category'], $exclude_categories)
		
		$processed_xml_data = SmartestDataUtility::getAssetTypes();
		
		if(is_array($processed_xml_data)){
		    foreach($processed_xml_data as $type_array){
		        if(isset($types[$type_array['category']])){
		            $cat_array =& $types[$type_array['category']]['types'];
		            $cat_array[] = $type_array;
		        }
		    }
	    }
	    
	    return $types;
		
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
    
    public function getTypeCodesByStorageLocation($exclude_categories=''){
        
        if(!is_array($exclude_categories)){
            $exclude_categories = array();
        }
        
        $asset_types = SmartestDataUtility::getAssetTypes();
        $locations = array();
        $location_types = array();
        
        // get the folders where uploads are made to, and match those to types
        foreach($asset_types as $key => $t){
            if($t['storage']['type'] == 'file' && !in_array($t['category'], $exclude_categories)){
                $location_types[$t['storage']['location']][] = $key;
            }
        }
        
        return $location_types;
        
    }
    
    public function getStorageLocationByTypeCode($type_code){
        
        $asset_types = SmartestDataUtility::getAssetTypes();
        
        if(isset($asset_types[$type_code])){
            $type = $asset_types[$type_code];
            if(isset($type['storage']['location'])){
                return $type['storage']['location'];
            }else{
                return self::MISSING_DATA;
            }
        }else{
            return self::ASSET_TYPE_UNKNOWN;
        }
        
    }
    
    public function getNonImportableCategoryNames(){
        
        $cats_data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/assettypecategories.yml');
		$cats = $cats_data['categories'];
		$names = array();
		
		foreach($cats as $c){
		    if(!$c['importable']){
		        $names[] = $c['short_name'];
		    }
		}
		
		return $names;
        
    }
    
    public function getAllTypesBySuffix(){
        
        $asset_types = SmartestDataUtility::getAssetTypes();
        $suffixes = array();
        
        foreach($asset_types as $t){
            if(isset($t['suffix']) && is_array($t['suffix'])){
                
                foreach($t['suffix'] as $s){
                    
                    if($t['storage']['type'] == 'file'){
                        
                        $safe_suffix = SmartestStringHelper::toVarName($s['_content']);
                        
                        if(!isset($suffixes[$safe_suffix])){
                            $suffixes[$safe_suffix] = array();
                        }
                        
                        $suffix = $s;
                        $suffix['type'] = $t;
                        $suffix['storage_location'] = $t['storage']['location'];
                        
                        $suffixes[$s['_content']][] = $suffix;
                        
                    }
                }
            }
        }
        
        return $suffixes;
        
    }
    
    public function getPossibleTypesBySuffix($s){
        
        $suffixes = $this->getAllTypesBySuffix();
        $suffix = strtolower($s);
        
        if(isset($suffixes[$suffix])){
            return $suffixes[$suffix];
        }else{
            return array();
        }
        
    }
    
    public function getAcceptableNameOptionsForUnknownSuffix($filename, $location=''){
        
        if(!strlen($location)){
            $location = false;
        }
        
        $root_name = SmartestStringHelper::removeDotSuffix($filename);
        $types = $this->getImportableFileTypes();
        $options = array();
        
        foreach($types as $t){
            if(isset($t['suffix']) && isset($t['suffix'][0]['_content'])){
                if(!$location || $location == $t['storage']['location']){
                    $suffix = $t['suffix'][0]['_content'];
                    $option = array();
                    $option['filename'] = $root_name.'.'.$suffix;
                    $option['type'] = $t;
                    $option['storage_location'] = $t['storage']['location'];
                    $options[] = $option;
                }
            }
        }
        
        return $options;
        
    }
    
    public function getImportableFileTypes(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/assettypecategories.yml');
        $categories = $data['categories'];
        $importable_category_short_names = array();
        
        foreach($categories as $c){
            if(isset($c['importable']) && $c['importable']){
                $importable_category_short_names[] = $c['short_name'];
            }
        }
        
        $asset_types = SmartestDataUtility::getAssetTypes();
        
        foreach($asset_types as $id=>$type){
            if($type['storage']['type'] != 'file' || !in_array($type['category'], $importable_category_short_names)){
                unset($asset_types[$id]);
            }
        }
        
        return $asset_types;
        
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
	
	public function getClassNamesByTypeCode(){
	    
	    $types = $this->getTypes();
	    $classes = array();
	    
	    foreach($types as $t){
	        $classes[$t['id']] = $t['class'];
	    }
	    
	    return $classes;
	    
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
		
		$classes = $this->getClassNamesByTypeCode();
		
		foreach($result as $r){
		    
		    if(class_exists($classes[$r['asset_type']])){
		        $c = $classes[$r['asset_type']];
		        $a = new $c;
		    }else{
		        $a = new SmartestAsset;
		    }
		    
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