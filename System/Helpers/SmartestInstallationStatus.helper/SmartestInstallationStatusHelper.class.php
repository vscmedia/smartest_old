<?php

require SM_ROOT_DIR.'System/Base/Exceptions/SmartestNotInstalledException.class.php';

class SmartestInstallationStatusHelper{
    
    public static function checkStatus($purge=false){
        
        // Add this in a few installlations time SmartestSystemSettingsHelper::load('successful_install') === true
        if(SmartestCache::load('installation_status', true) === SM_INSTALLSTATUS_COMPLETE && !$purge && (is_file(SM_ROOT_DIR.'Public/.htaccess') && is_file(SM_ROOT_DIR.'Configuration/controller.xml') && is_file(SM_ROOT_DIR.'Configuration/database.ini'))){
            return SmartestCache::load('installation_status', true);
        }else{
        // if(SmartestCache::load('installation_status', true) !== SM_INSTALLSTATUS_COMPLETE || $purge || (!is_file(SM_ROOT_DIR.'Public/.htaccess') || !is_file(SM_ROOT_DIR.'Configuration/controller.xml') || !is_file(SM_ROOT_DIR.'Configuration/database.ini'))){
            
            // session_start();
            $system_data = SmartestYamlHelper::toParameterHolder(SM_ROOT_DIR.'System/Core/Info/system.yml', false);
            $writable_files = array_merge($system_data->g('system')->g('writable_locations')->g('always')->toArray(), $system_data->g('system')->g('writable_locations')->g('installation')->toArray());
            
            $errors = array();
            
            foreach($writable_files as $file){
    			if(!is_writable(SM_ROOT_DIR.$file)){
    				$errors[] = SM_ROOT_DIR.$file;
    			}
    		}
    		
    		if(count($errors)){
    		    throw new SmartestNotInstalledException(SM_INSTALLSTATUS_NO_FILE_PERMS);
    		}
    		
    		// Now, if a form has been submit, there might be an installer action that needs to be carried out before we check the installation status again
    		if(isset($_POST['execute']) && $_POST['execute'] == '1' && isset($_POST['action'])){
    		    
    		    if(!class_exists('SmartestInstaller')){
    	            require SM_ROOT_DIR.'System/Install/SmartestInstaller.class.php';
                }

                $action = $_POST['action'];
                
                SmartestLog::getInstance('installer')->log('The installer submitted action \''.$action.'\'.', SM_LOG_DEBUG);

                // Yes, yes, I know, switch/case is ugly, but the whole point of this is not to rely on any of the actual Smartest code - just small and simple.
                switch($action){

                    case 'createConfigs':
                    
                    $ph = new SmartestParameterHolder("New database connection parameters");
                    $ph->setParameter('username', $_POST['db_username']);
                    $ph->setParameter('password', $_POST['db_password']);
                    $ph->setParameter('database', $_POST['db_database']);
                    $ph->setParameter('host', $_POST['db_host']);
                    
                    $installer = new SmartestInstaller;
                    $installer->createNewDatabaseConfig($ph);
                    
                    if(isset($_POST['controller_domain'])){
                        $controller_domain = $_POST['controller_domain'];
                        if(substr($controller_domain, -1, 1) != '/'){
                            $controller_domain .= '/';
                        }
                    }else{
                        $controller_domain = '';
                    }
                    
                    // $installer->createQuinceControllerFile($controller_domain);
                    $installer->createHtAccessFile('/'.$controller_domain);
                    
                    break;
                    
                    case 'createUser':
                    
                    $fve = new SmartestParameterHolder("User creation form validator errors");
                    
                    if(strlen($_POST['smartest_username']) < 3){
                        // problem with username
                        $fve->setParameter('username', "The username you entered is too short. It must have a minimum of three characters.");
                        SmartestLog::getInstance('installer')->log('The username given to the installer at stage 3 was shorter than the required 3 character ('.strlen($_POST['smartest_password']).' chars).', SM_LOG_WARNING);
                    }
                    
                    if(strlen($_POST['smartest_password']) < 6){
                        // problem with password 1
                        $fve->setParameter('password', "The password you entered is too short. It must have a minimum of six characters.");
                        SmartestLog::getInstance('installer')->log('The password given to the installer at stage 3 was shorter than the required 6 character ('.strlen($_POST['smartest_password']).' chars).', SM_LOG_WARNING);
                    }else if($_POST['smartest_password'] != $_POST['smartest_password_2']){
                        // problem with password 2
                        $fve->setParameter('password', "The passwords you entered did not match.");
                        SmartestLog::getInstance('installer')->log('The passwords given to the installer at stage 3 did not match.', SM_LOG_WARNING);
                    }
                    
                    if(strlen($_POST['smartest_firstname']) < 2){
                        // problem with firstname
                        $fve->setParameter('firstname', "The first name you entered is too short. It must have a minimum of two characters.");
                        SmartestLog::getInstance('installer')->log('The first name given to the installer at stage 3 was shorter than the required 2 characters ('.strlen($_POST['smartest_firstname']).' chars).', SM_LOG_WARNING);
                    }
                    
                    if(!SmartestStringHelper::isEmailAddress($_POST['smartest_email'])){
                        // problem with email format
                        $fve->setParameter('email', "Please enter a valid e-mail address.");
                        SmartestLog::getInstance('installer')->log('The e-mail address given to the installer at stage 3 was invalid.', SM_LOG_WARNING);
                    }
                    
                    if($fve->hasData()){
                        $nie = new SmartestNotInstalledException(SM_INSTALLSTATUS_USER_DATA_INVALID);
                        $nie->setValidationErrors($fve);
                        throw $nie;
                    }else{
                        
                        $username = SmartestStringHelper::toVarName($_POST['smartest_username']);
                        $password = md5($_POST['smartest_password']);
                        $firstname = SmartestStringHelper::sanitize($_POST['smartest_firstname']);
                        $firstname = str_replace("'", '', $firstname);
                        $lastname = SmartestStringHelper::sanitize($_POST['smartest_lastname']);
                        $lastname = str_replace("'", '', $lastname);
                        $email = SmartestStringHelper::isEmailAddress($_POST['smartest_email']) ? $_POST['smartest_email'] : '';
                        
                        $sql = file_get_contents(SM_ROOT_DIR.'System/Install/SqlScripts/create_user.sql.txt');
                        $sql = str_replace('%USERNAME%', $username, $sql);
                        $sql = str_replace('%PASSWORD%', $password, $sql);
                        $sql = str_replace('%FIRSTNAME%', $firstname, $sql);
                        $sql = str_replace('%LASTNAME%', $lastname, $sql);
                        $sql = str_replace('%EMAIL%', $email, $sql);
                        
                        $queries = explode(';', $sql);
                        $db = SmartestDatabase::getInstance('SMARTEST');

                        foreach($queries as $query){
                            if(strlen(trim($query))){
                                try{
                                    $db->rawQuery(trim($query).';');
                                }catch (SmartestDatabaseException $user_error) {
                                    SmartestLog::getInstance('installer')->log('There was an error creating user account data on query: '.$query.'.', SM_LOG_ERROR);
                                    continue;
                                }
                            }
                        }
                        
                        SmartestLog::getInstance('installer')->log('Created system user \'Smartest\' with a uid of 0', SM_LOG_DEBUG);
                        SmartestLog::getInstance('installer')->log('Created user '.$username.' with a uid of 1', SM_LOG_DEBUG);
                        
                    }
                    
                    break;
                    
                    case 'createSite':
                    
                    $fve = new SmartestParameterHolder("Site creation form validator errors");
                    
                    if(strlen($_POST['site_name']) < 3){
                        // problem with username
                        $fve->setParameter('name', "The site name you entered is too short. It must have a minimum of three characters.");
                        SmartestLog::getInstance('installer')->log('The site name given to the installer at stage 4 was shorter than the required 3 characters ('.strlen($_POST['site_name']).' chars).', SM_LOG_WARNING);
                    }
                    
                    if(strlen($_POST['site_host']) < 5){
                        // problem with username
                        $fve->setParameter('host', "The hostname you entered is too short. It must have a minimum of five characters.");
                        SmartestLog::getInstance('installer')->log('The hostname given to the installer at stage 4 was shorter than the possible 5 characters ('.strlen($_POST['site_host']).' chars).', SM_LOG_WARNING);
                    }
                    
                    if($fve->hasData()){
                        $nie = new SmartestNotInstalledException(SM_INSTALLSTATUS_SITE_DATA_INVALID);
                        $nie->setValidationErrors($fve);
                        throw $nie;
                    }else{
                        
                        $db = SmartestDatabase::getInstance('SMARTEST');
                        
                        $uq = $db->queryToArray('SELECT user_email FROM Users WHERE user_id=1');
                        $email = $uq[0]['user_email'];
                        $sitename = SmartestStringHelper::sanitize($_POST['site_name']);
                        $sitename = str_replace("'", "\'", $sitename);
                        $hostname = SmartestStringHelper::sanitize($_POST['site_host']);
                        $template = ($_POST['site_initial_tpl'] == '_DEFAULT') ? '' : SmartestStringHelper::sanitize($_POST['site_initial_tpl']);
                        
                        $sql = file_get_contents(SM_ROOT_DIR.'System/Install/SqlScripts/create_site.sql.txt');
                        $sql = str_replace('%NOW%', time(), $sql);
                        $sql = str_replace('%TEMPLATE%', $template, $sql);
                        $sql = str_replace('%EMAIL%', $email, $sql);
                        $sql = str_replace('%SITENAME%', $sitename, $sql);
                        $sql = str_replace('%DIRECTORYNAME%', substr(SmartestStringHelper::toCamelCase($sitename), 0, 64), $sql);
                        $sql = str_replace('%HOSTNAME%', $hostname, $sql);
                        $sql = str_replace('%HOMEPAGEWEBID%', SmartestStringHelper::random(32), $sql);
                        $sql = str_replace('%ERRORPAGEWEBID%', SmartestStringHelper::random(32), $sql);
                        $sql = str_replace('%SEARCHPAGEWEBID%', SmartestStringHelper::random(32), $sql);
                        $sql = str_replace('%TAGPAGEWEBID%', SmartestStringHelper::random(32), $sql);
                        
                        // Attempt to create site dir
                        $site_dir = SM_ROOT_DIR.'Sites/'.substr(SmartestStringHelper::toCamelCase($sitename), 0, 64).'/';

                	    if(!is_dir($site_dir)){mkdir($site_dir);}
                	    if(!is_dir($site_dir.'Presentation')){mkdir($site_dir.'Presentation');}
                	    if(!is_dir($site_dir.'Configuration')){mkdir($site_dir.'Configuration');}
                	    if(!is_file($site_dir.'Configuration/site.yml')){file_put_contents($site_dir.'Configuration/site.yml', '');}
                	    if(!is_dir($site_dir.'Library')){mkdir($site_dir.'Library');}
                	    if(!is_dir($site_dir.'Library/Actions')){mkdir($site_dir.'Library/Actions');}
                	    if(!is_dir($site_dir.'Library/Actions')){mkdir($site_dir.'Library/ObjectModel');}
                	    $actions_class_name = SmartestStringHelper::toCamelCase($sitename).'Actions';
                	    $class_file_contents = file_get_contents(SM_ROOT_DIR.'System/Base/ClassTemplates/SiteActions.class.php.txt');
                	    $class_file_contents = str_replace('__TIMESTAMP__', time('Y-m-d h:i:s'), $class_file_contents);
                	    if(!is_file($site_dir.'Library/Actions/SiteActions.class.php')){file_put_contents($site_dir.'Library/Actions/SiteActions.class.php', $class_file_contents);}
                	    chmod($site_dir.'Library/Actions/SiteActions.class.php', 0666);
                        
                        // Lastly, execute database queries     
                        $queries = explode(';', $sql);

                        foreach($queries as $query){
                            if(strlen(trim($query))){
                                try{
                                    $db->rawQuery(trim($query).';');
                                }catch (SmartestDatabaseException $user_error) {
                                    SmartestLog::getInstance('installer')->log('There was an error creating site data on query: '.$query.'.', SM_LOG_ERROR);
                                    continue;
                                }
                            }
                        }
                        
                        $cd = SmartestSystemSettingHelper::load('htaccess_rewrite_base');
                        
                        if(strlen($cd) && $cd != '/'){
                            $location = $cd.'smartest/login';
                        }else{
                            $location = '/smartest/login';
                        }
                        
                        header("Location: ".$location);
                        
                    }
                    
                    break;

                }

            }
            
            // ok, now the status can be checked again
            
            if(file_exists(SM_ROOT_DIR.'Configuration/database.ini')){
                // Config files are in place, so try connecting to the database
                try{
                    $db = SmartestDatabase::getInstance('SMARTEST', true);
                }catch(SmartestDatabaseException $e){
                    
                    switch($e->getDbErrorType()){
                        
                        case SmartestDatabaseException::SPEC_DB_ACCESS_DENIED:
                        SmartestLog::getInstance('installer')->log('Database error: access denied for user specified in connection [SMARTEST].', SM_LOG_WARNING);
                        $ph = new SmartestParameterHolder('Database connection parameters');
                        $ph->setParameter('username', $e->getUsername());
                        $ph->setParameter('database', $e->getDatabase());
                        $ph->setParameter('host', $e->getHost());
                        $s = SM_INSTALLSTATUS_DB_NOT_ALLOWED;
                        break;
                        
                        case SmartestDatabaseException::INVALID_CONNECTION_NAME:
                        SmartestLog::getInstance('installer')->log('Database error: connection [SMARTEST] does not exist.', SM_LOG_WARNING);
                        $ph = new SmartestParameterHolder('Database connection parameters');
                        $ph->setParameter('username', $e->getUsername());
                        $ph->setParameter('database', $e->getDatabase());
                        $ph->setParameter('host', $e->getHost());
                        $s = SM_INSTALLSTATUS_NO_DB_CONFIG;
                        break;
                        
                        case SmartestDatabaseException::CONNECTION_IMPOSSIBLE:
                        default:
                        SmartestLog::getInstance('installer')->log('Database error: Smartest could not connect to the database with the details provided in ./Configuration/database.ini', SM_LOG_WARNING);
                        $ph = new SmartestParameterHolder('Database connection parameters');
                        $ph->setParameter('username', $e->getUsername());
                        $ph->setParameter('database', $e->getDatabase());
                        $ph->setParameter('host', $e->getHost());
                        $s = SM_INSTALLSTATUS_DB_NO_CONN;
                        break;
                        
                    }
                    
                    if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                        SmartestCache::save('installation_status', $s, -1, true);
                    }
                    
                    $nie = new SmartestNotInstalledException($s);
                    $nie->setDatabaseConnectionParameters($ph);
                    throw $nie;
                    
                }
                
                SmartestLog::getInstance('installer')->log('SmartestInstaller has a working database connection.', SM_LOG_DEBUG);
                
                if(count($db->getTables()) < 1){
                    SmartestLog::getInstance('installer')->log('Trying to build database tables structure.', SM_LOG_DEBUG);
                    try{
                        $db->executeSqlFile(SM_ROOT_DIR."System/Install/SqlScripts/table_setup.sql");
                    }catch(SmartestDatabaseException $e){
                        // The tables could not be set up. Write to install log
                        SmartestLog::getInstance('installer')->log('Database schema setup failed: '.$e->getMessage(), SM_LOG_ERROR);
                    }
                }
                
                // If we have got this far then that means we have a working connection to the database
                if(count($db->getTables(true)) < 1){
                    
                    if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                        SmartestCache::save('installation_status', SM_INSTALLSTATUS_DB_NO_CREATE_PERMS, -1, true);
                    }
                    
                    SmartestLog::getInstance('installer')->log('After trying to create, database tables still don\'t exist which probably means Smartest doesn\'t have permission to create them', SM_LOG_WARNING);
                    $ph = SmartestDatabase::readConfiguration('SMARTEST');
                    $nie = new SmartestNotInstalledException(SM_INSTALLSTATUS_DB_NO_CREATE_PERMS);
                    $nie->setDatabaseConnectionParameters($ph);
                    throw $nie;
                }
                
                if(count($db->queryToArray("SELECT user_id FROM Users")) < 2){
                    
                    if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                        SmartestCache::save('installation_status', SM_INSTALLSTATUS_NO_USERS, -1, true);
                    }
                    
                    throw new SmartestNotInstalledException(SM_INSTALLSTATUS_NO_USERS);
                    
                }
                
                if(count($db->queryToArray("SELECT token_id FROM UserTokens")) < 1){
                    
                    try{
                        $db->executeSqlFile(SM_ROOT_DIR."System/Install/SqlScripts/user_tokens.sql");
                    }catch (SmartestDatabaseException $tokens_error) {
                        SmartestLog::getInstance('installer')->log('There was an error creating user tokens from file System/Install/SqlScripts/user_tokens.sql: '.$tokens_error->getMessage(), SM_LOG_ERROR);
                    }
                    
                }
                
                if(count($db->queryToArray("SELECT site_id FROM Sites")) < 1){
                    
                    if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                        SmartestCache::save('installation_status', SM_INSTALLSTATUS_NO_SITES, -1, true);
                    }
                    
                    throw new SmartestNotInstalledException(SM_INSTALLSTATUS_NO_SITES);
                    
                }
                
                if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                    SmartestCache::save('installation_status', SM_INSTALLSTATUS_COMPLETE, -1, true);
                    if(!SmartestSystemSettingHelper::hasData('_system_installed_timestamp')){
                        SmartestSystemSettingHelper::save('_system_installed_timestamp', time());
                    }
                }
                
            }else{
                
                // Config files haven't been created yet
                if(is_writable(SM_ROOT_DIR."System/Cache/Data/")){
                    
                    SmartestCache::save('installation_status', SM_INSTALLSTATUS_NO_CONFIG, -1, true);
                    
                }
                
                throw new SmartestNotInstalledException(SM_INSTALLSTATUS_NO_CONFIG);
            }
        }
        
    }
    
}