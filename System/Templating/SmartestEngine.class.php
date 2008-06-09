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
	
	public function __construct($process_id){
	    
	    parent::Smarty();
		
		/* if(!$process_id){
		    $process_id = '_main';
		}else{
		    echo $process_id;
		} */
		
		$this->_process_id = $process_id;
		$this->_context = SM_CONTEXT_GENERAL;
		
		$this->controller = SmartestPersistentObject::get('controller');
		$this->section = $this->controller->getModuleName();
		$this->method  = $this->controller->getMethodName();
		$this->domain  = $this->controller->getDomain();
		$this->get     = $this->controller->getRequestVariables();
		
		$this->templateHelper = new SmartestTemplateHelper;
		$this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/Shared/";
		$this->compiler_file = SM_ROOT_DIR."System/Templating/SmartestEngineCompiler.class.php";
        $this->compiler_class = "SmartestEngineCompiler";
    	
    	$this->left_delimiter = '{';
		$this->right_delimiter = '}';
		
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
	    
	    if(!is_array($data)){
	        $data = array('data'=>$data);
	        if(isset($this->draft_mode) && $this->draft_mode){
	            echo '<br />NOTICE: $data should be array.';
	        }
	    }
	    
	    if(file_exists($template)){
	        $this->_smarty_include(array('smarty_include_tpl_file'=>$template, 'smarty_include_vars'=>$data));
        }else{
            echo '<br />ERROR: Template \''.$template.'\' does not exist.';
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
	
	public function __destruct(){
	    
	}

}