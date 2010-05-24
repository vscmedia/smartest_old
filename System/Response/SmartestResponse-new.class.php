<?php

mb_http_output("UTF-8");
mb_http_input("UTF-8");
mb_internal_encoding("UTF-8");

require SM_ROOT_DIR.'System/Response/SmartestInfo.class.php';
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

class SmartestResponse{
    
    // Request object returned by the controller
    private $_request;
    
    // Templating object
    private $_smarty;
    
    // The settings from Configuration/database.ini
	private $_dbconfig;
	
	// Main system configuration data
	private $_configuration;
	
	// The settings from Configuration/database.ini
   	private $_error_stack;
    
    // The database object
	private $database;
	
	// The settings from Configuration/database.ini
   	private $_timing_data;
   	
   	// Authentication object
	private $_authentication;
	
	// An instance of SmartestUserAgentHelper for browser sniffing
	private $_browser;
	
	// Presentation variables defined in build() and used in finish()
	private $_main_template;
	private $_ui_template;
	
	public function __construct(){
	    
	    $this->_error_stack = new SmartestErrorStack();
	    
	    try{
            SmartestHelper::loadAll();
        }catch(SmartestException $e){
            $this->error($e->getMessage());
            $this->_error_stack->display();
        }
        
        SmartestDataObjectHelper::loadInterfaces();
        
        SmartestFileSystemHelper::include_group(

        	'System/Response/SmartestUserMessage.class.php',
        	'System/Data/SmartestSession.class.php',
        	'System/Data/SmartestPersistentObject.class.php',
            'System/Data/SmartestMysql.class.php',
        	'System/Data/SmartestSqllite.class.php',
            'System/Data/SmartestGenericListedObject.interface.php',
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

//        	'System/Data/DataQuery.class.php',
        	'System/Data/SmartestQuery.class.php',
        	'System/Data/SmartestQueryResultSet.class.php',
        	'System/Data/SmartestManyToManyQuery.class.php',
        	'System/Data/SmartestObjectModelHelper.class.php',
//        	'System/Data/SmartestGenericListedObject.class.php',
//         	'System/Data/SmartestSystemUiObject.interface.php',
        	'Library/Quince/Quince.class.php',
//        	'Library/Quince/QuinceException.class.php',
//        	'Library/Quince/QuinceBase.interface.php',
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
        
        // General system information
    	$sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
    	// Constants need to be phased out as they are slow!!
		define('SM_INFO_REVISION_NUMBER', $sd['system']['info']['revision']);
		SmartestInfo::$revision = $sd['system']['info']['revision'];
        define('SM_INFO_VERSION_NUMBER', $sd['system']['info']['version']);
        SmartestInfo::$version = $sd['system']['info']['version'];
        define('SM_INFO_BUILD_NUMBER', $sd['system']['info']['build']);
        SmartestInfo::$build = $sd['system']['info']['build'];
        
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
    		$this->_error_stack->recordError($e, false);
    	}
    	
    	$td = new SmartestParameterHolder("Smartest System Response Times");
    	$td->setParameter('start_time', microtime(true));
    	
    	SmartestPersistentObject::set('timing_data', $td);
    	
    	$this->_error_stack->display();
	    
	}
	
	public function init(){
	    
	    session_start();
		$sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		
        if(version_compare(PHP_VERSION, $sd['system']['info']['minimum_php_version']) === -1){
            $this->error("This version of PHP is too old to run Smartest. You need to have version ".$sd['system']['info']['minimum_php_version'].' or later.');
        }
        
        if(version_compare(PHP_VERSION, '5.3.0') >= 0){
            if(!ini_get('date.timezone')){
                date_default_timezone_set($sd['system']['info']['default_timezone']);
                SmartestLog::getInstance('system')->log("Default timezone must be set for PHP Version 5.3.0 and later. Was automatically set to ".$sd['system']['info']['default_timezone'].' (in system.yml). Update your php.ini file to make this notice go away.', SmartestLog::WARNING);
            }
        }
		
	    SmartestPersistentObject::set('errors:stack', $this->_error_stack);
	    
	    $sh = new SmartestSystemHelper;
	    
	    try{
	        $sh->checkRequiredExtensionsLoaded();
        }catch(SmartestException $e){
            $this->_error_stack->recordError($e, false);
        }
        
        try{
		    $sh->checkWritablePermissions();
		}catch(SmartestException $e){
		    $this->_error_stack->recordError($e, false);
		}
		
		try{
		    $sh->checkRequiredFilesExist();
		}catch(SmartestException $e){
		    $this->_error_stack->recordError($e, false);
		}
		
		$this->_configuration = new SmartestConfigurationHelper();
		
		// make sure they're loaded every time if we're doing dev
		if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			$this->_configuration->flushAll();
		}
		
		// instantiate database object
		try{
		    $this->database = SmartestDatabase::getInstance('SMARTEST');
			SmartestPersistentObject::set('db:main', $this->database);
		} catch(SmartestException $e){
		    $this->errorFromException($e);
	    }
	    
	    $this->_error_stack->display();
	    
	    // Instantiate user auth object
		$this->_authentication = new SmartestAuthenticationHelper();
		
		// Instantiate browser object
		$this->_browser = new SmartestUserAgentHelper();
		SmartestPersistentObject::set('userAgent', $this->_browser);
		
		if($this->_browser->isExplorer() && $this->_browser->getPlatform() == 'Macintosh' && !$this->isWebsitePage()){
		    include(SM_ROOT_DIR.'System/Response/ErrorPages/mac_ie.php');
		    exit();
		}
		
		try{
		    if(is_object($GLOBALS['_site'])){
		        SmartestQuery::init(true, $GLOBALS['_site']->getId());
	        }else{
	            SmartestQuery::init(true);
	        }
		}catch(SmartestException $e){
			$this->errorFromException($e);
		}
	    
	}
	
	/* private function checkRequiredExtensionsLoaded(){
		
		$extensions = get_loaded_extensions();
		
		$dependencies = array(
		    "dom",
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
		
	} */
	
	public function build(){
	    
	    // Start Application Controller
	    $this->_controller = new Quince(SM_ROOT_DIR, 'Configuration/quince.yml');
	    
	    try{
	        $this->_controller->prepare();
	    }catch(QuinceException $e){
	        $this->_error_stack->recordError(new SmartestException('Quince error: '.$e->getMessage()), false);
	    }
	    
	    // print_r($this->_controller->getCurrentRequest());
	    
	    $this->_error_stack->display();
	    
	    SmartestPersistentObject::set('controller', $this->_controller);
	    $this->_controller->getCurrentRequest()->getUserActionObject()->give('_auth', $this->_authentication);
	    
	    $this->checkAuthenticationStatus();
	    
	    $rp = new SmartestParameterHolder("Smartest Controller Information");
	    
	    $metas = new SmartestParameterHolder('Application metadata');
		$metas->loadArray($this->_controller->getCurrentRequest()->getMetas());
		
		$rp->setParameter('action', $this->_controller->getCurrentRequest()->getAction());
		$rp->setParameter('domain', $this->_controller->getCurrentRequest()->getDomain());
		$rp->setParameter('namespace', $this->_controller->getCurrentRequest()->getNamespace());
		
		$params = new SmartestParameterHolder('Request parameters');
		$params->loadArray($this->_controller->getCurrentRequest()->getRequestParameters());
		$rp->setParameter('request_parameters', $params);
		
		$module = new SmartestParameterHolder($this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('name', $this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('long_name', $this->_controller->getCurrentRequest()->getMeta('_module_longname'));
		$module->setParameter('identifier', $this->_controller->getCurrentRequest()->getMeta('_module_identifier'));
		$module->setParameter('directory', $this->_controller->getCurrentRequest()->getMeta('_module_dir'));
		$module->setParameter('class', $this->_controller->getCurrentRequest()->getMeta('_module_php_class'));
		$module->setParameter('metas', $metas);
		$rp->setParameter('application', $module);
		
		SmartestPersistentObject::set('request_data', $rp);
	    
	    // Make sure site is always looked up
	    if($this->isWebsitePage()){
		    $h = new SmartestSiteIdentificationHelper;
		    if($this->_controller->getCurrentRequest()->getAction() == 'renderEditableDraftPage'){
		        if($site = $h->getSiteByPageWebId($_GET['page_id'])){
	                $GLOBALS['_site'] = $site;
	            }else{
	                // unknown page id
	            }
	        }else{
	            if($site = $h->getSiteByDomain($_REQUEST['HTTP_HOST'])){
	                $GLOBALS['_site'] = $site;
	            }else{
	                // unknown site domain
	            }
	        }
		}else if(SmartestSession::hasData('current_open_project')){
		    $GLOBALS['_site'] =& SmartestSession::get('current_open_project')->getId();
		}
	    
	    // Start Smarty
	    if($this->isSystemClass()){
		    $templateLayerContext = 'InterfaceBuilder';
		}else{
		    $templateLayerContext = 'UserAppBuilder';
		}
		
		$smarty_manager = new SmartyManager($templateLayerContext);
		
		try{
			$this->_smarty = $smarty_manager->initialize();
		} catch(SmartestException $e){
		    $this->_error_stack->recordError($e->getMessage(), false);
		}
		
		$this->_smarty->assign("sm_admin_email", SM_OPTIONS_ADMIN_EMAIL);
		$this->_smarty->assign("sm_user_agent_json", $this->_browser->getSimpleClientSideObjectAsJson());
		$this->_smarty->assign("sm_user_agent", $this->_browser->__toArray());
		
		SmartestPersistentObject::set('presentationLayer', $this->_smarty);
		
		// Push controller and execute the user action
		try{
		    $this->_controller->dispatch(Quince::CURRENT_URL, false);
		}catch(QuinceException $e){
		    $this->errorFromException(new SmartestException('Quince error: '.$e->getMessage()));
		}
		
		SmartestPersistentObject::get('timing_data')->setParameter('overhead_time', microtime(true));
		
		// Once things like forwarding have calmed down, initialize the templates that are actually going to be used, and make final controller data available for template layer
		
		$this->initializeTemplates();
		
		$metas = new SmartestParameterHolder('Application metadata');
		$metas->loadArray($this->_controller->getCurrentRequest()->getMetas());
		
		$rp->setParameter('action', $this->_controller->getCurrentRequest()->getAction());
		$rp->setParameter('domain', $this->_controller->getCurrentRequest()->getDomain());
		$rp->setParameter('namespace', $this->_controller->getCurrentRequest()->getNamespace());
		
		$params = new SmartestParameterHolder('Request parameters');
		$params->loadArray($this->_controller->getCurrentRequest()->getRequestParameters());
		$rp->setParameter('request_parameters', $params);
		
		$module = new SmartestParameterHolder($this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('name', $this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('long_name', $this->_controller->getCurrentRequest()->getMeta('_module_longname'));
		$module->setParameter('identifier', $this->_controller->getCurrentRequest()->getMeta('_module_identifier'));
		$module->setParameter('directory', $this->_controller->getCurrentRequest()->getMeta('_module_dir'));
		$module->setParameter('class', $this->_controller->getCurrentRequest()->getMeta('_module_php_class'));
		$module->setParameter('metas', $metas);
		$rp->setParameter('application', $module);
		
		SmartestPersistentObject::set('request_data', $rp);
		
		// echo "hello";
		SmartestPersistentObject::set('request_data', $rp);
		// var_dump(SmartestPersistentObject::get('request_data'));
		
		// Pass user messages to Smarty
	    if($this->_controller->getCurrentRequest()->getUserActionObject() instanceof SmartestSystemApplication){
		    $this->_smarty->assign('sm_messages', $this->_controller->getCurrentRequest()->getUserActionObject()->getUserMessages());
	    }
	    
	}
	
	protected function checkAuthenticationStatus(){
	    
	    if($this->isSystemClass() && !$this->isPublicMethod()){
		    
		    if(!$this->_authentication->getUserIsLoggedIn()){
				if($this->_controller->getCurrentRequest()->getRequestString() != "smartest/login"){
					
					$new_url = $this->_controller->getCurrentRequest()->getDomain().'smartest/login';
					
					header("HTTP/1.1 401 Unauthorized");
					$e = new SmartestRedirectException();
					$e->setRedirectUrl($new_url);
					$e->redirect();
					
				}
			}
		}
	}
	
	public function isPublicMethod(){
	    
	    $sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		$publicMethodNames = $sd['system']['public_methods'];
		$method = $this->_controller->getCurrentRequest()->getModule().'/'.$this->_controller->getCurrentRequest()->getAction();
		return in_array($method, $publicMethodNames);
	    
	}
	
	public function isWebsitePage(){
	    
	    return in_array($this->_controller->getCurrentRequest()->getAction(), array('renderPageFromUrl', 'renderPageFromId', 'renderEditableDraftPage', 'searchDomain'));
	    
	}
	
	protected function isSystemClass(){
		
		$sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		
		$rcn = $sd['system']['reserved_classes'];
		
		if($this->_controller->getCurrentRequest()->getMeta('system')){
			
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
	
	protected function initializeTemplates(){
		
		if($subfolder = $this->_controller->getCurrentRequest()->getMeta('presentation_subfolder')){
		    if(!SmartestStringHelper::endsWith($subfolder, '/')){
		        $subfolder .= '/';
		    }
		}else{
		    $subfolder = '';
		}
		
		define('SM_CONTROLLER_MODULE_PRES_DIR', $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/');
		$sc = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/system.yml');
		define('SM_SYSTEM_SYS_TEMPLATES_DIR', $sc['system']['places']['templates_dir']);
		
		$this->_ui_template = (strlen($this->_controller->getCurrentRequest()->getAction())) ? $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/'.$subfolder.$this->_controller->getCurrentRequest()->getAction().".tpl" : null;
			
		if(is_file($this->_ui_template)){
			$this->_smarty->assign("sm_interface", $this->_ui_template);
		}else{
			$this->_smarty->assign("sm_interface", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_subTemplateNotFound.tpl");
			$this->_smarty->assign("sm_intended_interface", $this->_ui_template);
		}
		
		if(!$default_tpl = $this->_controller->getCurrentRequest()->getMeta('template')){
		    $default_tpl = '_default.tpl';
		}
		
		$this->_main_template = $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/'.$subfolder.$default_tpl;
		
		if(!is_file($this->_main_template)){
			$this->_smarty->assign("sm_main_interface", $this->_main_template);
			$this->_main_template = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_templateNotFound.tpl";
		}
		
		$this->_smarty->assign("template", $this->_main_template);
		$this->_smarty->assign("sm_app_templates_dir", SM_SYSTEM_APP_TEMPLATES_DIR);
		$this->_smarty->assign("sm_navigation", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/navigation.tpl");
		$this->_smarty->assign("sm_header", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/header.tpl");
		$this->_smarty->assign("sm_frame", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/frame.tpl");
		$this->_smarty->assign("sm_footer", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/footer.tpl");
		
	}
	
	public function finish(){
	    
	    $cth = 'Content-Type: '.$this->_controller->getCurrentRequest()->getContentType().'; charset='.$this->_controller->getCurrentRequest()->getCharSet();
	    header($cth);
	    echo $this->fetch();
	    exit;
	    
	}
	
	public function fetch(){
	    
	    // Last chance to display any errors before trying to render the page
		$this->_error_stack->display();
		
		$output = $this->getUnfilteredOutput($fragment_only);
		$output = $this->executeFilterChain($output);
			
		return $output;
	}
	
	private function getUnfilteredOutput($fragment_only = false){
		
	    try{
	        if($fragment_only){
		        $output = $this->_smarty->fetch($this->_ui_template);
	        }else{
	            $output = $this->_smarty->fetch($this->_main_template);
	        }
        }catch (SmartestException $e){
            $this->errorFromException($e);
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
	
	public function error($message="", $type=100){
	    $e = new SmartestException($message, $type);
    	$this->_error_stack->recordError($e, false);
	}
	
	public function errorFromException($e){
        $this->_error_stack->recordError($e, false);
    }
    
}