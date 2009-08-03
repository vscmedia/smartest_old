<?php

mb_http_output("UTF-8");
mb_http_input("UTF-8");
mb_internal_encoding("UTF-8");

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
	
	public function __construct(){
		
		// These files can't be included using the include optimisation because it depends on their already having been included
		require SM_ROOT_DIR.'System/Data/SmartestCache.class.php';
        require SM_ROOT_DIR.'System/Helpers/SmartestHelper.class.php';
        require SM_ROOT_DIR.'System/Base/Exceptions/SmartestException.class.php';
        require SM_ROOT_DIR.'System/Base/SmartestError.class.php';
        require SM_ROOT_DIR.'System/Base/SmartestErrorStack.class.php';
        require SM_ROOT_DIR.'System/Data/SmartestDatabase.class.php';
        require SM_ROOT_DIR.'System/Data/SmartestDataUtility.class.php';
        
        require 'PEAR.php';
        require 'XML/Unserializer.php';
        require 'XML/Serializer.php';
        
        $this->errorStack = new SmartestErrorStack();

        try{
            SmartestHelper::loadAll();
        }catch(SmartestException $e){
            $this->error($e->getMessage());
            $this->errorStack->display();
        }
        
        /// This neds more compatibility work as it breaks on newer servers
        
        SmartestFileSystemHelper::include_group(

        	'System/Response/SmartestUserMessage.class.php',
        	'System/Data/SmartestSession.class.php',
        	'System/Data/SmartestPersistentObject.class.php',
        	'System/Data/SmartestDataAccessClass.interface.php',
        	'System/Data/Types/SmartestBasicType.interface.php',
        	'System/Response/SmartestResponseDataHolder.class.php',
        	'System/Data/SmartestMysql.class.php',
        	'System/Data/SmartestSqllite.class.php',
        	'System/Data/SmartestCacheDb.class.php',
        	'System/Data/SmartestCmsItem.class.php',
        	'System/Data/SmartestFile.class.php',
        	'System/Base/Exceptions/SmartestWebPageBuilderException.class.php',
        	'System/Base/Exceptions/SmartestInterfaceBuilderException.class.php',
        	'System/Base/Exceptions/SmartestRedirectException.class.php'

        );
        
        SmartestDataUtility::loadTypeObjects();
        
        SmartestFileSystemHelper::include_group(
            'System/Response/SmartestLog.class.php',
        	'System/Response/SmartestLogType.class.php'
        );
        
        try{
            SmartestInstallationStatusHelper::checkStatus();
	    }catch(SmartestNotInstalledException $e){
	        if(!class_exists('SmartestInstaller')){
	            require SM_ROOT_DIR.'System/Install/SmartestInstaller.class.php';
            }
	        require SM_ROOT_DIR.'System/Install/Screens/index.php';
	        exit;
	    }
	    
	    SmartestFileSystemHelper::include_group(

        	'System/Data/DataQuery.class.php',
        	'System/Data/SmartestQuery.class.php',
        	'System/Data/SmartestQueryResultSet.class.php',
        	'System/Data/SmartestManyToManyQuery.class.php',
        	'System/Data/SmartestObjectModelHelper.class.php',
        	'System/Data/SmartestGenericListedObject.class.php',
        	'System/Data/SmartestSystemUiObject.interface.php',
        	'Library/Quince/Quince.class.php',
        	'Library/Quince/QuinceException.class.php',
        	'Library/Quince/QuinceBase.interface.php',
        	'System/Templating/SmartestEngine.class.php',
        	'System/Templating/SmartestInterfaceBuilder.class.php',
        	'System/Templating/SmartyManager.class.php',
        	'System/Controller/SmartestController.class.php',
        	'System/Templating/SmartestTemplateHelper.class.php',
        	'System/Base/SmartestBaseProcess.class.php',
        	'System/Base/SmartestBaseApplication.class.php',
        	'System/Base/SmartestSystemApplication.class.php',
        	'System/Data/SmartestDataObjectHelper.class.php',
        	'System/Base/SmartestSiteActions.class.php',
        	'System/Templating/SmartestBasicRenderer.class.php',
        	'System/Templating/SmartestWebPageBuilder.class.php',
        	'System/Response/SmartestFilterChain.class.php',
        	'System/Response/SmartestFilter.class.php'

        );
        
    	$sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		define('SM_INFO_REVISION_NUMBER', $sd['system']['info']['revision']);
        define('SM_INFO_VERSION_NUMBER', $sd['system']['info']['version']);
        
        try{
	        
	        // load database connection settings
            $c = SmartestDatabase::readConfiguration('SMARTEST');
	        
	        $d = new SmartestDataObjectHelper($c);
	        $d->loadBasicObjects();
            $d->loadExtendedObjects();
            
            SmartestFileSystemHelper::include_group(

            	'Library/API/SmartestApplication.class.php',
            	'Library/API/myUser.class.php'

            );
            
	    }catch(SmartestException $e){
    		$this->errorFromException($e);
    	}
        
        define('SM_START_TIME', microtime(true));
		$this->startTime = SM_START_TIME;
		
	}
	
	public function init(){
		
		session_start();
		
	    SmartestPersistentObject::set('errors:stack', $this->errorStack);
		SmartestPersistentObject::set('centralDataHolder', new SmartestResponseDataHolder);
		
		$this->checkRequiredExtensionsLoaded();
		$this->checkWritablePermissions();
		$this->checkRequiredFilesExist();
		
		// load up settings
		$this->configuration = new SmartestConfigurationHelper();
		
		// make sure they're loaded every time if we're doing dev
		if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			$this->configuration->flushAll();
		}
		
		// instantiate database object
		try{
			$mysql = SmartestDatabase::getInstance('SMARTEST');
			SmartestPersistentObject::set('db:main', $mysql);
		} catch(SmartestException $e){
			$this->errorFromException($e);
	    }
		
		// instantiate sqlite database object
		try{
			// $sql_main = new SmartestSqllite();
			// $_SESSION['sqllite'] = $sql_main;
			// SmartestPersistentObject::set('db:sqlite:user', $sql_main);
		} catch(SmartestException $e){
			$this->errorFromException($e);
		}
		
		// instantiate cache sqlite database object
		try{
			// $_SESSION['cache_db'] = new SmartestCacheDb();
			// SmartestPersistentObject::set('db:sqlite:cache', $sql_main);
		} catch(SmartestException $e){
			$this->errorFromException($e);
		}
		
		$this->database =& SmartestPersistentObject::get('db:main');
		
		// Instantiate user auth object
		$this->authentication = new SmartestAuthenticationHelper();
		
		// Instantiate browser object
		$this->browser = new SmartestUserAgentHelper();
		SmartestPersistentObject::set('userAgent', $this->browser);
		
		// instantiate controller
		$this->_log("Starting controller...");
		
		$this->controller = new SmartestController(SM_ROOT_DIR."Configuration/controller.xml", false);
		
		$this->controller->addProperty('_auth', $this->authentication);
		$this->controller->dispatch();
		
		SmartestPersistentObject::set('controller', $this->controller);
		
		// Everything after the hostname and before the query qtring
		define("SM_CONTROLLER_URL", $this->controller->getRequest());
		
		$this->errorStack->display();
		
	}
	
	private function error($message="", $type=100){
	    $e = new SmartestException($message, $type);
    	$this->errorStack->recordError($e, false);
    }
    
    private function errorFromException($e){
        $this->errorStack->recordError($e, false);
    }
    
    function setPersistentObject(){}
	
	function isSystemClass(){
		
		$sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		
		$reservedClassNames = $sd['system']['reserved_classes'];
		
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
	
	public function isPublicMethod(){
	    
	    $sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		
		$publicMethodNames = $sd['system']['public_methods'];
		
		$method = $this->controller->getModuleName().'/'.$this->controller->getMethodName();
		
		return in_array($method, $publicMethodNames);
	    
	}
	
	public function build(){
		
		$this->_log("Starting presentation layer...");
		
		if($this->isSystemClass()){
		    $templateLayerContext = 'InterfaceBuilder';
		}else{
		    $templateLayerContext = 'Normal';
		}
		
		$smarty_manager = new SmartyManager($templateLayerContext);
		
		try{
			$this->smarty = $smarty_manager->initialize();
		} catch(SmartestException $e){
			$this->errorFromException($e);
		}
		
		if(is_dir($module_smarty_plugins_dir)){
		    $smarty->plugins_dir[] = $module_smarty_plugins_dir;
		}
		
		SmartestPersistentObject::set('presentationLayer', $this->smarty);
		
		try{
		    $this->checkAuthenicationStatus();
		}catch (SmartestRedirectException $e){
		    // $this->redirect($e->getRedirectUrl());
		    $e->redirect();
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
		
		$this->controller->addProperty('url', $this->controller->getControllerUrlRequest());
		
		$this->isSystemClass();
		
		$this->smarty->assign("sm_admin_email", SM_OPTIONS_ADMIN_EMAIL);
		$this->smarty->assign("sm_user_agent_json", $this->browser->getSimpleClientSideObjectAsJson());
		$this->smarty->assign("sm_user_agent", $this->browser->__toArray());
		
		// These constants will be phased out once the controller supports non-redirect forwarding (changing module and action and then re-calling)
		define("SM_CONTROLLER_SECTION", $this->controller->getModuleName());
		define("SM_CONTROLLER_MODULE", $this->controller->getModuleName());
		define("SM_CONTROLLER_MODULE_DIR", SM_ROOT_DIR.$this->controller->getModuleDirectory());
		define("SM_CONTROLLER_METHOD", $this->controller->getMethodName());
		define("SM_CONTROLLER_DOMAIN", $this->controller->getDomain());
		define("SM_CONTROLLER_TEMPLATE_FAMILY", $this->controller->getTemplateName());
		define("SM_CONTROLLER_TEMPLATE_FILE", $this->templateFile);
		
		if($this->browser->isExplorer() && $this->browser->getPlatform() == 'Macintosh' && !$this->isWebsitePage()){
		    include(SM_ROOT_DIR.'System/Response/ErrorPages/mac_ie.php');
		    exit();
		}
		
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
	    
	    if($this->isSystemClass() && !$this->isPublicMethod()){
		    
		    if(!$this->authentication->getUserIsLoggedIn()){
				if(SM_CONTROLLER_URL != "smartest/login"){
					
					$new_url = $this->controller->getDomain().'smartest/login';
					
					$e = new SmartestRedirectException();
					$e->setRedirectUrl($new_url);
					
					throw $e;
				}
			}
		}
	}
	
	protected function initializeTemplates(){
		
		define('SM_CONTROLLER_MODULE_PRES_DIR', SM_CONTROLLER_MODULE_DIR.'Presentation/');
		$sc = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/system.yml');
		define('SM_SYSTEM_SYS_TEMPLATES_DIR', $sc['system']['places']['templates_dir']);
		
		$user_interface = (strlen($this->method)) ? SM_CONTROLLER_MODULE_DIR.'Presentation/'.SM_CONTROLLER_METHOD.".tpl" : null;
			
		if(is_file($user_interface)){
			$this->smarty->assign("sm_interface", $user_interface);
		}else{
			$this->smarty->assign("sm_interface", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_subTemplateNotFound.tpl");
			$this->smarty->assign("sm_intended_interface", $user_interface);
		}
		
		$this->templateFile = SM_CONTROLLER_MODULE_DIR.'Presentation/_default.tpl';
		
		if(!is_file($this->templateFile)){
			$this->smarty->assign("sm_main_interface", $this->templateFile);
			$this->templateFile = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_templateNotFound.tpl";
		}
        
        $this->userInterfaceTemplate = $user_interface;
        $this->smarty->assign("template", $this->templateFile);
		$this->smarty->assign("sm_app_templates_dir", SM_SYSTEM_APP_TEMPLATES_DIR);
		$this->smarty->assign("sm_navigation", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/navigation.tpl");
		$this->smarty->assign("sm_header", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/header.tpl");
		$this->smarty->assign("sm_frame", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/frame.tpl");
		$this->smarty->assign("sm_footer", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/footer.tpl");
		
	}
	
	public function isWebsitePage(){
	    
	    return in_array($this->controller->getMethodName(), array('renderPageFromUrl', 'renderPageFromId', 'renderEditableDraftPage', 'searchDomain'));
	    
	}
	
	private function checkRequiredExtensionsLoaded(){
		
		$extensions = get_loaded_extensions();
		
		$dependencies = array(
		    "dom",
// 			"PDO",
// 			"pdo_mysql",
//			"pdo_sqlite",
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
		
		$system_data = SmartestYamlHelper::toParameterHolder(SM_ROOT_DIR.'System/Core/Info/system.yml');
		$writable_files = $system_data->g('system')->g('writable_locations')->g('always')->getParameters();
		
		$errors = array();
		
		foreach($writable_files as $label=>$file){
			if(!is_writable($file)){
				// $errors[] = array("label"=>$label, "file"=>$file);
				$errors[] = SM_ROOT_DIR.$file;
			}
		}
		
		if(count($errors) > 0){
			$this->unwritableFiles = $errors;
			
			foreach($this->unwritableFiles as $unwritable_file){
				if(is_file($unwritable_file)){
					$this->error("The file \"".$unwritable_file."\" needs to be writable.", SM_ERROR_PERMISSIONS);
				}else{
					$this->error("The directory \"".$unwritable_file."\" needs to be writable.", SM_ERROR_PERMISSIONS);
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
	
    protected function prepareContent(){
		
		try{
			SmartestQuery::init(true);
		}catch(SmartestException $e){
			$this->errorFromException($e);
		}
		
		try{
			$this->controller->registerProcess(SmartestController::APPLICATION);
		}catch(SmartestException $e){
			$this->errorFromException($e);
		}
		
		try{
			$this->controller->performAction();
		}catch(SmartestException $e){
			$this->errorFromException($e);
		}
		
		if(!defined("SM_OVERHEAD_TIME")){
		    $overhead_finish_time = microtime(true);
    		$overhead_time_taken = number_format(($overhead_finish_time - SM_START_TIME)*1000, 2, ".", "");
		    define("SM_OVERHEAD_TIME", $overhead_time_taken);
	    }
		
		$this->errorStack->display();
		
		// if(!SmartestSystemSettingHelper::hasData('successful_install')){
		    SmartestSystemSettingHelper::save('successful_install', true);
		// }
			
		// retrieve the result
		$this->content =  $this->controller->getContent();
			
		$this->controller->setDebugLevel(0);
		$debug_info = $this->controller->getDebugContent();
		
		if($this->controller->getUserActionObject() instanceof SmartestSystemApplication){
		    $this->smarty->assign('sm_messages', $this->controller->getUserActionObject()->getUserMessages());
	    }
		
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
	
	function _log($message){
		$time = number_format(microtime(true)*100000, 0, ".", "");
		$this->log[$time] = "Smartest: ".$message;
	}
	
	function getUnfilteredOutput($fragment_only = false){
		
		if($this->content == Quince::NODISPLAY){
			$output = null;
		}else{
		    try{
		        if($fragment_only){
			        $output = $this->smarty->fetch($this->userInterfaceTemplate);
		        }else{
		            $output = $this->smarty->fetch($this->templateFile);
		        }
	        }catch (SmartestException $e){
	            
	            $this->errorFromException($e);
	            
	        }
		}
		
		return $output;
	}
    
    private function executeFilterChain($html){
        
        if($this->isSystemClass()){
            $filterchain = "InterfaceBuilder";
        }else{
            $filterchain = "ApplicationFilters";
        }
        
        $fc = new SmartestFilterChain($filterchain);
        $html = $fc->execute($html);
	    
	    return $html;
	    
	}
	
	public function redirect($destination=""){
		
		if(strlen($destination) == 0){
			$destination = SM_CONTROLLER_DOMAIN;
		}
		
		header("location:".$destination);
		exit;
	}
	
	public function fetch($fragment_only = false){
		
		// get all Smartest defined classes and constants
		// $smartest_constants = $this->getConstants();
		// $smartest_classes = $this->getClasses();
		
		// Calculate time taken and assign to smarty
		/* $this->endTime   = microtime(true);
		$this->timeTaken = number_format(($this->endTime - $this->startTime)*1000, 2, ".", "");
		
		$this->controllerPrepareTimeTaken = number_format(($this->controller->prepareTime - $this->startTime)*1000, 2, ".", "");
		$this->controllerActionTimeTaken  = number_format(($this->controller->postActionTime - $this->startTime)*1000, 2, ".", "");*/
		
		// Last chance to display any errors before trying to render the page
		$this->errorStack->display();
		
		// Calculate time taken and assign to smarty
		// $endTime   = microtime(true);
		// $this->fullTimeTaken = number_format(($endTime - $this->startTime)*1000, 2, ".", "");
		
		$output = $this->getUnfilteredOutput($fragment_only);
		$output = $this->executeFilterChain($output);
			
		return $output;
	}
	
	public function finish(){
		
	    if($this->content === null){
		    $this->content = new stdClass;
		}
		
		/* switch($this->controller->getNamespace()){
		    case 'ui':
		        echo $this->fetch(true);
		        break;
		    case 'json':
		        echo json_encode($this->content);
		        break;
		    default:
		        echo $this->fetch();
		        // echo $this->fullTimeTaken.'<br />';
		        // 
		        break;
	    } */
	    
	    echo $this->fetch();
		
		// Last chance to display any errors before trying to render the page
		$this->errorStack->display();
		
		exit;
	}
}
