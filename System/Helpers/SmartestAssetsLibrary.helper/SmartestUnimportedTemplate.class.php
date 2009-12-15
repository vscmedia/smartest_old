<?php

class SmartestUnimportedTemplate implements ArrayAccess{
    
    protected $_file;
    protected $_probable_asset_type;
    protected $_probable_template_type;
    protected $_sites = array();
    protected $database;
    
    public function __construct($filename){
        
        $this->_file = new SmartestFile;
        $this->_file->loadFile($filename);
        $this->database = SmartestDatabase::getInstance('SMARTEST');
        $this->calculateProbableTypes();
        
    }
    
    private function calculateProbableTypes(){
        
        $h = new SmartestTemplatesLibraryHelper;
        $types = $h->getTypes();
        $root_dir_length = strlen(SM_ROOT_DIR);
        
        foreach($types as $t){
            $test_location = $t['storage']['location'];
            $actual_location = SmartestFileSystemHelper::dirName(substr($this->_file->getPath(), $root_dir_length));
            if($test_location == $actual_location){
                $this->_probable_asset_type = $t['id'];
                if(isset($t['template_type'])){
                    $this->_probable_template_type = $t['template_type'];
                }
            }
        }
        
    }
    
    public function getSitesWhereUsed($type='guess'){
        
        if($type=='guess'){
            $type = $this->getProbableAssetType();
        }
        
        if(!count($this->_sites[$type])){
        
            $base_name = SmartestFileSystemHelper::baseName($this->_file->getPath());
            $this->_sites[$type] = array();
        
            switch($type){
                case "SM_ASSETTYPE_MASTER_TEMPLATE":
                    $sql = "SELECT DISTINCT Sites.*, page_site_id FROM Pages, Sites WHERE (page_draft_template='".$base_name."' OR page_live_template='".$base_name."') AND Pages.page_site_id=Sites.site_id";
                    $result = $this->database->queryToArray($sql);
                    break;
                case "SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE":
                    $sql = "SELECT DISTINCT Sites.*, page_site_id, list_page_id FROM Pages, Sites, Lists WHERE (list_draft_template_file='".$base_name."' OR list_live_template_file='".$base_name."') AND Lists.list_type='SM_LIST_SIMPLE' AND Lists.list_page_id=Pages.page_site_id AND Pages.page_site_id=Sites.site_id";
                    $result = $this->database->queryToArray($sql);
                    break;
                case "SM_ASSETTYPE_ART_LIST_TEMPLATE":
                    $sql = "SELECT DISTINCT Sites.*, page_site_id, list_page_id FROM Pages, Sites, Lists WHERE ((list_draft_template_file='".$base_name."' OR list_live_template_file='".$base_name."') OR (list_draft_header_template='".$base_name."' OR list_live_header_template='".$base_name."') OR (list_draft_footer_template='".$base_name."' OR list_live_footer_template='".$base_name."')) AND Lists.list_type='SM_LIST_ARTICULAED' AND Lists.list_page_id=Pages.page_site_id AND Pages.page_site_id=Sites.site_id";
                    $result = $this->database->queryToArray($sql);
                    break;
                case "SM_ASSETTYPE_CONTAINER_TEMPLATE":
                    // Unimported container templates cannot be used, and this has not been possible in any prior versions
                    return array();
            }
        
            if(is_array($result)){
                foreach($result as $rs){
                    $s = new SmartestSite;
                    $s->hydrate($rs);
                    $this->_sites[$type][] = $s;
                }
            }
        
        }
        
        return $this->_sites[$type];
        
    }
    
    public function getSiteIdsWhereUsed($type='guess'){
        
        $sites = $this->getSitesWhereUsed($type);
        $ids = array();
        
        foreach($sites as $site){
            $ids[$site->getId()] = 1;
        }
        
        return array_keys($ids);
        
    }
    
    public function isInUseOnMultipleSites($type='guess'){
        
        return (count($this->getSitesWhereUsed($type)) > 1);
        
    }
    
    public function getProbableTemplateType(){
        return $this->_probable_template_type;
    }
    
    public function getProbableAssetType(){
        return $this->_probable_asset_type;
    }
    
    public function getStorageLocation(){
        return SmartestFileSystemHelper::dirName($this->_file->getSmartestPath());
    }
    
    public function getContent(){
        return $this->_file->getContent();
    }
    
    public function getContentForEditor(){
	    return htmlentities($this->getContent(), ENT_COMPAT, 'UTF-8');
	}
	
	public function setContent($content){
	    return $this->_file->setContent($content, true);
	}
	
	public function getUrl(){
	    return $this->_file->getFileName();
	}
    
    public function offsetGet($offset){
        switch($offset){
            case "url":
            return $this->getUrl();
            case "status":
            return 'unimported';
            case "php_class":
            return 'SmartestUnimportedTemplate';
            case "type":
            case "asset_type":
            return $this->_probable_asset_type;
            case "template_type":
            return $this->_probable_template_type;
            case "size":
            return $this->_file->getSize();
            case "raw_size":
            return $this->_file->getSize(false);
            case "file_path":
            return $this->_file->getSmartestPath();
            case "suggested_name":
            return SmartestStringHelper::removeDotSuffix($this->_file->getFileName());
            case "storage_location":
            return $this->getStorageLocation();
            case "force_shared":
            case "multiple_sites":
            return $this->isInUseOnMultipleSites();
        }
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}

}