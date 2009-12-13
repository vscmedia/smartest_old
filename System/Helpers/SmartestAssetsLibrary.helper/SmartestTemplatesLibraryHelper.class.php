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
		
        $all_templates = SmartestFileSystemHelper::getDirectoryContents($path, false, SM_DIR_SCAN_FILES);
		$templates = array();
		
		// Templates already imported into the repository but for other sites can be ignored
		$sql = "SELECT * FROM Assets WHERE asset_site_id !='".$site_id."' AND asset_type='SM_ASSETTYPE_MASTER_TEMPLATE'";
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
    
    public function getTypes(){
        
        if(!count($this->types)){
        
            $all_category_names = $this->_helper->getCategoryShortNames();
            $k = array_search('templates', $all_category_names);
            unset($all_category_names[$k]);
            $cats = $this->_helper->getTypesByCategory($all_category_names);
        
            $rt = $cats['templates']['types'];
            
            foreach($rt as $t){
                $this->types[$t['id']] = $t;
            }
        
        }
        
        return $this->types;
        
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

}