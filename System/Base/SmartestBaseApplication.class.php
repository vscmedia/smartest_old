<?php

/**
 * Contains the base for each public page in the website
 *
 * PHP version 5
 *
 * @category   System
 * @package    Smartest
 * @license    Smartest License
 * @author     Marcus Gilroy-Ware <marcus@visudo.com>
 */

// DO NOT EDIT! This file may be overwritten when Smartest is upgraded

class SmartestBaseApplication extends SmartestBaseProcess{

	protected $_results;
	// public $_presentationLayer;
	public $manager;
	public $domain;
	public $module;
	protected $database;
	protected $_errorStack;
	protected $_settings;
	protected $_resultIndex;
	protected $_formReturnUri;
	protected $_formContinueUri;
	protected $_formFailUri;
	protected $_userTokenHelper;
	public $_auth;
	private $_user_loaded_classes;
	
	final public function __construct(){
	
		$this->database = SmartestPersistentObject::get('db:main');
		$this->_errorStack =& SmartestPersistentObject::get('errors:stack');
		$this->domain = SM_CONTROLLER_DOMAIN;
		$this->module = SM_CONTROLLER_MODULE;
		$this->_resultIndex = 0;
		$this->userTokenHelper = new SmartestUsersHelper();
		$this->settings = new SmartestParameterHolder("Application settings");
		
		SmartestSession::set('user:currentApp', SM_CONTROLLER_MODULE);
		SmartestSession::set('user:currentAction', SM_CONTROLLER_METHOD);
		
		// Applications can come bundled with their own template plugins
		if(is_dir(SM_CONTROLLER_MODULE_DIR.'Library/Templating/Plugins/')){
		    $this->getPresentationLayer()->addPluginDirectory(SM_CONTROLLER_MODULE_DIR.'Library/Templating/Plugins/');
		}
		
		// load user-defined application-wide settings
		// TODO: add caching here
		if(is_file(SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml")){
			
			$this->settings->setParameter('application', SmartestYamlHelper::toParameterHolder(SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml", SM_DEVELOPER_MODE));
			
			// print_r($this->settings->getParameter('application')->getParameter('messages')->getParameter('summaries'));
			
			/* if(is_array($appSettingsFileData)){
			    
			    // $this->settings['application'] = new SmartestParameterHolder('Application Settings Holder', true);
			    // $this->settings['application']->loadArray($appSettingsFileData, true);
			    $this->settings['application'] = $appSettingsFileData;
			    
			}else{
				throw new SmartestException("Error parsing config file: ".SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml");
			} */
		}
		
		// load user-defined system-wide settings
		// TODO: add caching here
		/* if(is_file(SM_ROOT_DIR."Configuration/user.ini")){
			if($this->_settings['global'] = @parse_ini_file(SM_ROOT_DIR."Configuration/user.ini")){
				
			}else{
				throw new SmartestException("Error parsing config file: ".SM_ROOT_DIR."Configuration/user.ini");
			}
		} */
		
		
		/////////////// MANAGERS CODE WILL BE DEPRECATED SOON - FUNCTIONALITIES IN MANAGERS ARE BEING MOVED TO HELPERS ////////////////
		// Detect to see if manager classes exist and initiate them, if configured to do so
		$managerClassFile = SM_ROOT_DIR.'Managers/'.SM_CONTROLLER_CLASS."Manager.class.php";
		$managerClass = SM_CONTROLLER_CLASS."Manager";
		
		define("SM_MANAGER_CLASS", $managerClass);
		
		if(@is_file(SM_ROOT_DIR.'Managers/'.SM_CONTROLLER_CLASS."Manager.class.php")){
		
			define("SM_MANAGER_CLASS_FILE", SM_ROOT_DIR.'Managers/'.SM_CONTROLLER_CLASS."Manager.class.php");
			include_once(SM_MANAGER_CLASS_FILE);
		
			if(class_exists(SM_MANAGER_CLASS)){
				
				$this->manager = new $managerClass($this->database);
				
			}
			
		}else if(defined("SM_CONTROLLER_MODULE_DIR")){
		  
		    if(@is_file(SM_CONTROLLER_MODULE_DIR.SM_CONTROLLER_CLASS."Manager.class.php")){
			
				define("SM_MANAGER_CLASS_FILE", SM_CONTROLLER_MODULE_DIR.SM_CONTROLLER_CLASS."Manager.class.php");
				include_once(SM_MANAGER_CLASS_FILE);
				
				if(class_exists(SM_MANAGER_CLASS)){
				
					$this->manager = new $managerClass($this->database);
				
				}
			
			}
			
		}
		
		if(method_exists($this, '__myConstructor')){
		    $this->__myConstructor();
		}
		
		if(method_exists($this, "__moduleConstruct")){
		    $this->__moduleConstruct();
	    }
	    
	    if(method_exists($this, "__systemModulePreConstruct")){
		    $this->__systemModulePreConstruct();
	    }
	    
	    if(SmartestSession::get('user:isAuthenticated')){
	        $this->send($this->getUser(), '_user');
	    }
	    
	}
	
	final public function __destruct(){
		
		if(method_exists($this, "__moduleDestruct")){
			$this->__moduleDestruct();
		}
		
	}
	
	///// String Stuff //////
    
    /////////////// THIS SHIT IS DEPRECATED. SmartestStringHelper OR SmartestString SHOULD BE USED FOR STRING MANIPULATION //////////////////
    
	protected function getRandomString($size=32){ // creates a "random" string, $size chars in length
	
		return SmartestStringHelper::random($size);
		
	}
    
	protected function getPageNameFromTitle($page_title){
    	
		return SmartestStringHelper::toVarName($page_title);
    	
	}
	
	///// Authentication Stuff /////
	
	protected function getUser(){
	    
	    return SmartestPersistentObject::get('user');
	    
	}
	
	/* 
	protected function requireAuthenticatedUser($authservicename){
		if(!$this->_auth->getUserIsLoggedIn()){
			$this->redirect($this->domain."smartest/login");
		}
	}
	
	protected function requireToken($token){
	    if(!$this->getUser()->hasToken($token)){
	        $this->addUserMessageToNextRequest('You do not have sufficient access privileges for that action.');
	        $this->redirect('/smartest');
	    }
	}
	
	*/
	
	///// Cache Stuff /////
	
	protected function loadData($token, $is_smartest=false){
		return SmartestCache::load($token, $is_smartest);
	}
	
	protected function saveData($token, $data, $expire=-1, $is_smartest=false){
		return SmartestCache::save($token, $data, $expire, $is_smartest);
	}
	
	protected function hasData($token, $is_smartest){
		return SmartestCache::hasData($token, $is_smartest);
	}
		
	///// Passing Data to presentation layer //////
	
	protected function getPresentationLayer(){
	    return SmartestPersistentObject::get('presentationLayer');
	}
	
	protected function getUserAgent(){
	    return SmartestPersistentObject::get('userAgent');
	}
	
	protected function setTitle($page_title){
		$this->getPresentationLayer()->assign("sectionName", $page_title);
	}
    
    final protected function bring($data, $name=""){
    	$this->send($data, $name);
    }
    
    final protected function send($data, $name=""){
        
        if(strlen($name) > 0){
    		$this->getPresentationLayer()->assign($name, $data);
    	}else{
    		$this->getPresentationLayer()->_tpl_vars["content"][$this->_resultIndex] = $data;
    		$this->_resultIndex++;
    	}
    }
    
    ///// Preferences/Settings Access //////
    
    public function getApplicationPreference($pref_name){
    	if(isset($this->_settings['application'][$pref_name])){
    		return $this->_settings['application'][$pref_name];
    	}else{
    		return false;
    	}
    }
    
    public function getGlobalPreference($pref_name){
    	if(isset($this->_settings['global'][$pref_name])){
    		return $this->_settings['global'][$pref_name];
    	}else{
    		return false;
    	}
    }
    
    ///// Flow Control //////
    
    protected function redirect($destination=""){
		
		if(strlen($destination) == 0){
			$destination = constant('SM_CONTROLLER_DOMAIN');
		}else if($destination{0} == "/"){
		    $destination = constant('SM_CONTROLLER_DOMAIN').substr($destination, 1);
		}
		
		header("location:".$destination);
		// exit;
	}
    
    ///// Check for Libraries /////
    
    protected function loadApplicationClass($class){
        
        $dir = SM_CONTROLLER_MODULE_DIR.'Library/';
        
        if(substr($class, -4) != '.php'){
            $class = $class.'.class.php';
        }
        
        if(is_file($dir.$class)){
            if(!in_array($class, $this->_user_loaded_classes)){
                $this->_user_loaded_classes[] = $class;
                require $dir.$class;
            }
        }else{
            $this->log("SmartestBaseApplication::loadClass() tried to load a class that does not exist in $dir", 'system');
            throw new SmartestException("SmartestBaseApplication::loadClass() tried to load a class that does not exist in $dir");
        }
        
    }
    
    protected function helperIsInstalled($helper){
        
        if(substr($helper, -6) == 'Helper'){
            $full_helper = $helper;
            $helper = substr($full_helper, 0, -6);
        }else{
            $full_helper = $helper.'Helper';
        }
        
        if(substr($helper, 0, 8) == 'Smartest'){
            // we are checking for a System helper, so only look in System/Helpers/
            if(is_dir(SM_ROOT_DIR.'System/Helpers/'.$helper.'.helper') && class_exists($full_helper)){
                return true;
            }
        }else{
            // We could either be referring to a user-created library or to a system library (but without using the 'Smartest' prefix, ie 'ManyToMany' for SmartestManyToManyHelper)
            if(is_dir(SM_ROOT_DIR.'Library/Helpers/'.$helper.'.helper') && class_exists($full_helper)){
                return true;
            }else if(is_dir(SM_ROOT_DIR.'System/Helpers/Smartest'.$helper.'.helper') && class_exists('Smartest'.$full_helper)){
                return true;
            }
        }
        
    }
    
    ///// Errors and Logging /////
    
    public function log($message, $log, $type=''){
    	return SmartestLog::getinstance($log)->log($message, $type);
    }
    
    function _error($message, $type=''){
    	
    	if(!$message){
    		$message = "[unspecified error]";
    	}
    	
    	if(!$type){
    		$type = 106;
    	}
    	
    	$this->_errorStack->recordError($message, $type);
    }

}