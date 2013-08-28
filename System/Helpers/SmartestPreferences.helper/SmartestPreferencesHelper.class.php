<?php

class SmartestPreferencesHelper extends SmartestHelper{
    
    protected $database;
    protected $_application_info = array();
    protected $_global_prefs = array();
    protected $_application_prefs = array();
    
    public function __construct(){
        
        $this->database = SmartestDatabase::getInstance('SMARTEST');
        
    }
    
    protected function fetchApplicationInfo($application_id){
        
        $modules = SmartestPersistentObject::get('controller')->getAllModulesById();
        
        if(isset($modules[$application_id])){
            return $modules[$application_id];
        }else{
            throw new SmartestException("Unknown application ID given: ".$application_id);
        }
        
    }
    
    protected function fetchApplicationPreferenceInfo($name, $application_id){
        
        $preference = new SmartestPreference;
        
        $info = $this->fetchApplicationInfo($application_id);
        $file = $info['directory'].'Configuration/preferences.yml';
        $ct = SmartestYamlHelper::fastLoad($file);
        $prefs = $ct['prefs'];
        
        if(isset($prefs[$name])){
            $preference->hydrate($prefs[$name]);
            $preference->setApplicationId($application_id);
            $preference->setName($name);
        }
        
        return $preference;
        
    }
    
    protected function fetchGlobalPreferenceInfo($name){
        
        $preference = new SmartestPreference;
        $prefs = SmartestPffrHelper::getContentsFast(SM_ROOT_DIR.'System/Core/Types/globalprefs.pff');
        
        if(isset($prefs[$name])){
            $preference->hydrate($prefs[$name]);
            $preference->setApplicationId('_GLOBAL');
            $preference->setName($name);
        }else{
            throw new SmartestException("Tried to set or retrieve an unknown global preference: '".$name."'");
        }
        
        return $preference;
        
    }
    
    public function applicationPreferenceIsSet($name, $application_id, $user_id=0, $site_id=0){
        
        return $this->getApplicationPreference($name, $application_id, $user_id, $site_id, true);
        
    }
    
    public function getApplicationPreference($name, $application_id, $user_id=0, $site_id=0, $existence_check_only=false){
        
        if(!isset($this->_application_prefs[$name])){
            $this->_application_prefs[$name] = $this->fetchApplicationPreferenceInfo($name, $application_id);
        }
        
        $sql = "SELECT setting_value FROM Settings WHERE setting_name='".$name."' AND setting_application_id='".$application_id."' AND setting_type='SM_SETTINGTYPE_APPLICATION_PREFERENCE'";
        
        if($this->_application_prefs[$name]->isUserSpecific()){
            $sql .= " AND setting_user_id='".$user_id."'";
        }
        
        if($this->_application_prefs[$name]->isSiteSpecific()){
            $sql .= " AND setting_site_id='".$site_id."'";
        }
        
        $result = $this->database->queryToArray($sql);
        
        if($existence_check_only){
            return (bool) count($result);
        }else{
            return isset($result[0]) ? stripslashes($result[0]['setting_value']) : null;
        }
        
    }
    
    public function setApplicationPreference($name, $value, $application_id, $user_id, $site_id){
        
        if($application_id == '_GLOBAL'){
            SmartestLog::getInstance('system')->log("SmartestPreferencesHelper::setApplicationPreference() used with '_GLOBAL' as application ID to try to set global preference.");
            return $this->setGlobalPreference($name, $value, $user_id, $site_id);
        }
        
        if(!isset($this->_application_prefs[$name])){
            try{
                $this->_application_prefs[$name] = $this->fetchApplicationPreferenceInfo($name, $application_id);
            }catch(SmartestException $e){
                
            }
        }
        
        if($this->applicationPreferenceIsSet($name, $application_id, $user_id, $site_id)){
            $sql = "UPDATE Settings SET setting_value='".addslashes($value)."' WHERE setting_application_id='".$application_id."' AND setting_name='".$name."' AND setting_type='SM_SETTINGTYPE_APPLICATION_PREFERENCE'";
            
            if($this->_application_prefs[$name]->isUserSpecific()){
                $sql .= " AND setting_user_id='".$user_id."'";
            }
            
            if($this->_application_prefs[$name]->isSiteSpecific()){
                $sql .= " AND setting_site_id='".$site_id."'";
            }
            
        }else{
            $sql = "INSERT INTO Settings (setting_name, setting_value, setting_type, setting_application_id";
            
            if($this->_application_prefs[$name]->isUserSpecific()){
                $sql .= ", setting_user_id";
            }
            
            if($this->_application_prefs[$name]->isSiteSpecific()){
                $sql .= ", setting_site_id";
            }
            
            $sql .= ") VALUES ('".$name."', '".addslashes($value)."', 'SM_SETTINGTYPE_APPLICATION_PREFERENCE', '".$application_id."'";
            
            if($this->_application_prefs[$name]->isUserSpecific()){
                $sql .= ", '".$user_id."'";
            }
            
            if($this->_application_prefs[$name]->isSiteSpecific()){
                $sql .= ", '".$site_id."'";
            }
            
            $sql .= ")";
        }
        
        $this->database->rawQuery($sql);
        
    }
    
    public function globalPreferenceIsSet($name, $user_id=0, $site_id=0){
        
        return $this->getGlobalPreference($name, $user_id, $site_id, true);
        
    }
    
    public function getGlobalPreference($name, $user_id=0, $site_id=0, $existence_check_only=false){
        
        if(!isset($this->_global_prefs[$name])){
            $this->_global_prefs[$name] = $this->fetchGlobalPreferenceInfo($name);
        }
        
        $sql = "SELECT setting_value FROM Settings WHERE setting_name='".$name."' AND (setting_application_id='_GLOBAL' OR setting_application_id='com.smartest.global') AND setting_type='SM_SETTINGTYPE_GLOBAL_PREFERENCE'";
        
        if($this->_global_prefs[$name]->isUserSpecific()){
            $sql .= " AND setting_user_id='".$user_id."'";
        }
        
        if($this->_global_prefs[$name]->isSiteSpecific()){
            $sql .= " AND setting_site_id='".$site_id."'";
        }
        
        $result = $this->database->queryToArray($sql);
        
        if($existence_check_only){
            return (bool) count($result);
        }else{
            return isset($result[0]) ? stripslashes($result[0]['setting_value']) : null;
        }
        
    }
    
    public function setGlobalPreference($name, $value, $user_id, $site_id){
        
        if(!isset($this->_global_prefs[$name])){
            try{
                $this->_global_prefs[$name] = $this->fetchGlobalPreferenceInfo($name);
            }catch(SmartestException $e){
                
            }
        }
        
        if($this->globalPreferenceIsSet($name, $user_id, $site_id)){
            $sql = "UPDATE Settings SET setting_value='".addslashes($value)."' WHERE setting_application_id='_GLOBAL' AND setting_name='".$name."' AND setting_type='SM_SETTINGTYPE_GLOBAL_PREFERENCE'";
            
            if($this->_global_prefs[$name]->isUserSpecific()){
                $sql .= " AND setting_user_id='".$user_id."'";
            }
            
            if($this->_global_prefs[$name]->isSiteSpecific()){
                $sql .= " AND setting_site_id='".$site_id."'";
            }
        }else{
            $sql = "INSERT INTO Settings (setting_name, setting_value, setting_type, setting_application_id";
            
            if($this->_global_prefs[$name]->isUserSpecific()){
                $sql .= ", setting_user_id";
            }
            
            if($this->_global_prefs[$name]->isSiteSpecific()){
                $sql .= ", setting_site_id";
            }
            
            $sql .= ") VALUES ('".$name."', '".addslashes($value)."', 'SM_SETTINGTYPE_GLOBAL_PREFERENCE', '_GLOBAL'";
            
            if($this->_global_prefs[$name]->isUserSpecific()){
                $sql .= ", '".$user_id."'";
            }
            
            if($this->_global_prefs[$name]->isSiteSpecific()){
                $sql .= ", '".$site_id."'";
            }
            
            $sql .= ")";
        }
        
        $this->database->rawQuery($sql);
        
    }

}