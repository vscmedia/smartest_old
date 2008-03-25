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
        // print_r($asset_types);
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
                // print_r($type_info);
                // if(){
                foreach($type['suffix'] as $s){
                    if(strtolower($s['_content']) == $suffix){
                        $this->typesSuffixesMap[$suffix] = $type;
                        return $type;
                    }
                }    
                //}
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
	
	public function getAssetsByTypeCode($code, $site_id=''){
		
		$sql = "SELECT * FROM Assets WHERE asset_type='$code' AND asset_deleted != 1";
		
		if(is_numeric($site_id)){
		    $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1) ORDER BY asset_stringid";
		}
		
		$assets = $this->database->queryToArray($sql);
		
		return $assets;
	}
    
}