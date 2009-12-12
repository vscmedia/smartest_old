<?php

class SmartestUnimportedTemplate implements ArrayAccess{
    
    protected $_file;
    protected $_probable_asset_type;
    protected $_probable_template_type;
    
    public function __construct($filename){
        
        $this->_file = new SmartestFile;
        $this->_file->loadFile($filename);
        
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
    
    public function getProbableTemplateType(){
        return $this->_probable_template_type;
    }
    
    public function getProbableAssetType(){
        return $this->_probable_asset_type;
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "url":
            return $this->_file->getFileName();
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
            return SmartestFileSystemHelper::dirName($this->_file->getSmartestPath());
        }
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}

}