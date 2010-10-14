<?php

class SmartestAssetClassesHelper{
    
    protected $database;
    protected $_all_types;
    
    public function __construct(){
        
        $this->database = SmartestDatabase::getInstance('SMARTEST');
        
    }
    
    public function getTypes($show_invisible=false){
        
        if(empty($this->_all_types)){
            $this->_all_types = SmartestDataUtility::getAssetClassTypes();
        }
        
        if($show_invisible){
            return $this->_all_types;
        }else{
            $types = $this->_all_types;
            foreach($types as $k => $t){
                if(isset($t['hide']) && SmartestStringHelper::toRealBool($t['hide'])){
                    unset($types[$k]);
                }
            }
            return $types;
        }
        
    }
    
    public function getTypeCodes($show_invisible=false){
        
        $types = $this->getTypes($show_invisible);
        $codes = array();
        
        foreach($types as $k => $t){
            $codes[] = $t['id'];
        }
        
        return $codes;
        
    }
    
    public function getAssetTypesFromAssetClassType($type){
	    
	    $ptypes = $this->getTypes();
	    
	    if(isset($ptypes[$type])){
	        $atypes = $ptypes[$type]['accept'];
	        if(is_array($atypes)){
	            $h = new SmartestAssetsLibraryHelper;
	            return $h->getSelectedTypes($atypes);
	        }else{
	            return array();
	        }
	    }
	    
	}
	
	public function getAssetTypeCodesFromAssetClassType($type){
	    
	    $ptypes = $this->getTypes();
	    
	    if(isset($ptypes[$type])){
	        $atypes = $ptypes[$type]['accept'];
	        if(is_array($atypes)){
	            return $atypes;
	        }else{
	            return array();
	        }
	    }
	    
	}
    
    public function getAssetGroupsForPlaceholderType($type, $site_id=''){
        
        $sql = "SELECT * FROM Sets WHERE set_type='SM_SET_ASSETGROUP'";
        
        if(is_numeric($site_id)){
	        $sql .= " AND set_site_id='".$site_id."'";
	    }
	    
	    $sql .= " AND ((set_filter_type='SM_SET_FILTERTYPE_ASSETCLASS' AND set_filter_value='".$type."') OR (set_filter_type='SM_SET_FILTERTYPE_ASSETTYPE' AND set_filter_value IN ('".implode("', '", $this->getAssetTypeCodesFromAssetClassType($type))."')))";
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