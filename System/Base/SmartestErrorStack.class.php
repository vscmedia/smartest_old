<?php

class SmartestErrorStack{
	
	protected $stack = array();
	protected $stackIndex = 0;
	protected $errorCodes;
	
	public function __construct(){
		
		$this->errorCodes = array(
			"100" => "Unknown or Miscellaneous",
			"101" => "Presentation",
			"102" => "Files and Permissions",
			"103" => "Authentication",
			"104" => "Database",
			"105" => "PHP",
			"106" => "User Code",
			"107" => "Object Model Automation",
			"108" => "Internal Smartest"
		);
		
	}
	
	public function display(){
		if(count($this->stack)){
		    
		    header("HTTP/1.1 500 Internal Server Error");
		    
		    $sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
		    $smartest_version = $sd['system']['info']['version'];
		    $smartest_revision = $sd['system']['info']['revision'];
		    
			if(defined("SM_DEVELOPER_MODE") && constant('SM_DEVELOPER_MODE')){
				$errors = $this->getErrors();
				include SM_ROOT_DIR."System/Response/ErrorPages/errorlog.php";
				exit;
			}else{
				include SM_ROOT_DIR."System/Response/ErrorPages/niceerror.php";
				exit;
			}
		}
	}
	
	/* function recordError($message="[No error message given]", $type=100){
		$this->stack[$this->stackIndex] = new SmartestError($message, $type, @$this->errorCodes[$type]);
		$this->stackIndex++;
	} */
	
	public function recordError($exception){
	    $this->stack[$this->stackIndex] = new SmartestError($exception, @$this->errorCodes[$exception->getCode()]);
		$this->stackIndex++;
	}
	
	public function getErrors(){
		return $this->stack;
	}
	
}