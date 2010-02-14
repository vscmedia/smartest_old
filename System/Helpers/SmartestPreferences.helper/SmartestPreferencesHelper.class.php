<?php

class SmartestPreferencesHelper{
    
    protected $database;
    
    public function __construct(){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
    }
    
    protected function fetchApplicationInfo($application_id){
        
    }
    
    public function getApplicationPreference($name, $application_id, $user_id, $site_id){
        
    }
    
    public function setApplicationPreference($name, $value, $application_id, $user_id, $site_id){
        
    }
    
    public function getGlobalPreference($name, $user_id, $site_id){
        
    }
    
    public function setGlobalPreference($name, $value, $user_id, $site_id){
        
    }

}