<?php

class SmartestTemplatesLibraryHelper{
    
    protected $database;
    protected $_helper;
    protected $types = array();
    
    public function __construct(){
        $this->database = SmartestDatabase::getInstance('SMARTEST');
        $this->_helper = new SmartestAssetsLibraryHelper;
    }
    
    public function getMasterTemplates($site_id){
        
        $path = SM_ROOT_DIR.'Presentation/Masters/';
        $site_id = (int) $site_id;
		
        $all_templates = SmartestFileSystemHelper::getDirectoryContents($path, false, SM_DIR_SCAN_FILES);
		$templates = array();
		
		// Templates already imported into the repository but for other sites can be ignored
		$sql = "SELECT * FROM Assets WHERE asset_site_id !='".$site_id."' AND asset_type='SM_ASSETTYPE_MASTER_TEMPLATE' AND asset_shared!='1'";
		$result = $this->database->queryToArray($sql);
		
		foreach($result as $foreign_template_asset){
		    if($foreign_template_asset['asset_shared'] != '1'){
		        $key = array_search($foreign_template_asset['asset_url'], $all_templates);
		        unset($all_templates[$key]);
	        }
		}
		
		// Templates already imported into the repository for this site should have proper SmartestTemplateAsset objects
		$sql = "SELECT * FROM Assets WHERE (asset_site_id='".$site_id."' OR asset_shared='1') AND asset_type='SM_ASSETTYPE_MASTER_TEMPLATE' AND asset_deleted=0";
		$result = $this->database->queryToArray($sql);
		$db_templates = array();
		$db_template_names = array();
		
		foreach($result as $imported_template_record){
		    $t = new SmartestTemplateAsset;
		    $t->hydrate($imported_template_record);
		    $db_templates[] = $t;
		    $db_template_names[] = $t->getUrl();
		}
		
		foreach($all_templates as $template_on_disk){
		    
		    if(in_array($template_on_disk, $db_template_names)){
		        $k = array_search($template_on_disk, $db_template_names);
		        $templates[] = $db_templates[$k];
		    }else{
		        $templates[] = new SmartestUnimportedTemplate(SM_ROOT_DIR.'Presentation/Masters/'.$template_on_disk);
		    }
		}
		
		return $templates;
        
    }
    
    public function getSharedMasterTemplates(){
        
        $path = SM_ROOT_DIR.'Presentation/Masters/';
		
        $all_templates = SmartestFileSystemHelper::getDirectoryContents($path, false, SM_DIR_SCAN_FILES);
		$templates = array();
		
		// Templates already imported into the repository but for other sites can be ignored
		$sql = "SELECT * FROM Assets WHERE asset_type='SM_ASSETTYPE_MASTER_TEMPLATE' AND asset_shared!='1'";
		$result = $this->database->queryToArray($sql);
		
		foreach($result as $foreign_template_asset){
		    if($foreign_template_asset['asset_shared'] != '1'){
		        $key = array_search($foreign_template_asset['asset_url'], $all_templates);
		        unset($all_templates[$key]);
	        }
		}
		
		// Templates already imported into the repository for this site should have proper SmartestTemplateAsset objects
		$sql = "SELECT * FROM Assets WHERE asset_shared='1' AND asset_type='SM_ASSETTYPE_MASTER_TEMPLATE' AND asset_deleted=0";
		$result = $this->database->queryToArray($sql);
		$db_templates = array();
		$db_template_names = array();
		
		foreach($result as $imported_template_record){
		    $t = new SmartestTemplateAsset;
		    $t->hydrate($imported_template_record);
		    $db_templates[] = $t;
		    $db_template_names[] = $t->getUrl();
		}
		
		foreach($all_templates as $template_on_disk){
		    
		    if(in_array($template_on_disk, $db_template_names)){
		        $k = array_search($template_on_disk, $db_template_names);
		        $templates[] = $db_templates[$k];
		    }else{
		        $templates[] = new SmartestUnimportedTemplate(SM_ROOT_DIR.'Presentation/Masters/'.$template_on_disk);
		    }
		}
		
		return $templates;
        
    }
    
    public function getArticulatedListTemplates($site_id){
        
        // unimported template files in Presentation/ListItems/ become SmartestUnimportedTemplate objects
        // except that 
        //// 1) unimported template files in Presentation/ListItems/ that are in use as compound templates should be ignored
        //// 2) template files in Presentation/ListItems/ that have already been imported as compound templates should be ignored
        // and articulated list templates that have already been imported need to be included too
        
        $path = SM_ROOT_DIR.'Presentation/ListItems/';
		
        $all_templates = SmartestFileSystemHelper::getDirectoryContents($path, false, SM_DIR_SCAN_FILES);
		$templates = array();
		
		// Templates already imported into the repository but for other sites can be ignored
		$sql = "SELECT * FROM Assets WHERE asset_site_id !='".$site_id."' AND asset_type='SM_ASSETTYPE_ART_LIST_TEMPLATE' AND asset_shared!='1'";
		$result = $this->database->queryToArray($sql);
		
		foreach($result as $foreign_template_asset){
		    if($foreign_template_asset['asset_shared'] != '1'){
		        $key = array_search($foreign_template_asset['asset_url'], $all_templates);
		        unset($all_templates[$key]);
	        }
		}
		
		// Templates already imported into the repository for this site should have proper SmartestTemplateAsset objects
		$sql = "SELECT * FROM Assets WHERE (asset_site_id='".$site_id."' OR asset_shared='1') AND asset_type='SM_ASSETTYPE_ART_LIST_TEMPLATE' AND asset_deleted=0";
		$result = $this->database->queryToArray($sql);
		$db_templates = array();
		$db_template_names = array();
		
		foreach($result as $imported_template_record){
		    $t = new SmartestTemplateAsset;
		    $t->hydrate($imported_template_record);
		    $db_templates[] = $t;
		    $db_template_names[] = $t->getUrl();
		}
		
		foreach($all_templates as $template_on_disk){
		    
		    if(in_array($template_on_disk, $db_template_names)){
		        $k = array_search($template_on_disk, $db_template_names);
		        $templates[] = $db_templates[$k];
		    }else{
		        $templates[] = new SmartestUnimportedTemplate($path.$template_on_disk);
		    }
		}
		
		return $templates;
        
    }
    
    public function hydrateMasterTemplateByFileName($filename, $site_id=''){
        
        $sql = "SELECT * FROM Assets WHERE asset_url='".$filename."' AND asset_type='SM_ASSETTYPE_MASTER_TEMPLATE' AND asset_deleted='0'";
        
        if(is_numeric($site_id)){
            $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1)";
        }
        
        $sql .= " LIMIT 1";
        
        // echo $sql;
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $template = new SmartestTemplateAsset;
            $template->hydrate($result[0]);
        }else{
            $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.'Presentation/Masters/'.$filename);
        }
        
        return $template;
        
    }
    
    public function getTypes(){
        
        if(!count($this->types)){
        
            $all_category_names = $this->_helper->getCategoryShortNames();
            $k = array_search('templates', $all_category_names);
            unset($all_category_names[$k]);
            $cats = $this->_helper->getTypesByCategory($all_category_names);
        
            $rt = $cats['templates']['types'];
            
            foreach($rt as $t){
                $this->types[$t['id']] = $t;
                if(is_dir(SM_ROOT_DIR.$t['storage']['location'])){
                    $this->types[$t['id']]['storage']['writable'] = is_writable(SM_ROOT_DIR.$t['storage']['location']);
                }
            }
        
        }
        
        return $this->types;
        
    }
    
    public function getTypeCodes(){
        
        $codes = array();
        
        foreach($this->getTypes() as $t){
            $codes[] = $t['id'];
        }
        
        return $codes;
        
    }
    
    public function getStorageLocationByTypeCode($type_code){
        
        $asset_types = $this->getTypes();
        
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
    
    public function getUnWritableStorageLocations(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/system.yml');
        $locations = $data['system']['writable_locations']['templates_repo'];
        $problem_locations = array();
        
        foreach($locations as $l){
            if(!is_writable(SM_ROOT_DIR.$l)){
                $problem_locations[] = $l;
            }
        }
        
        return $problem_locations;
        
    }
    
    public function getMasterTemplateHasBeenImported($template_filename){
        
        if(is_file(SM_ROOT_DIR.'Presentation/Masters/'.$template_filename)){
            $sql = "SELECT asset_id FROM Assets WHERE asset_url='".$template_filename."' AND asset_type='SM_ASSETTYPE_MASTER_TEMPLATE' AND asset_deleted=0";
		    $result = $this->database->queryToArray($sql);
		    return (bool) count($result);
	    }else{
	        return false;
	    }
        
    }
    
    public function getTemplateIsImported($file_url, $type_code=null){
        
        $sql = "SELECT asset_id FROM Assets WHERE asset_url='".$file_url."'";
        
        if($type_code){
            $sql .= " AND asset_type='".$type_code."'";
        }else{
            $sql .= " AND asset_type IN ('".implode("', '", $this->getTypeCodes())."')";
        }
        
        $sql .= " AND asset_deleted=0";
        
        $result = $this->database->queryToArray($sql);
	    return (bool) count($result);
        
    }

}