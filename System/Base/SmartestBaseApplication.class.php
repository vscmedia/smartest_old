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

class SmartestBaseApplication extends QuinceBase{

	public $manager;
	public $_auth;
	
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
	protected $_site;
	
	private $_user_loaded_classes;
	
	public function __moduleConstruct(){
	    
	    $this->_errorStack =& SmartestPersistentObject::get('errors:stack');
		$this->_resultIndex = 0;
		$this->userTokenHelper = new SmartestUsersHelper();
		$this->settings = new SmartestParameterHolder("Application settings");
		
		SmartestSession::set('user:currentApp', $this->getRequest()->getModule());
		SmartestSession::set('user:currentAction', $this->getRequest()->getAction());
		
		$this->_preferences_helper = SmartestPersistentObject::get('prefs_helper');
		$this->_cached_application_preferences = new SmartestParameterHolder('Cached application-level preferences');
		$this->_cached_global_preferences = new SmartestParameterHolder('Cached global preferences');
		
		$this->_loadApplicationSpecificResources();
		$this->_prepareManagers();
		$this->_assignTemplateValues();
		
		$this->lookupSiteDomain();
	    
	}
	
	public function __pre(){
	    
	    $this->_callOptionalConstructors();
	    $this->_loadApplicationSpecificTemplatePlugins();
	    
	    if(SmartestSession::get('user:isAuthenticated')){
		    $this->send($this->getUser(), '_user');
	    }
	    
	    if(SmartestSession::hasData('current_open_project')){
		    
		    if(SmartestSession::get('current_open_project') instanceof SmartestSite){
    	        $this->getPresentationLayer()->assign('show_left_nav_options', true);
    	    }else{
    	        $this->getPresentationLayer()->assign('show_left_nav_options', false);
    	    }
		    
		}
		
		if(method_exists($this, 'getLocalisationFilePath') && is_file($this->getLocalisationFilePath())){
	        
	        $s = SmartestYamlHelper::fastLoad($this->getLocalisationFilePath());
	        
	        if(isset($s['strings'])){
	            $this->_l10n_strings = $s['strings'];
	            $this->send($this->_l10n_strings, '_l10n_strings');
	        }
	        
	    }else if(method_exists($this, 'getEnglishLocalisationFilePath') && is_file($this->getEnglishLocalisationFilePath())){
	        
	        $s = SmartestYamlHelper::fastLoad($this->getEnglishLocalisationFilePath());
	        
	        if(isset($s['strings'])){
	            $this->_l10n_strings = $s['strings'];
	            $this->send($this->_l10n_strings, '_l10n_strings');
	        }
	        
	    }
	    
	    if(method_exists($this, 'getActionLocalisationFilePath') && is_file($this->getActionLocalisationFilePath())){
	        
	        $s = SmartestYamlHelper::fastLoad($this->getActionLocalisationFilePath());
	        
	        if(isset($s['strings'])){
	            $this->_l10n_action_strings = $s['strings'];
	            $this->send($this->_l10n_action_strings, '_l10n_action_strings');
	        }
	        
	    }else if(method_exists($this, 'getEnglishActionLocalisationFilePath') && is_file($this->getEnglishActionLocalisationFilePath())){
	        
	        $s = SmartestYamlHelper::fastLoad($this->getEnglishActionLocalisationFilePath());
	        
	        if(isset($s['strings'])){
	            $this->_l10n_action_strings = $s['strings'];
	            $this->send($this->_l10n_action_strings, '_l10n_action_strings');
	        }
	        
	    }
		
		$this->getPresentationLayer()->assign("now", new SmartestDateTime(time()));
		$this->getPresentationLayer()->assign("domain", $this->getRequest()->getDomain());
	    $this->getPresentationLayer()->assign("section", $this->getRequest()->getModule()); // deprecated
	    $this->getPresentationLayer()->assign("module", $this->getRequest()->getModule());
	    $this->getPresentationLayer()->assign("module_dir", $this->getRequest()->getMeta('_module_dir'));
	    $this->getPresentationLayer()->assign("action", $this->getRequest()->getAction());
	    $this->getPresentationLayer()->assign("method", $this->getRequest()->getAction()); // deprecated
	    $this->getPresentationLayer()->assign("metas", $this->getRequest()->getMetas());
	    
	}
	
	public function __post(){
	    
	    $rp = new SmartestParameterHolder('Final request parameters');
	    $rp->loadArray($this->getRequest()->getRequestParameters());
	    $this->getPresentationLayer()->assign('request_parameters', $rp);
	    
	}
	
	public function lookupSiteDomain(){
	    
	    if((!$this->isSystemApplication()) || $this->isWebsitePage()){
		    
		    $rh = new SmartestRequestUrlHelper;
		    
		    try{
                
                if($this->_site = $rh->getSiteByDomain($_SERVER['HTTP_HOST'], $this->getRequest()->getRequestStringWithVars())){
                    return true;
        	    }else{
        	        return false;
        	    }

            }catch(SmartestRedirectException $e){
                $e->redirect();
            }
		    
		}
	    
	}
	
	private function _callOptionalConstructors(){
	    
	    // Called by all system applications
	    if($this->isSystemApplication() && method_exists($this, "__systemModulePreConstruct")){
		    $this->__systemModulePreConstruct();
	    }
	    
	    // Called by the individual applications
	    if(method_exists($this, "__smartestApplicationInit")){
		    $this->__smartestApplicationInit();
	    }
	    
	}
	
	private function _loadSettings(){
	    
	    // load user-defined application-wide settings
		// TODO: add caching here
		if(is_file($this->getRequest()->getMeta('_module_dir')."Configuration/settings.yml")){
			
			$this->settings->setParameter('settings', SmartestYamlHelper::toParameterHolder($this->getRequest()->getMeta('_module_dir')."Configuration/settings.yml", SM_DEVELOPER_MODE));
			
		}
		
		if(is_file($this->getRequest()->getMeta('_module_dir')."Configuration/application.yml")){
			
			$this->settings->setParameter('application', SmartestYamlHelper::toParameterHolder($this->getRequest()->getMeta('_module_dir')."Configuration/application.yml", SM_DEVELOPER_MODE));
			
		}
	    
	}
	
	private function _loadApplicationSpecificResources(){
	    
	    $this->_loadSettings();
	    
	    if(is_dir($this->getRequest()->getMeta('_module_dir').'Library/Data/ExtendedObjects/')){
		    SmartestDataObjectHelper::loadExtendedObjects($this->getRequest()->getMeta('_module_dir').'Library/Data/ExtendedObjects/');
		}
	    
	}
	
	private function _loadApplicationSpecificTemplatePlugins(){
	    
	    // Applications can come bundled with their own template plugins
		if(is_dir($this->getRequest()->getMeta('_module_dir').'Library/Templating/Plugins/')){
		    $this->getPresentationLayer()->addPluginDirectory($this->getRequest()->getMeta('_module_dir').'Library/Templating/Plugins/');
		}
	    
	}
	
    private function _assignTemplateValues(){
	    
	    // print_r($this->getPresentationLayer());
	    
	}
	
	private function _prepareManagers(){
	    
	    /////////////// MANAGERS CODE WILL BE DEPRECATED SOON - FUNCTIONALITIES IN MANAGERS ARE BEING MOVED TO HELPERS ////////////////
		// Detect to see if manager classes exist and instantiate them, if configured to do so
		
		$managerClass = $this->getRequest()->getMeta('_module_php_class')."Manager";
		
		if(is_file($this->getRequest()->getMeta('_module_dir').$managerClass.".class.php")){
			
			include_once($this->getRequest()->getMeta('_module_dir').$managerClass.".class.php");
			
			if(class_exists($managerClass)){
			
				$this->manager = new $managerClass(SmartestDatabase::getInstance('SMARTEST'));
			
			}
			
		}
	    
	}
	
	protected function requestParameterIsSet($p){
	    return $this->getRequest()->hasRequestParameter($p);
	}
	
	protected function getRequestParameter($p, $default=''){
	    return $this->getRequest()->getRequestParameter($p, $default);
	}
	
	protected function setRequestParameter($p, $v){
	    return $this->getRequest()->setRequestParameter($p, $v);
	}
	
	protected function getRequestParameters(){
	    return $this->getRequest()->getRequestParameters();
	}
	
	protected function getLocalisationFilePath(){
	    return $this->getRequest()->getMeta('_module_dir').'Configuration/strings.yml';
	}
	
	final public function __destruct(){
		
		if(method_exists($this, "__moduleDestruct")){
			$this->__moduleDestruct();
		}
		
	}
	
	final public function isSystemApplication(){
	    
	    return ((bool) $this->getRequest()->getMeta('system') ? true : false) && ($this instanceof SmartestSystemApplication);
	    
	}
	
	final public function isWebsitePage(){
	    
	    $sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
	    $websiteMethodNames = $sd['system']['content_interaction_methods'];
	    $method = $this->getRequest()->getModule().'/'.$this->getRequest()->getAction();
	    return in_array($method, $websiteMethodNames);
	    
	}
	
	final public function isPublicMethod(){
	    
	    $sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		$publicMethodNames = $sd['system']['public_methods'];
		$method = $this->getRequest()->getModule().'/'.$this->getRequest()->getAction();
		return in_array($method, $publicMethodNames);
	    
	}
	
	public function requireSiteByDomain($domain){
	    
	    if($this->getSite()->getDomain() == $domain){
	        
	        if(!defined('SM_CMS_PAGE_SITE_ID')){
	            define('SM_CMS_PAGE_SITE_ID', $this->getSite()->getId());
    	        define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->getSite()->getUniqueId());
	        }
	        
	        return true;
	        
	    }else{
	        // This forces a not found page
	        $this->forward('website', 'renderPage');
	    }
	    
	}
	
	///// Authentication Stuff /////
	
	protected function getUser(){
	    
	    return SmartestSession::get('user');
	    
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
    		$this->getPresentationLayer()->_tpl_vars["data"][$this->_resultIndex] = $data;
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
    
    protected function getSite(){
        return $this->_site;
    }
    
    protected function getApplicationPreference($preference_name, $default=null){
        
        $name = SmartestStringHelper::toVarName($preference_name);
        
        if($this->_cached_application_preferences->hasParameter($name)){
            $value = $this->_cached_application_preferences->getParameter($name);
        }else{
            $value = $this->_preferences_helper->getApplicationPreference($name, $this->getRequest()->getMeta('_module_identifier'), $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        }
        
        if(isset($value) && strlen($value)){
            $this->_cached_application_preferences->setParameter($name, $value);
            return $value;
        }else{
            return $default;
        }
        
    }
    
    protected function setApplicationPreference($preference_name, $preference_value){
        
        $name = SmartestStringHelper::toVarName($preference_name);
        return $this->_preferences_helper->setApplicationPreference($name, $preference_value, $this->getRequest()->getMeta('_module_identifier'), $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        
    }
    
    protected function getGlobalPreference($preference_name){
        
        $name = SmartestStringHelper::toVarName($preference_name);
        
        if($this->_cached_global_preferences->hasParameter($name)){
            return $this->_cached_global_preferences->getParameter($name);
        }else{
            $value = $this->_preferences_helper->getGlobalPreference($name, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
            $this->_cached_global_preferences->setParameter($name, $value);
            return $value;
        }
        
    }
    
    protected function setGlobalPreference($preference_name, $preference_value){
        
        $name = SmartestStringHelper::toVarName($preference_name);
        return $this->_preferences_helper->setGlobalPreference($name, $preference_value, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        
    }
    
    public function preferences(){
        
        if(!is_file($this->getRequest()->getMeta('_module_dir').'Configuration/preferences.yml')){
            $this->formForward();
        }
        
    }
    
    public function updatePreferences(){
        
        $this->formForward();
        
    }
    
    ///// Flow Control //////
    
    /* protected function redirect($to="", $exit=false, $http_code=303){
		
		$d = $this->getRequest()->getDomain();
		
		if(!$to){
			$destination = constant($d);
		}else if($to{0} == "/"){
		    if($this->getRequest()->getDomain() == '/' || substr($to, 0, strlen(constant('SM_CONTROLLER_DOMAIN'))) == constant('SM_CONTROLLER_DOMAIN')){
		        $destination = $to;
	        }else{
	            $destination = $d.substr($to, 1);
	        }
		}
		
		$r = new SmartestRedirectException($destination);
		$r->redirect($http_code, $exit);
		
		// header("location:".$destination);
		if($exit){
		    exit;
		}
		
	} */
    
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
    
    ///// Cookies /////
    
    public function getCookie($name){
        
        /* if(isset($_COOKIE[$name])){
            return urldecode($_COOKIE[$name]);
        }else{
            return null;
        } */
        
        return SmartestCookiesHelper::getCookie($name);
        
    }
    
    public function setCookie($name, $value, $duration=30, $domain='_C', $secure=false){ // default duration is 30 days
        
        return SmartestCookiesHelper::setCookie($name, $value, $duration, $domain, $secure);
        
        /* $expire = time() + 86400 * (int) $duration; // 86400 is the number of seconds in one day
        $domain = ($domain == '_C') ? '.'.$_SERVER['HTTP_HOST'] : $domain;
        return setcookie($name, $value, $expire, $this->getRequest()->getDomain(), $domain, (bool) $secure); */
        
    }
    
    public function clearCookie($name, $domain='_C', $secure=false){
        
        return SmartestCookiesHelper::clearCookie($name, $domain, $secure);
        
        /* $expire = time() - 86400; // now, minus one day
        $domain = ($domain == '_C') ? '.'.$_SERVER['HTTP_HOST'] : $domain;
        return setcookie($name, '', $expire, $this->getRequest()->getDomain(), $domain, (bool) $secure); */
        
    }

}