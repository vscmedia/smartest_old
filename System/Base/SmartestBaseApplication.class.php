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
	
	final public function __construct(){
	
		$this->database = SmartestPersistentObject::get('db:main');
		$this->_errorStack =& SmartestPersistentObject::get('errors:stack');
		$this->domain = SM_CONTROLLER_DOMAIN;
		$this->module = SM_CONTROLLER_MODULE;
		$this->_resultIndex = 0;
		$this->userTokenHelper = new SmartestUsersHelper();
		
		SmartestSession::set('user:currentApp', SM_CONTROLLER_MODULE);
		SmartestSession::set('user:currentAction', SM_CONTROLLER_METHOD);
		
		
		
		// load user-defined application-wide settings
		// TODO: add caching here
		if(is_file(SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml")){
			// if($this->_settings['application'] = @parse_ini_file(SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml")){
			$appSettingsFileData = SmartestYamlHelper::load(SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml");
			
			if(is_array($appSettingsFileData)){
			    // 
			    $this->settings['application'] = $appSettingsFileData;
			    // $this->settings['application'] = SmartestConfigurationHelper::parseConfigDataArray($appSettingsFileData, 'application');
			    // print_r($this->settings['application']);
			}else{
				throw new SmartestException("Error parsing config file: ".SM_CONTROLLER_MODULE_DIR."Configuration/settings.yml");
			}
		}
		
		// load user-defined system-wide settings
		// TODO: add caching here
		if(is_file(SM_ROOT_DIR."Configuration/user.ini")){
			if($this->_settings['global'] = @parse_ini_file(SM_ROOT_DIR."Configuration/user.ini")){
				
			}else{
				throw new SmartestException("Error parsing config file: ".SM_ROOT_DIR."Configuration/user.ini");
			}
		}
		
		if(SM_OPTIONS_MANAGERS_AUTOLOAD || SM_SYSTEM_IS_BACKEND_MODULE){
		
			// Detect to see if manager classes exist and initiate them, if configured to do so
			$managerClassFile = SM_SYSTEM_MANAGERS_DIR.SM_CONTROLLER_CLASS."Manager.class.php";
			$managerClass = SM_CONTROLLER_CLASS."Manager";
			
			define("SM_MANAGER_CLASS", $managerClass);
			
			if(@is_file(SM_ROOT_DIR.SM_SYSTEM_MANAGERS_DIR.SM_CONTROLLER_CLASS."Manager.class.php")){
			
				define("SM_MANAGER_CLASS_FILE", SM_SYSTEM_MANAGERS_DIR.SM_CONTROLLER_CLASS."Manager.class.php");
				include_once(SM_MANAGER_CLASS_FILE);
			
				if(class_exists(SM_MANAGER_CLASS)){
					
					if(SM_OPTIONS_MANAGERS_GET_AUTO_DB){
						$this->manager = new $managerClass($this->database);
					}else{
						$this->manager = new $managerClass();
					}
					
				}
				
			}else if(defined("SM_CONTROLLER_MODULE_DIR")){
			  
			  // echo SM_CONTROLLER_MODULE_DIR;
			  
				if(@is_file(SM_CONTROLLER_MODULE_DIR.SM_CONTROLLER_CLASS."Manager.class.php")){
				
					define("SM_MANAGER_CLASS_FILE", SM_CONTROLLER_MODULE_DIR.SM_CONTROLLER_CLASS."Manager.class.php");
					include_once(SM_MANAGER_CLASS_FILE);
					
					if(class_exists(SM_MANAGER_CLASS)){
					
						if(SM_OPTIONS_MANAGERS_GET_AUTO_DB){
							$this->manager = new $managerClass($this->database);
						}else{
							$this->manager = new $managerClass();
						}
					
					}
				
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
	        $this->send($this->getUser()->__toArray(), '_user');
	    }
	    
	    // print_r(SmartestCache::load('user:messages:nextRequest:'.$this->getUser()->getId(), true));
	}
	
	final public function __destruct(){
		
		if(method_exists($this, "__moduleDestruct")){
			$this->__moduleDestruct();
		}
		
		// handle user messages:
		
		/* if(count($this->_userMessages)){
			array_shift($this->_userMessages);
		} */
		
		// SmartestPersistentObject::set('user:messages:nextRequest', $this->_userMessages);
		
	}
	
	///// String Stuff //////
  
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
	protected function requireAuthenticatedUser(){
		if(!$this->_auth->getUserIsLoggedIn()){
			$this->redirect($this->domain."smartest/login");
		}
	}
	
	protected function getUser(){
	    
	    return SmartestPersistentObject::get('user');
	    
	}
	
	protected function getSite(){
	    
	    return SmartestPersistentObject::get('current_open_project');
	    
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
    		// if(!isset($this->getPresentationLayer()->_tpl_vars[$name])){
    			$this->getPresentationLayer()->assign($name, $data);
    			
    		/* }else{
    			$this->_error("A value called \"".$name."\" is already in use within the presentation layer.");
    		} */
    	}else{
    		//if(!isset($this->getPresentationLayer()->_tpl_vars["content"][$this->_resultIndex])){
    			$this->getPresentationLayer()->_tpl_vars["content"][$this->_resultIndex] = $data;
    			$this->_resultIndex++;
    		// }else{
    		//	$this->_error("A value called \"".$name."\" is already in use within the presentation layer.");
    		// }
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
    
    ///// Check for Libraries /////
    
    function isInstalled($library){
    	return SmartestLibHelper::isInstalled($library);
    }
    
    ///// Errors and Logging /////
    
    public function log($message, $type=''){
    	
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