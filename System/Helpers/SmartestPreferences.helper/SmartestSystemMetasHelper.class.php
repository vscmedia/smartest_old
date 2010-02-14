<?php

class SmartestSystemMetasHelper{

    protected $database;
    
    public function __construct(){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
    }
    
    public function fetchSystemMetaInfo($meta_name){
        
    }
    
    public function getSystemMeta($meta_name){
        
    }
    
    public function setSystemMeta($meta_name, $meta_value){
        
    }
    
    public function retrieveAll(){
        
    }

}