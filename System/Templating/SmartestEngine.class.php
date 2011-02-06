<?php

require('Smarty.class.php');

class SmartestEngine extends Smarty{

	protected $controller;
	protected $section;
	protected $method;
	protected $domain;
	protected $get;
	protected $_process_id;
	protected $_child_processes = array();
	protected $_context;
	protected $_abstractPropertyHolder = array();
	protected $_log = array();
	protected $_included_scripts = array();
	protected $_included_stylesheets = array();
	protected $_series = array();
	protected $_request_data;
	protected $_request;
	
	public function __construct($process_id){
	    
	    parent::Smarty();
		
		$this->_process_id = $process_id;
		$this->_context = SM_CONTEXT_GENERAL;
		
		$this->controller = SmartestPersistentObject::get('controller');
		// $this->_request_data = SmartestPersistentObject::get('controller')->getCurrentRequest();
		$this->_request_data = SmartestPersistentObject::get('request_data');
		
		/* $this->section = $this->controller->getModuleName();
		$this->method  = $this->controller->getMethodName();
		$this->domain  = $this->controller->getDomain();
		$this->get     = $this->controller->getRequestVariables(); */
		
		$this->templateHelper = new SmartestTemplateHelper;
		$this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/Shared/";
		$this->compiler_file = SM_ROOT_DIR."System/Templating/SmartestEngineCompiler.class.php";
        $this->compiler_class = "SmartestEngineCompiler";
    	
        $this->left_delimiter = '{';
		$this->right_delimiter = '}';
		
		$this->assign('request_parameters', $this->_request_data->getParameter('request_parameters'));
		
	}
	
	public function startChildProcess($pid, $type=''){
	    
	    $pid = SmartestStringHelper::toVarName($pid);
	    
	    if(!$type){
	        $engine_type = get_class($this);
        }else{
            $engine_type = $type;
        }
        
	    $cp = new $engine_type($pid);
	    
	    $cp->template_dir = $this->templates_dir;
		$cp->compile_dir = $this->compile_dir;
		$cp->cache_dir = $this->cache_dir;
		$cp->config_dir = $this->config_dir;
		
		$cp->assign('section', $this->_tpl_vars['section']);
		$cp->assign('module', $this->_tpl_vars['module']);
		$cp->assign('module_dir', $this->_tpl_vars['module_dir']);
		$cp->assign('method', $this->_tpl_vars['method']);
		$cp->assign('domain', $this->_tpl_vars['domain']);
		$cp->assign('class', $this->_tpl_vars['class']);
		$cp->assign('sm_admin_email', $this->_tpl_vars['sm_admin_email']);
		$cp->assign('sm_user_agent', $this->_tpl_vars['sm_user_agent']);
		$cp->assign('request_parameters', $this->_request_data->getParameter('request_parameters'));
		
		$this->_child_processes[$pid] = $cp;
        return $cp;
	}
	
	public function killChildProcess($pid){
	    $pid = SmartestStringHelper::toVarName($pid);
	    
	    if(isset($this->_child_processes[$pid])){
	        if($pid != $this->_process_id){
	            unset($this->_child_processes[$pid]);
	            return true;
            }else{
                return false;
            }
	        
	    }else{
	        return false;
	    }
	}
	
	public function getProcessId(){
	    return $this->_process_id;
	}
	
    public function getUserAgent(){
	    return SmartestPersistentObject::get('userAgent');
	}
	
	public function getRequestData(){
	    return $this->_request_data;
	}
	
	public function getController(){
	    return $this->controller;
	}
	
	public function getUrlFor($route_name){
	    try{
	        return $this->controller->getUrlFor($route_name);
        }catch(QuinceException $e){
            $this->raiseError($e->getMessage());
        }
	}
	
	public function getContext(){
	    return $this->_context;
	}
	
	public function setContext($context){
	    $this->_context = $context;
	}
	
	public function getProperty($property_name){
	    $property_name = SmartestStringHelper::toVarName($property_name);
	    if(isset($this->_abstractPropertyHolder[$property_name])){
	        return $this->_abstractPropertyHolder[$property_name];
	    }
	}
	
	public function setProperty($property_name, $value){
	    $property_name = SmartestStringHelper::toVarName($property_name);
	    $this->_abstractPropertyHolder[$property_name] = $value;
	}
	
	public function run($template, $data){
	    
	    if(!is_array($data) && !($data instanceof SmartestParameterHolder)){
	        $data = array('data'=>$data);
	        if(isset($this->draft_mode) && $this->draft_mode){
	            echo '<br />NOTICE: $data should be and array or SmartestParameterHolder object.';
	        }
	    }
	    
	    if($data instanceof SmartestParameterHolder){
	        $data = $data->getParameters();
	    }
	    
	    if(file_exists($template)){
	        $this->_smarty_include(array('smarty_include_tpl_file'=>$template, 'smarty_include_vars'=>$data));
        }else{
            echo '<br />ERROR: Template \''.$template.'\' does not exist.';
        }
	}
	
	protected function _log($message){
	    $this->_log[] = $message;
	}
	
	protected function _comment($message){
	    $message = str_replace('-->', '', $message);
	    $this->_log($message);
	    return "<!-- SmartestEngine Message: ".$message." -->\n";
	}
	
	public function raiseError($error_msg='Unknown Template Error'){
	    
	    $this->_log($error_msg);
	    
	    if($this->getDraftMode()){
	        $this->assign('_error_text', $error_msg);
	        $error_markup = $this->fetch(SM_ROOT_DIR."System/Presentation/WebPageBuilder/markup_error.tpl");
	        return $error_markup;
        }
	}
	
	public function evaluate($string, $compile_name=null){
	    
	    // create resource name
	    if($compile_name){
	        $resource_name = $compile_name;
        }else{
            $resource_name = sha1($string);
        }
        
        // try compiling it and running it
        if($this->_compile_source($resource_name, $string, $result)){
            return $result;
        }else{
            return false;
        }
        
	}
	
	public function getScriptIncluded($script_file){
	    return in_array($script_file, $this->_included_scripts);
	}
	
	public function setScriptIncluded($script_file){
	    $this->_included_scripts[] = $script_file;
	}
	
	public function getStylesheetIncluded($file){
	    return in_array($file, $this->_included_stylesheets);
	}
	
	public function setStylesheetIncluded($file){
	    $this->_included_stylesheets[] = $file;
	}
	
	public function addPluginDirectory($directory){
	    
	    $directory = realpath($directory).'/';
	    
	    if(is_dir($directory)){
	        if(SmartestFileSystemHelper::isSafeFileName($directory)){
	            $this->plugins_dir[] = $directory;
	        }else{
	            throw new SmartestException("Tried to add plugin directory outside Smartest: ".$directory, SM_ERROR_USER);
	        }
	    }else{
	        throw new SmartestException("Tried to add non-existent plugin directory: ".$directory, SM_ERROR_USER);
	    }
	}
	
	public function getPluginDirectories(){
	    
	    return $this->plugins_dir;
	    
	}
	
	public function initNumberSeriesByName($series_name){
	    if(isset($this->_series[$series_name])){
	        return $this->_series[$series_name];
	    }else{
	        $this->_series[$series_name] = new SmartestNumberSeries;
	        $this->_series[$series_name]->setName($series_name);
	        return $this->_series[$series_name];
	    }
	}

}