<?php

require('Smarty.class.php');

class SmartestEngine extends Smarty{

	protected $controller;
	protected $section;
	protected $method;
	protected $domain;
	protected $get;
	
	public function __construct(){
	
		parent::Smarty();
		
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
	
    public function getUserAgent(){
	    return SmartestPersistentObject::get('userAgent');
	}
	
	public function run($string, $compile_name=null){
	    
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