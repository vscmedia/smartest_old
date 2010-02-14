<?php

/**
 * Contains the base for each public page in the website
 *
 * PHP version 5
 *
 * @category   System
 * @package    Smartest
 * @license    Smartest License
 * @author     Marcus Gilroy-Ware <marcus@vsccreative.com>
 */

// DO NOT EDIT! This file may be overwritten when Smartest is upgraded

class SmartestBaseApplication extends SmartestBaseProcess{

	public $manager;
	public $domain;
	public $module;
	public $_auth;
	
	protected $database;
	protected $_errorStack;
	protected $_settings;
	protected $_resultIndex;
	protected $_formReturnUri;
	protected $_formContinueUri;
	protected $_formFailUri;
	protected $_userTokenHelper;
	protected $_results;
	protected $_cached_application_preferences = array();
	protected $_cached_global_preferences = array();
	protected $_preferences_helper;
	
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
		
		$this->_cached_application_preferences = new SmartestParameterHolder('Cached application-level preferences');
		$this->_cached_global_preferences = new SmartestParameterHolder('Cached global preferences');
		
		// Applications can come bundled with their own template plugins
		if(is_dir(SM_CONTROLLER_MODULE_DIR.'Library/Templating/Plugins/')){
		    $this->getPresentationLayer()->addPluginDirectory(SM_CONTROLLER_MODULE_DIR.'Library/Templating/Plugins/');
		}
		
		if(is_dir(SM_CONTROLLER_MODULE_DIR.'Library/Data/ExtendedObjects/')){
		    SmartestDataObjectHelper::loadExtendedObjects(SM_CONTROLLER_MODULE_DIR.'Library/Data/ExtendedObjects/');
		}
		
		// load user-defined application-wide settings
		// TODO: add caching here
		if(is_file(SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml")){
			
			$this->settings->setParameter('settings', SmartestYamlHelper::toParameterHolder(SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml", SM_DEVELOPER_MODE));
			
		}
		
		if(is_file(SM_CONTROLLER_MODULE_DIR."Configuration/application.yml")){
			
			$this->settings->setParameter('application', SmartestYamlHelper::toParameterHolder(SM_CONTROLLER_MODULE_DIR."Configuration/application.yml", SM_DEVELOPER_MODE));
			
		}
		
		/////////////// MANAGERS CODE WILL BE DEPRECATED SOON - FUNCTIONALITIES IN MANAGERS ARE BEING MOVED TO HELPERS ////////////////
		// Detect to see if manager classes exist and initiate them, if configured to do so
		$managerClassFile = SM_ROOT_DIR.'Managers/'.SM_CONTROLLER_CLASS."Manager.class.php";
		$managerClass = SM_CONTROLLER_CLASS."Manager";
		
		define("SM_MANAGER_CLASS", $managerClass);
		
		if(is_file(SM_ROOT_DIR.'Managers/'.SM_CONTROLLER_CLASS."Manager.class.php")){
		
			define("SM_MANAGER_CLASS_FILE", SM_ROOT_DIR.'Managers/'.SM_CONTROLLER_CLASS."Manager.class.php");
			include_once(SM_MANAGER_CLASS_FILE);
		
			if(class_exists(SM_MANAGER_CLASS)){
				
				$this->manager = new $managerClass($this->database);
				
			}
			
		}else if(defined("SM_CONTROLLER_MODULE_DIR")){
		  
		    if(is_file(SM_CONTROLLER_MODULE_DIR.SM_CONTROLLER_CLASS."Manager.class.php")){
			
				define("SM_MANAGER_CLASS_FILE", SM_CONTROLLER_MODULE_DIR.SM_CONTROLLER_CLASS."Manager.class.php");
				include_once(SM_MANAGER_CLASS_FILE);
				
				if(class_exists(SM_MANAGER_CLASS)){
				
					$this->manager = new $managerClass($this->database);
				
				}
			
			}
			
		}
		
		$this->send(SM_CONTROLLER_DOMAIN.SM_CONTROLLER_URL, '_here');
		
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
	
	final protected function bring($data, $name=""){
	    SmartestLog::getInstance('system')->log('Deprecated function used: SmartestBaseApplication->bring(). Use SmartestBaseApplication->send()');
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
    
    /* public function getApplicationPreference($pref_name){
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
    } */
    
    protected function getUserIdOrZero(){
        if(is_object($this->getUser())){
            return $this->getUser()->getId();
        }else{
            return '0';
        }
    }
    
    protected function getSiteIdOrZero(){
        if(is_object($this->getSite())){
            return $this->getSite()->getId();
        }else{
            return '0';
        }
    }
    
    public function getApplicationPreference($preference_name){
        
        if($this->_cached_application_preferences->hasParameter($preference_name)){
            return $this->_cached_application_preferences->getParameter($preference_name);
        }else{
            $value = $this->_preferences_helper->getApplicationPreference($preference_name, $this->_application_id, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        }
        
    }
    
    public function setApplicationPreference($preference_name, $preference_value){
        
        $this->_preferences_helper->setApplicationPreference($preference_name, $preference_value, $this->_application_id, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        
    }
    
    public function getGlobalPreference($preference_name){
        
        if($this->_cached_application_preferences->hasParameter($preference_name)){
            return $this->_cached_application_preferences->getParameter($preference_name);
        }else{
            $value = $this->_preferences_helper->getApplicationPreference($preference_name, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        }
        
    }
    
    public function setGlobalPreference($preference_name, $preference_value){
        
        $this->_preferences_helper->setGlobalPreference($preference_name, $preference_value, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        
    }
    
    ///// Flow Control //////
    
    protected function redirect($to="", $exit=false){
		
		if(!$to){
			$destination = constant('SM_CONTROLLER_DOMAIN');
		}else if($to{0} == "/"){
		    if(constant('SM_CONTROLLER_DOMAIN') == '/' || substr($to, 0, strlen(constant('SM_CONTROLLER_DOMAIN'))) == constant('SM_CONTROLLER_DOMAIN')){
		        $destination = $to;
	        }else{
	            $destination = constant('SM_CONTROLLER_DOMAIN').substr($to, 1);
	        }
		}
		
		header("location:".$destination);
		if($exit){
		    exit;
		}
		
	}
    
    ///// Check for Libraries /////
    
    // TODO: Deprecate this and implement FS#172 (http://bugs.vsclabs.com/task/172)
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
            if(is_dir(SM_CONTROLLER_MODULE_DIR.'Library/Helpers/'.$helper.'.helper') && class_exists($full_helper)){
                return true;
            }else if(is_dir(SM_ROOT_DIR.'Library/Helpers/'.$helper.'.helper') && class_exists($full_helper)){
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