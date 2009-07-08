<?php
/**
 * Implements the installer file
 *
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
            $ph->setParameter('perms', $this->createPermissionsScript());
            break;

            case SM_INSTALLSTATUS_NO_CONFIG: // 2
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "Currently, no valid configuration files can be found. Yep, you guessed - we're going to need some of those. Smartest will create them from the details you enter below.");
            $this->removePermissionsScript();
            break;
            
            case SM_INSTALLSTATUS_NO_DB_CONFIG: // 4
            $ph->setParameter('screen', 'create_config.php');
            $ph->setParameter('message', "Currently, no valid database configuration files can be found. Please fill in the details below so that Smartest can connect to the database.");
            $this->removePermissionsScript();
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
    
    public function createHtAccessFile($rewritebase, $force = false){
        
        if($force || !file_exists(SM_ROOT_DIR.'Public/.htaccess')){
            
            $fc = file_get_contents(SM_ROOT_DIR.'System/Install/Samples/htaccess-sample.txt');
            $fc = str_replace('%REWRITE_BASE%', $rewritebase, $fc);
            $fc = str_replace('%DATE_CREATED%', date('M jS, Y \a\t h:i a'), $fc);
            
            if (file_put_contents(SM_ROOT_DIR.'Public/.htaccess', $fc)){
                SmartestLog::getInstance('installer')->log('Created file ./Public/.htaccess', SM_LOG_DEBUG);
                SmartestSystemSettingHelper::save('htaccess_rewrite_base', $domain);
                return true;
            }else{
                SmartestLog::getInstance('installer')->log('Couldn\'t create file ./Public/.htaccess. Permissions should have been sorted by this stage, but check them anyway.', SM_LOG_WARNING);
            }
        }
        
    }
    
    public function createQuinceControllerFile($domain, $force = false){
        
        if($force || !file_exists(SM_ROOT_DIR.'Configuration/controller.xml')){
            
            $fc = file_get_contents(SM_ROOT_DIR.'System/Install/Samples/controller-sample.xml');
            $fc = str_replace('%DOMAIN%', $domain, $fc);
            $fc = str_replace('%DATE_CREATED%', date('M jS, Y \a\t h:i a'), $fc);
            
            if (file_put_contents(SM_ROOT_DIR.'Configuration/controller.xml', $fc)){
                SmartestLog::getInstance('installer')->log('Created file ./Configuration/controller.xml', SM_LOG_DEBUG);
                SmartestSystemSettingHelper::save('controller_domain', $domain);
                return true;
            }else{
                SmartestLog::getInstance('installer')->log('Couldn\'t create file ./Configuration/controller.xml. Permissions should have been sorted by this stage, but check them anyway.', SM_LOG_WARNING);
            }
        }
        
    }
    
    public function removePermissionsScript(){
        
        $hash = substr(sha1(SM_ROOT_DIR), 0, 8);
        $script_name = '/tmp/smartest_install_'.$hash.'.tmp.sh';
        
        if(is_file($script_name) && is_writable($script_name) && is_writable('/tmp')){
            unlink($script_name);
        }
        
    }
    
    public function createPermissionsScript(){
        
        $ph = new SmartestParameterHolder('Permissions information');
        
        $system_data = SmartestYamlHelper::toParameterHolder(SM_ROOT_DIR.'System/Core/Info/system.yml', false);
        $writable_files = array_merge($system_data->g('system')->g('writable_locations')->g('always')->toArray(), $system_data->g('system')->g('writable_locations')->g('installation')->toArray());
        $errors = array();
        
        $ph->setParameter('writable_files', $writable_files);
        
        foreach($writable_files as $file){
			if(!is_writable(SM_ROOT_DIR.$file)){
				$errors[] = SM_ROOT_DIR.$file;
			}
		}
		
		$ph->setParameter('writable_files', $writable_files);
		$ph->setParameter('errors', $errors);
        
        if(is_dir('/tmp') && is_writable('/tmp')){
            
            $ph->setParameter('script_created', true);
            $hash = substr(sha1(SM_ROOT_DIR), 0, 8);
            $script_name = '/tmp/smartest_install_'.$hash.'.tmp.sh';
            $ph->setParameter('script_name', $script_name);
            
            // TODO: Make this windows compatible
            
            $contents = '#! /usr/bin/env bash'."\n\n# File permissions installer script, auto-generated by Smartest on: ".date('M jS, Y \a\t h:i a')."\n\n";
            
            foreach($errors as $file){
    			$contents .= 'chmod 777 '.$file."\n";
    		}
            
            file_put_contents($script_name, $contents);
            chmod($script_name, 0777);
            
        }else{
            $ph->setParameter('script_created', false);
        }
        
        return $ph;
        
    }
}
