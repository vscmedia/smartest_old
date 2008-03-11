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
    
}