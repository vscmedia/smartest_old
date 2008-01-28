<?php

// print_r(mb_get_info());

mb_http_output("UTF-8");
mb_http_input("UTF-8");
// mb_detect_order("UTF-8, ISO-8859-1, ASCII");
mb_internal_encoding("UTF-8");

// echo "é";

// These two files can't be included using the include optimisation because it depends on their already having been included

define('SM_START_TIME', microtime(true));

require SM_ROOT_DIR.'System/Data/SmartestCache.class.php';
require SM_ROOT_DIR.'System/Helpers/SmartestHelper.class.php';

SmartestHelper::loadAll();



SmartestFileSystemHelper::include_group(

	'System/Base/SmartestException.class.php',
	'System/Base/SmartestError.class.php',
	'System/Base/SmartestErrorStack.class.php',
	'System/Response/SmartestUserMessage.class.php',
	'System/Data/SmartestSession.class.php',
	'System/Data/SmartestPersistentObject.class.php',
	'System/Data/SmartestDataAccessClass.interface.php',
	'System/Response/SmartestResponseDataHolder.class.php',
	'System/Data/SmartestMysql.class.php',
	'System/Data/SmartestSqllite.class.php',
	'System/Data/SmartestCacheDb.class.php',
	'System/Data/SmartestCmsItem.class.php',
	'System/Data/SmartestDataUtility.class.php',
	'System/Data/SmartestFile.class.php'

);

include 'PEAR.php';
include 'XML/Unserializer.php';
include 'XML/Serializer.php';

SmartestDataUtility::loadTypeObjects();
SmartestDataUtility::loadBasicObjects();
SmartestDataUtility::loadExtendedObjects();

// include SM_ROOT_DIR.'Libraries/Plugins/SmartestXml/SmartestXmlSerializer.class.php';

SmartestFileSystemHelper::include_group(

	'System/Data/DataQuery.class.php',
	'System/Data/SmartestQuery.class.php',
	'System/Data/SmartestQueryResultSet.class.php',
	'System/Data/SmartestObjectModelHelper.class.php',
	'System/Data/SmartestGenericListedObject.class.php',
	'Library/Quince/Quince.class.php',
	'Library/Quince/QuinceException.class.php',
	'Library/Quince/QuinceBase.interface.php',
	'System/Templating/SmartestEngine.class.php',
	'System/Templating/SmartyManager.class.php',
	'System/Controller/SmartestController.class.php',
	'System/Templating/SmartestTemplateHelper.class.php',
	'System/Base/SmartestBaseProcess.class.php',
	'System/Base/SmartestBaseApplication.class.php',
	'Library/API/SmartestApplication.class.php',
	'Library/API/SmartestUser.class.php'

);

class SmartestResponse{
	
	// Main controller object
	private $controller;
	
	// Templating object
	private $smarty;
	
	// Name of the template family, exactly as written in controller.xml
	private $template;
	
	// Error Stack
	public $errorStack;
	
	// The actual template file being included (has method name and ".tpl" appended)
	public $templateFile;
	
	// The object that handles requests for assetclasses, images, etc. *very* important.
	var $templateHelper;
	
	// The current controller method
	var $method;
	
	// The current controller section/page
	var $section;
	
	// The controller $domain
	var $domain;
	
	// An object for browser sniffing
	var $browser;
	
	// The name of the current class instantiated by the controller as per controller.xml
	var $userClass;
	
	// The result of the current method
	var $content;
	
	// A "Cleaned" version of the $_GET string
	var $get;
	
	// The database/data-access object
	var $database;
	
	// The database/data-access object
	var $database_sqllite;
	
	// The settings from Configuration/options.ini
	var $userOptions;
	
	// The settings from Configuration/system.ini
	var $systemOptions;
	
	// The settings from Configuration/database.ini
	var $dbconfig;
	
	// Time in milliseconds at start of pageload
	var $startTime;
	
	// Time in milliseconds at end of pageload
	var $endTime;
	
	// Resulting time taken/overhead
	var $timeTaken;
	
	// Resulting time taken/overhead, including template parse.
	var $fullTimeTaken;
	
	// Resulting time taken/overhead by controller prior to calling user action
	var $controllerPrepareTimeTaken;
	
	// Resulting time taken/overhead by controller including calling user action
	var $controllerActionTimeTaken;
	
	// Settings manager object
	var $configuration;
	
	// Methods that don't require authentication
	private $publicMethodNames = array("renderPageFromUrl", "renderPageFromId", "doAuth", "doLogOut", "searchDomain", "renderSiteTagSimpleRssFeed", "downloadAsset");
	
	// Filter Chain
	private $filters = array();
	
	// Files that need to be editable, but aren't
	var $unwritableFiles;
	
	// Files that need not to be editable, but are
	var $writableFiles;
	
	// Files that need to exist, but don't
	var $missingFiles;
	
	// measuring units
	var $measuringUnits;
	
	// Authentication object
	var $authentication;
	
	// URL
	var $url;
	
	// Log
	var $log = array();
	
	var $userInterfaceTemplate;
	
	function __construct(){
		
		$this->startTime = SM_START_TIME;
		// echo $this->startTime;
		
	}
	
	function init(){
		
		@session_start();
		
		// print_r(SmartestPersistentObject::getRegisteredNames(SmartestSession::NOTFALSE));
		// print_r($_SESSION);
		
		$this->_log("Session started");
		$this->errorStack = new SmartestErrorStack();
		
		SmartestPersistentObject::set('errors:stack', $this->errorStack);
		SmartestPersistentObject::set('centralDataHolder', new SmartestResponseDataHolder);
		
		// print_r(SmartestSession::clearAll(true));
		
		$this->checkRequiredExtensionsLoaded();
		$this->checkRequiredFilesExist();
		$this->checkWritablePermissions();
		
		// load database connection settings
		if(SmartestCache::hasData('dbconfig', true)){
			$this->dbconfig = SmartestCache::load('dbconfig', true);
			$this->_log("Database settings loaded from disk cache.");
		}else{
			$dbconfig = parse_ini_file(SM_ROOT_DIR."Configuration/database.ini");
			$this->dbconfig = $dbconfig;
			SmartestCache::save('dbconfig', $dbconfig, -1, true);
			$this->_log("Database settings loaded from ".SM_ROOT_DIR."Configuration/database.ini.");
		}
		
		// print_r($this->dbconfig);
		
		// load up settings
		$this->configuration = new SmartestConfigurationHelper();
		
		// make sure they're loaded every time if we're doing dev
		if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			$this->configuration->flushAll();
		}
		
		// load user-defined options
		try{
			$this->userOptions = $this->configuration->getUserOptions();
		} catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		// load system-essential settings
		// this will probably be moved to sqlite
		try{
			$this->systemOptions   = $this->configuration->getSystemOptions();
		} catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		// load measuring units
		try{
			$this->measuringUnits  = $this->configuration->getMeasuringUnits();
		} catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		$this->_log("System settings and options loaded.");
		
		// instantiate database object
		try{
			$mysql = new SmartestMysql($this->dbconfig['host'], $this->dbconfig['username'], $this->dbconfig['database'], $this->dbconfig['password']);
			// var_dump($mysql);
			// $_SESSION['database'] = $mysql;
			SmartestPersistentObject::set('db:main', $mysql);
			// var_dump(SmartestPersistentObject::get('db:main'));
		} catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
	    }
		
		// instantiate sqlite database object
		try{
			// $sql_main = new SmartestSqllite();
			// $_SESSION['sqllite'] = $sql_main;
			// SmartestPersistentObject::set('db:sqlite:user', $sql_main);
		} catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		// instantiate cache sqlite database object
		try{
			// $_SESSION['cache_db'] = new SmartestCacheDb();
			// SmartestPersistentObject::set('db:sqlite:cache', $sql_main);
		} catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		$this->database =& SmartestPersistentObject::get('db:main');
		
		// Instantiate user auth object
		$this->authentication = new SmartestAuthenticationHelper();
		
		// Instantiate browser object
		$this->browser = new SmartestUserAgentHelper();
		SmartestPersistentObject::set('userAgent', $this->browser);
		
		// Todo: Re-Write Data access class as a wrapper of Pear MDB2
		// $this->database = new Database("Configuration/database.xml");
		// $this->database->connect();
		
		// instantiate controller
		
		$this->_log("Starting controller...");
		
		// var_dump(SmartestSession::get('user:isAuthenticated'));
		
		$this->controller = new SmartestController(SM_ROOT_DIR."Configuration/controller.xml", false);
		
		$this->controller->addProperty('_auth', $this->authentication);
		$this->controller->dispatch();
		
		SmartestPersistentObject::set('controller', $this->controller);
		
		// Everything after the hostname and before the query qtring
		define("SM_CONTROLLER_URL", $this->controller->getRequest());
		
		/* if(!is_object(SmartestPersistentObject::get('user'))){
		    $user = new SmartestUser;
		    $user->hydrate(SmartestSession::get('user:array'));
		    print_r($user);
		} */
		
		// print_r(SmartestSession::getRegisteredNames(SmartestSession::OBJECTS));
		
		$this->errorStack->display();
		
	}
	
	function error($message="", $type=100){
    	$this->errorStack->recordError($message, $type);
    }
    
    function setPersistentObject(){}
	
	function isSystemClass(){
		
		// var_dump(constant("SM_SYSTEM_RESERVED_CLASSES"));
		
		$reservedClassNames = explode(",", constant("SM_SYSTEM_RESERVED_CLASSES"));
		
		if(in_array($this->controller->getClassName(), $reservedClassNames)){
			if(!defined("SM_SYSTEM_IS_BACKEND_MODULE")){
				define("SM_SYSTEM_IS_BACKEND_MODULE", true);
			}
			
			return true;
		}else{
			if(!defined("SM_SYSTEM_IS_BACKEND_MODULE")){
				define("SM_SYSTEM_IS_BACKEND_MODULE", false);
			}
			
			return false;
		}
	}
	
	function build(){
		
		// require_once(SM_ROOT_DIR.'System/Templating/SmartyManager.class.php');
		
		$this->_log("Starting presentation layer...");
		
		$smarty_manager = new SmartyManager();
		
		try{
			$this->smarty = $smarty_manager->initialize();
		} catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		SmartestPersistentObject::set('presentationLayer', $this->smarty);
		
		try{
		    $this->checkAuthenicationStatus();
		}catch (SmartestException $e){
		    // $this->redirect($e->getRedirectUrl());
		    $this->redirect($this->controller->getDomainName().'smartest/login');
		}
		
		// Assign a bunch of important values for use throughout Smartest
		
		$this->section = $this->controller->getSectionName();
		$this->smarty->assign("section", $this->section);
		
		$this->module = $this->controller->getModuleName();
		$this->smarty->assign("module", $this->module);
		
		if(method_exists($this->controller,'getModuleDirectory')){
			$this->moduleDir = $this->controller->getModuleDirectory();
			$this->smarty->assign("module_dir", $this->controller->getModuleDirectory());
		}
		
		$this->method = $this->controller->getMethodName();
		$this->smarty->assign("method", $this->controller->getMethodName());
		
		$this->domain = $this->controller->getDomainName();
		$this->smarty->assign("domain", $this->domain);
		
		$this->userClass = $this->controller->getClassName();
		$this->smarty->assign("class", $this->userClass);
		define("SM_CONTROLLER_CLASS", $this->userClass);
		
		$this->template = $this->controller->getTemplateName();
		
		$this->url = $this->getUrl();
		$this->controller->addProperty('url', $this->url);
		
		$this->isSystemClass();
		
		$this->smarty->assign("sm_admin_email", SM_OPTIONS_ADMIN_EMAIL);
		$this->smarty->assign("sm_user_agent", $this->browser->getSimpleClientSideObjectAsJson());
		
		// These constants will be phased out once the controller supports non-redirect forwarding (changing module and action and then re-calling)
		define("SM_CONTROLLER_SECTION", $this->controller->getModuleName());
		define("SM_CONTROLLER_MODULE", $this->controller->getModuleName());
		define("SM_CONTROLLER_MODULE_DIR", SM_ROOT_DIR.$this->controller->getModuleDirectory());
		define("SM_CONTROLLER_METHOD", $this->controller->getMethodName());
		define("SM_CONTROLLER_DOMAIN", $this->controller->getDomain());
		define("SM_CONTROLLER_TEMPLATE_FAMILY", $this->controller->getTemplateName());
		define("SM_CONTROLLER_TEMPLATE_FILE", $this->templateFile);
		
		$this->errorStack->display();
		
		switch($this->controller->getNamespace()){
		    case 'ui':
		        header('Content-Type: text/html; charset=utf-8');
		        break;
		    case 'json':
		        header('Content-Type: text/html; charset=utf-8');
		        break;
		    default:
		        header('Content-Type: text/html; charset=utf-8');
		        break;
	    }
		
		$this->initializeTemplates();
		
		// get "clean" $_GET vars array
		// $this->get = $this->controller->getGetVariables();
		
		// Look to see if the current class is part of system functionality or part of the user's web application(s)
		// If it is the former, require that the user is logged in.
		
		// Instantiate templating helper object that deals with getting asset classes, links, images, etc.
		$this->templateHelper = new SmartestTemplateHelper($this->controller->getGetVariables());
		
		$this->errorStack->display();
		
		// Execute the controller method
		$this->prepareContent();
		
	}
	
	function checkAuthenicationStatus(){
	    
	    if($this->isSystemClass() && !in_array($this->controller->getMethodName(), $this->publicMethodNames)){
		    
		    // echo 'restricted';
		    // print_r(SmartestPersistentObject::get('user'));
			// var_dump($this->authentication->getUserIsLoggedIn());
		    
		    if(!$this->authentication->getUserIsLoggedIn()){
				if(SM_CONTROLLER_URL != "smartest/login"){
					$new_url = $this->controller->getDomain()."smartest/login?from=/".SM_CONTROLLER_URL;
					
					if(isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING'])){
						$new_url .= '&'.$_SERVER['QUERY_STRING'];
					}
					
					// echo $new_url;
					
					$e = new SmartestException('The user must be logged in to see this page.');
					$e->setRedirectUrl($new_url);
					throw $e;
					
				}
			}
		}
	}
	
	function initializeTemplates(){
		
		define('SM_CONTROLLER_MODULE_PRES_DIR', SM_CONTROLLER_MODULE_DIR.'Presentation/');
		
		/* if(SM_SYSTEM_IS_BACKEND_MODULE){ 
		    
		    if(SM_CONTROLLER_MODULE != 'website' && $this->browser->isExplorer() && $this->browser->isMacintosh()){
		        // the administration part of Smartest isn't supported in IE for Mac
			    include SM_ROOT_DIR."System/Response/ErrorPages/mac_ie.php";
				exit;
			}
			
			// if the current class is part of system functionality
			// tell smarty to look at the system interface templates
			
			// $system_interface = (strlen($this->method)) ? SM_SYSTEM_SYS_TEMPLATES_DIR.SM_CONTROLLER_TEMPLATE_FAMILY."/".SM_CONTROLLER_METHOD.".tpl" : null;
			$system_interface = (strlen($this->method)) ? SM_CONTROLLER_MODULE_DIR.'Presentation/'.SM_CONTROLLER_METHOD.".tpl" : null;
			
			if(is_file($system_interface)){
				$this->smarty->assign("sm_system_interface", $system_interface);
			}else{
				$this->smarty->assign("sm_system_interface", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_subTemplateNotFound.tpl");
				$this->smarty->assign("sm_intended_interface", $system_interface);
			}
			
			// $this->templateFile = SM_SYSTEM_SYS_TEMPLATES_DIR.$this->controller->getTemplateName()."/_default.tpl";
			$this->templateFile = SM_CONTROLLER_MODULE_DIR.'Presentation/_default.tpl';
			
			if(!is_file($this->templateFile)){
				$this->smarty->assign("sm_main_interface", $this->templateFile);
				$this->templateFile = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_templateNotFound.tpl";
			}
			
		}else{ */
			
			// tell smarty to look for user templates
			// $this->smarty->assign("sm_interface", (strlen($this->method)) ? SM_SYSTEM_APP_TEMPLATES_DIR.SM_CONTROLLER_TEMPLATE_FAMILY."/".SM_CONTROLLER_METHOD.".tpl" : null);
			
			// $user_interface = (strlen($this->method)) ? SM_SYSTEM_APP_TEMPLATES_DIR.SM_CONTROLLER_TEMPLATE_FAMILY."/".SM_CONTROLLER_METHOD.".tpl" : null;
			$user_interface = (strlen($this->method)) ? SM_CONTROLLER_MODULE_DIR.'Presentation/'.SM_CONTROLLER_METHOD.".tpl" : null;
			
			if(is_file($user_interface)){
				$this->smarty->assign("sm_interface", $user_interface);
			}else{
				$this->smarty->assign("sm_interface", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_subTemplateNotFound.tpl");
				$this->smarty->assign("sm_intended_interface", $user_interface);
			}
			
			// $this->templateFile = SM_SYSTEM_APP_TEMPLATES_DIR.$this->template."/_default.tpl";
			$this->templateFile = SM_CONTROLLER_MODULE_DIR.'Presentation/_default.tpl';
			
			if(!is_file($this->templateFile)){
				$this->smarty->assign("sm_main_interface", $this->templateFile);
				$this->templateFile = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_templateNotFound.tpl";
			}
		// }
        
        // echo $user_interface;
        
        $this->userInterfaceTemplate = $user_interface;
        $this->smarty->assign("template", $this->templateFile);
		$this->smarty->assign("sm_app_templates_dir", SM_SYSTEM_APP_TEMPLATES_DIR);
		$this->smarty->assign("sm_navigation", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Backend/navigation.tpl");
		$this->smarty->assign("sm_header", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Backend/header.tpl");
		$this->smarty->assign("sm_frame", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Backend/frame.tpl");
		$this->smarty->assign("sm_footer", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Backend/footer.tpl");
		
	}
	
	public function isWebsitePage(){
	    
	    return in_array(SM_CONTROLLER_METHOD, array('renderPageFromUrl', 'renderPageFromId', 'renderEditableDraftPage'));
	    
	}
	
	private function checkRequiredExtensionsLoaded(){
		
		$extensions = get_loaded_extensions();
		
		// print_r($extensions);
		
		$dependencies = array(
			"PDO",
			"pdo_mysql",
			"pdo_sqlite",
			"json",
			"curl",
			"xmlreader",
			"xml",
			"mysql",
		);
		
		foreach($dependencies as $dep){	
			if(!in_array($dep, $extensions)){
				$this->error("The PHP extension \"".$dep."\" is not installed or failed to load.", SM_ERROR_PHP);
			}
		}
		
	}
	
	function checkRequiredFilesExist(){
		
		$needed_files = array(
			"Main Controller XML" => SM_ROOT_DIR."Configuration/controller.xml",
			"System Configuration File" => SM_ROOT_DIR."Configuration/system.ini",
			"Options Configuration File" => SM_ROOT_DIR."Configuration/options.ini",
			"Smarty Configuration File" => SM_ROOT_DIR."Configuration/smarty.ini",
			"Database Configuration File" => SM_ROOT_DIR."Configuration/database.ini"
		);
		
		$errors = array();
		
		foreach($needed_files as $label=>$file){
			if(!is_file($file) || !is_readable($file)){
				$errors[] = array("label"=>$label, "file"=>$file);
			}
		}
		
		if(count($errors) > 0){
			$this->missingFiles = $errors;
			
			foreach($this->missingFiles as $missing_file){
				$this->error("The required file \"".$missing_file['file']."\" doesn't exist or isn't readable.", SM_ERROR_FILES);
			}
			
			return false;
		}else{
			return true;
		}
	}
	
	function checkWritablePermissions(){
		
		// print_r($this->smarty);
		
		$writable_files = array(
			"System Core Info Directory" => SM_ROOT_DIR."System/Core/Info/",
			"Smartest Engine Cache"      => SM_ROOT_DIR."System/Cache/SmartestEngine/",
			"Smarty Cache"               => SM_ROOT_DIR."System/Cache/Smarty/",
			"CMS Pages Cache"            => SM_ROOT_DIR."System/Cache/Pages/",
			"Data Cache"                 => SM_ROOT_DIR."System/Cache/Data/",
			"Logic Cache"                => SM_ROOT_DIR."System/Cache/Includes/",
			"Auto-generated Objects"     => SM_ROOT_DIR."System/Cache/ObjectModel/Models/",
			"System-saved settings"      => SM_ROOT_DIR."System/Cache/Settings/",
			"User-editable objects"      => SM_ROOT_DIR."Library/ObjectModel/",
			"Documents Folder"           => SM_ROOT_DIR."Documents/",
			"Deleted Files Folder"       => SM_ROOT_DIR."Documents/Deleted/"
		);
		
		$errors = array();
		
		foreach($writable_files as $label=>$file){
			if(!is_writable($file)){
				$errors[] = array("label"=>$label, "file"=>$file);
			}
		}
		
		if(count($errors) > 0){
			$this->unwritableFiles = $errors;
			
			foreach($this->unwritableFiles as $unwritable_file){
				if(is_file($unwritable_file['file'])){
					$this->error("The file \"".$unwritable_file['file']."\" needs to be writable.", SM_ERROR_PERMISSIONS);
				}else{
					$this->error("The directory \"".$unwritable_file['file']."\" needs to be writable.", SM_ERROR_PERMISSIONS);
				}
				
			}
			
			return false;
		}else{
			return true;
		}
		
	}
	
	function checkUnwritablePermissions(){
		
		$writable_files = array(
			"System Core Info Directory" => SM_ROOT_DIR.$this->controller->getClassFilePath()
		);
		
		$errors = array();
		
		foreach($writable_files as $label=>$file){
			if(is_writable($file)){
				$errors[] = array("label"=>$label, "file"=>$file);
			}
		}
		
		if(count($errors) > 0){
			
			$this->unwritableFiles = $errors;
			
			foreach($this->unwritableFiles as $unwritable_file){
				if(is_file($unwritable_file['file'])){
					$this->error("The file \"".$unwritable_file['file']."\" must not be writable by the web server.", SM_ERROR_PERMISSIONS);
				}else{
					$this->error("The directory \"".$unwritable_file['file']."\" must not be writable by the web server.", SM_ERROR_PERMISSIONS);
				}
				
			}
			
			return false;
		}else{
			return true;
		}
	}
	
	function getUrl(){
		
		$actual = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		
		$controller_url = $this->domain;
		
		$remaining = "/".substr($actual, strlen($controller_url));
		
		// echo $controller_url.'<br />';
		
		$getUrl = preg_match("/^\/([^\?]+)\??.*/i", $remaining, $matches);
		
		if(isset($matches[1])){
			return $matches[1];
		}else{
			return null;
		}
	}
	
	function prepareContent(){
		
		// $this->checkUnwritablePermissions();
		
		try{
			SmartestQuery::init();
		}catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		try{
			$this->controller->registerProcess(SmartestController::APPLICATION);
		}catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		// print_r($this->controller->getDebugContent());
		
		try{
			$this->controller->performAction();
		}catch(SmartestException $e){
			$this->error($e->getMessage(), $e->getCode());
		}
		
		// print_r($this->database);
		
		$this->errorStack->display();
			
		// retrieve the result
		$this->content =  $this->controller->getContent();
			
		$this->controller->setDebugLevel(0);
		$debug_info = $this->controller->getDebugContent();
		
		// print_r($this->controller->getDebugContent()); // 渡辺香津美.html
		
		$this->smarty->assign('sm_messages', $this->controller->getUserActionObject()->getUserMessages());
		
		// print_r($this->controller->getUserActionObject()->getUserMessages());
		// print_r(SmartestSession::get('user:messages:nextRequest'));
		
		if(defined('SM_CONTROLLER_DEBUG_JS') && defined('SM_CONTROLLER_DEBUG_HTML')){
			$this->smarty->assign("sm_controllerDebugJs", SM_CONTROLLER_DEBUG_JS);
			$this->smarty->assign("sm_controllerDebugHtml", SM_CONTROLLER_DEBUG_HTML);
		}
		
		// if $content is an array, loop through it assigning each element to smarty and making it easier to interract with
		if(is_array($this->content)){
			foreach($this->content as $varname=>$value){
				if(!isset($this->smarty->_tpl_vars[$varname]) || !strlen($this->smarty->_tpl_vars[$varname])){
					$this->smarty->assign($varname, $value);
				}
			}
		}
		
		$this->smarty->assign("content", $this->content);
		
	}
	
	function getConstants($keys=false){
		$all_constants = get_defined_constants();
		
		// print_r($all_constants);
		
		$smartest_constants = array();
		
		foreach ($all_constants as $constant_name=>$constant_value){
			if(substr($constant_name, 0, 3) == 'SM_'){
				$smartest_constants[$constant_name] = $constant_value;
			}
		}
		
		if($keys == true){
			return array_keys($smartest_constants);
		}else{
			return $smartest_constants;
		}
	}
	
	function getClasses($keys=false){
		$all_classes = get_declared_classes();
		
		// print_r($all_constants);
		
		$smartest_classes = array();
		
		foreach ($all_classes as $class_name=>$class_value){
			if(substr($class_value, 0, 8) == 'Smartest'){
				$smartest_classes[] = $class_value;
			}
		}
		
		$smartest_classes[] = $this->controller->getClassName();
		
		if($keys == true){
			return array_keys($smartest_classes);
		}else{
			return $smartest_classes;
		}
	}
	
	function getPagePreviewHtml(){
	    
	    // echo $this->fullTimeTaken;
	    
		if(defined("SM_DEVELOPER_MODE") && @SM_DEVELOPER_MODE == true && SM_CONTROLLER_MODULE == "website" && SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			$html = "<div class=\"smartest_preview_top_bar\">";
			// $html .= "&nbsp;Controller Overhead: ".$this->controllerPrepareTimeTaken."ms | ";
			$html .= "Smartest Pre-Render Overhead: ".$this->timeTaken."ms (of which controller: ".$this->controllerPrepareTimeTaken."ms) | ";
			$html .= "Page Build Time: ".($this->fullTimeTaken - $this->timeTaken)."ms | ";
			$html .= "Total time taken: ".$this->fullTimeTaken."ms";
			// $html .= "<a href=\"".SM_CONTROLLER_DOMAIN."websitemanager/getPageAssets?page_id=".SM_PAGE_WEBID."\">Edit Page Assets</a> | ";
			// $html .= "<a href=\"".SM_CONTROLLER_DOMAIN."websitemanager/editPage?page_id=".SM_PAGE_WEBID."\">Edit Page Properties</a> | ";
			// $html .= "<a href=\"#\" onclick=\"document.getElementById('smartest_preview_top_bar').style.display = 'none';\">Hide</a>\n";
			$html .= "</div>\n";
			return $html;
		}else{
			return null;
		}
	}
	
	function _log($message){
		$time = number_format(microtime(true)*100000, 0, ".", "");
		$this->log[$time] = "Smartest: ".$message;
	}
	
	function getUnfilteredOutput($fragment_only = false){
		
		// echo $this->templateFile;
		
		if($this->content == Quince::NODISPLAY){
			$output = "";
		}else{
		    if($fragment_only){
			    $output = $this->smarty->fetch($this->userInterfaceTemplate);
		    }else{
		        $output = $this->smarty->fetch($this->templateFile);
		    }
		}
		
		return $output;
	}
	
	function executeFilterChain($html){
		
		preg_match('/<body[^>]*?'.'>/i', $html, $match);
		
		if(!empty($match[0])){
			$body_tag = $match[0];
		}else{
			$body_tag = '';
		}
		
		if(SM_CONTROLLER_METHOD == "renderPageFromUrl" || SM_CONTROLLER_METHOD == "renderPageFromId"){
			$creator = "\n<!--Powered by Smartest(TM) Web Platform-->\n";
		}else{
			$creator = "";
		}
		
		$preview_html = $this->getPagePreviewHtml();
		
		$html = str_replace($body_tag, $body_tag.$creator.$preview_html, $html);
		
		if(SM_CONTROLLER_METHOD == "renderPageFromUrl" || SM_CONTROLLER_METHOD == "renderPageFromId"){
			$html = str_replace('</body>', "<!--Page returned in: ".$this->fullTimeTaken."ms -->\n</body>", $html);
		}
		
		if(defined("SM_DEVELOPER_MODE") && @SM_DEVELOPER_MODE == true && SM_CONTROLLER_MODULE == "website" && SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
			$preview_css = '	<link rel="stylesheet" href="'.SM_CONTROLLER_DOMAIN.'Resources/System/Stylesheets/sm_preview_main.css" />
	<!--[if IE 6]>
	<link rel="stylesheet" href="'.SM_CONTROLLER_DOMAIN.'Resources/System/Stylesheets/sm_preview_ie6.css" />
	<![endif]-->
';
			$html = str_replace('</head>', $preview_css.'</head>', $html);
		}
		
		return $html;
	}
	
	public function redirect($destination=""){
		
		if(strlen($destination) == 0){
			$destination = SM_CONTROLLER_DOMAIN;
		}
		
		header("location:".$destination);
		exit;
	}
	
	public function getFlatHtmlFileName(){
		
		if($this->isWebsitePage() && is_object($this->controller->getUserActionObject()->getPage())){
		    $file_name = $this->controller->getUserActionObject()->getPage()->getCacheFileName();
		    return SM_ROOT_DIR.'System/Cache/Pages/'.$file_name;
		}else{
		    return null;
		}
		
		/* if(defined("SM_PAGE_CACHE_NAME")){
			$filename = SM_ROOT_DIR."System/Cache/Pages/".SM_PAGE_CACHE_NAME;
		}else{
			$filename = SM_ROOT_DIR."System/Cache/Pages/smartest_page_".constant('SM_PAGE_ID').".html";
		}
		
		return $filename; */
	}
	
	function addFilter($filter_name){
		
	}
	
	public function fetch($fragment_only = false){
		
		if(defined("SM_DEVELOPER_MODE") && constant('SM_DEVELOPER_MODE') && !$fragment_only){
			$this->addFilter('DeveloperInfo');
		}
		
		// get all Smartest defined classes and constants
		$smartest_constants = $this->getConstants();
		$smartest_classes = $this->getClasses();
		
		// Calculate time taken and assign to smarty
		$this->endTime   = microtime(true);
		$this->timeTaken = number_format(($this->endTime - $this->startTime)*1000, 2, ".", ",");
		// echo $this->endTime - $this->startTime;
		
		$this->controllerPrepareTimeTaken = number_format(($this->controller->prepareTime - $this->startTime)*1000, 2, ".", ",");
		$this->controllerActionTimeTaken  = number_format(($this->controller->postActionTime - $this->startTime)*1000, 2, ".", ",");
		
		$this->smarty->assign("sm_execTime", $this->timeTaken);
		
		// Last chance to display any errors before trying to render the page
		$this->errorStack->display();
		
		// Calculate time taken and assign to smarty
		$endTime   = microtime(true);
		$this->fullTimeTaken = number_format(($endTime - $this->startTime)*1000, 2, ".", ",");
		
		// Display HTML from templates
		if((SM_CONTROLLER_METHOD == "renderPageFromUrl" || SM_CONTROLLER_METHOD == "renderPageFromId")){
			
			$page = $this->controller->getUserActionObject()->getPage();
			
			// We are rendering a CMS Page
			if(is_object($page)){	
			
				if(file_exists($this->getFlatHtmlFileName()) && $page->getCacheAsHtml() == "TRUE"){	
					
					// Just return the cached file
					return SmartestFileSystemHelper::load($this->getFlatHtmlFileName(), true);
					
				}else{
					
					// Cached file not found; Build page
					$output     = $this->getUnfilteredOutput();
					$endTime    = microtime(true);
					$this->fullTimeTaken = number_format(($endTime - $this->startTime)*1000, 2, ".", ",");
					$html       = $this->executeFilterChain($output);
					
					$filename = $this->getFlatHtmlFileName();
					
					if($page->getCacheAsHtml() == "TRUE"){
					
						if(SmartestFileSystemHelper::save($filename, $html, true)){
							return SmartestFileSystemHelper::load($filename, true);
						}else{
							return $html;
						}
					
					}else{
						return $html;
					}
				}
			
			}else{
			
				// Page is a 404 page, so don't bother with cache
				$output = $this->getUnfilteredOutput();
				$endTime   = microtime(true);
				$this->fullTimeTaken = number_format(($endTime - $this->startTime)*1000, 2, ".", ",");
				$html = $this->executeFilterChain($output);
				return $html;
			}
				
		}else{
			
			// Output is not from CMS, but one of the other modules
			
			// echo 'boo';
			
			// var_dump($fragment_only);
			
			$output = $this->getUnfilteredOutput($fragment_only);
			
			// echo $output;
			
			$endTime   = microtime(true);
			
			// echo 'Started: '.$this->startTime.', ended: '.$endTime;
			
			$this->fullTimeTaken = number_format(($endTime - $this->startTime)*1000, 2, ".", ",");
			$html = $this->executeFilterChain($output);
			
			return $html;
		}
	}
	
	public function finish(){
		
		if($this->content === null){
		    $this->content = new stdClass;
		}
		
		
		
		switch($this->controller->getNamespace()){
		    case 'ui':
		        echo $this->fetch(true);
		        // echo $this->fullTimeTaken;
		        break;
		    case 'json':
		        echo json_encode($this->content);
		        break;
		    default:
		        echo $this->fetch();
		        // echo $this->fullTimeTaken;
		        break;
	    }
		
		// echo '<pre>';
		// print_r($this->database->getDebugInfo());
		// echo '</pre>';
		
		SmartestPersistentObject::clear('centralDataHolder');
		
		exit;
	}
}
