<?php
/**
 * Implements the installer file
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Marcus Gilroy-Ware <marcus@vsccreative.com>
 */

class SmartestInstaller{
    
    protected $_exception;
    
    public function __construct(){
        
    }
    
    public function getStage(SmartestNotInstalledException $e){
        
        $this->_exception = $e;
        
        $ph = new SmartestParameterHolder("Installer status parameters");
        
        switch($this->_exception->getInstallationStatus()){
        
            case SM_INSTALLSTATUS_NO_FILE_PERMS: // 1
            $ph->setParameter('screen', 'set_perms.php');
            break;

            case SM_INSTALLSTATUS_NO_CONFIG: // 2
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "Currently, no valid configuration files can be found. Please fill in the details below so that Smartest can connect to the database.");
            break;
            
            case SM_INSTALLSTATUS_NO_DB_CONFIG: // 4
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "Currently, no valid database configuration files can be found. Please fill in the details below so that Smartest can connect to the database.");
            break;

            case SM_INSTALLSTATUS_DB_NO_CONN: // 8:
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "For some reason, Smartest couldn't connect to the database with the details you provided.");
            $ph->setParameter('db_connection_parameters', $e->getDatabaseConnectionParameters());
            break;

            case SM_INSTALLSTATUS_DB_NONE: // 16
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "The database you entered doesn't seem to exist yet. Please create it first.");
            $ph->setParameter('db_connection_parameters', $e->getDatabaseConnectionParameters());
            break;

            case SM_INSTALLSTATUS_DB_NOT_ALLOWED: // 32
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "Smartest doesn't have access to database you entered. Please grant permissions on it to the user you are using to connect or specify new authentication details or a different database.");
            $ph->setParameter('db_connection_parameters', $e->getDatabaseConnectionParameters());
            break;
            
            case SM_INSTALLSTATUS_DB_NO_CREATE_PERMS:
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "Smartest doesn't have permission to create tables in the database you entered. Please grant these permissions on it to the user you are using to connect or specify new authentication details.");
            $ph->setParameter('db_connection_parameters', $e->getDatabaseConnectionParameters());
            break;

            case SM_INSTALLSTATUS_NO_USERS:
            $ph->setParameter('screen', 'create_user.php');
            break;
            
            case SM_INSTALLSTATUS_USER_DATA_INVALID:
            $ph->setParameter('screen', 'create_user.php');
            $ph->setParameter('errors', $this->_exception->getValidationErrors());
            break;
            
            case SM_INSTALLSTATUS_NO_SITES:
            $ph->setParameter('screen', 'create_site.php');
            break;
            
            case SM_INSTALLSTATUS_SITE_DATA_INVALID:
            $ph->setParameter('screen', 'create_site.php');
            $ph->setParameter('errors', $this->_exception->getValidationErrors());
            break;
        
        }
        
        return $ph;
        
    }
    
    public function createNewDatabaseConfig(SmartestParameterHolder $ph){
        
        $c = file_get_contents(SM_ROOT_DIR."System/Install/Samples/database-sample.ini");
        $c = str_replace("__USERNAME__", $ph->getParameter('username'), $c);
        $c = str_replace("__PASSWORD__", $ph->getParameter('password'), $c);
        $c = str_replace("__DATABASE__", $ph->getParameter('database'), $c);
        $c = str_replace("__HOST__", $ph->getParameter('host'), $c);
        $c = str_replace("__NOW__", date("Y-m-d h:i:s"), $c);
        
        if (file_put_contents(SM_ROOT_DIR."Configuration/database.ini", $c)){
            SmartestLog::getInstance('installer')->log('Created file ./Configuration/database.ini', SM_LOG_DEBUG);
            return true;
        }
        
    }
    
    public function createHtAccessFile($force = false){
        
        if($force || !file_exists(SM_ROOT_DIR.'Public/.htaccess')){
            if (copy(SM_ROOT_DIR.'System/Install/Samples/htaccess-sample.txt', SM_ROOT_DIR.'Public/.htaccess')){
                SmartestLog::getInstance('installer')->log('Created file ./Public/.htaccess', SM_LOG_DEBUG);
                return true;
            }
        }
        
    }
    
    public function createQuinceControllerFile($domain, $force = false){
        
        if($force || !file_exists(SM_ROOT_DIR.'Configuration/controller.xml')){
            $fc = file_get_contents(SM_ROOT_DIR.'System/Install/Samples/controller-sample.xml');
            $fc = str_replace('%DOMAIN%', $domain, $fc);
            if (file_put_contents(SM_ROOT_DIR.'Configuration/controller.xml', $fc)){
                SmartestLog::getInstance('installer')->log('Created file ./Configuration/controller.xml', SM_LOG_DEBUG);
                return true;
            }
        }
        
    }
}
