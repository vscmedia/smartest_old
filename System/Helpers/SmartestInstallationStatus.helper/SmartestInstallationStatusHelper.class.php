<?php

require SM_ROOT_DIR.'System/Base/Exceptions/SmartestNotInstalledException.class.php';

class SmartestInstallationStatusHelper{
    
    public static function checkStatus($purge=false){
        
        if((SmartestCache::load('installation_status', true) != SM_INSTALLSTATUS_COMPLETE) || $purge){
            
            if(file_exists(SM_ROOT_DIR.'Configuration/database.ini') && file_exists(SM_ROOT_DIR.'Configuration/controller.xml')){
                // Config files are in place, so try connecting to the database
                try{
                    $db = SmartestDatabase::getInstance('SMARTEST');
                }catch(SmartestDatabaseException $e){
                    
                    switch($e->getDbErrorType()){
                        
                        case SmartestDatabaseException::SPEC_DB_ACCESS_DENIED:
                        $s = SM_INSTALLSTATUS_DB_NOT_ALLOWED;
                        break;
                        
                        case SmartestDatabaseException::INVALID_CONNECTION_NAME:
                        $s = SM_INSTALLSTATUS_NO_DB_CONFIG;
                        break;
                        
                        case SmartestDatabaseException::CONNECTION_IMPOSSIBLE:
                        default:
                        $s = SM_INSTALLSTATUS_DB_NO_CONN;
                        break;
                        
                    }
                    
                    if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                        SmartestCache::save('installation_status', $s, -1, true);
                    }
                    
                    throw new SmartestNotInstalledException($s);
                    
                }
                
                // echo 'everything ok so far';
                if(count($db->getTables()) < 1){
                    
                    if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                        SmartestCache::save('installation_status', SM_INSTALLSTATUS_DB_NO_CREATE_PERMS, -1, true);
                    }
                    
                    throw new SmartestNotInstalledException(SM_INSTALLSTATUS_DB_NO_CREATE_PERMS);
                }
                
                if(count($db->queryToArray("SELECT site_id FROM Sites")) < 1){
                    
                    if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                        SmartestCache::save('installation_status', SM_INSTALLSTATUS_NO_SITES, -1, true);
                    }
                    
                    throw new SmartestNotInstalledException(SM_INSTALLSTATUS_NO_SITES);
                    
                }
                
                if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                    SmartestCache::save('installation_status', SM_INSTALLSTATUS_COMPLETE, -1, true);
                }
                
            }else{
                // Config files haven't been created yet
                if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                    SmartestCache::save('installation_status', SM_INSTALLSTATUS_NO_CONFIG, -1, true);
                }
                
                throw new SmartestNotInstalledException(SM_INSTALLSTATUS_NO_CONFIG);
            }
        }else{
            return SmartestCache::load('installation_status', true);
        }
        
    }
    
}