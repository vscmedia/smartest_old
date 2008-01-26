<?php

define("SM_ERROR_MISC", 100);
define("SM_ERROR_TMPL", 101);
define("SM_ERROR_FILES", 102);
define("SM_ERROR_PERMISSIONS", 102);
define("SM_ERROR_AUTH", 103);
define("SM_ERROR_DB", 104);
define("SM_ERROR_DATABASE", 104);
define("SM_ERROR_PHP", 105);
define("SM_ERROR_USER", 106);
define("SM_ERROR_MODEL", 107);

class SmartestErrorStack{
	
	protected $stack = array();
	protected $stackIndex = 0;
	protected $errorCodes;
	
	function __construct(){
		
		$this->errorCodes = array(
			"100" => "Unknown or Miscellaneous",
			"101" => "Presentation",
			"102" => "Files and Permissions",
			"103" => "Authentication",
			"104" => "Database",
			"105" => "PHP",
			"106" => "User Code",
			"107" => "Object Model Automation"
		);
		
	}
	
	function display(){
		if(count($this->stack)){
			if(defined("SM_DEVELOPER_MODE") && @SM_DEVELOPER_MODE == true){
				$errors = $this->getErrors();
				include SM_ROOT_DIR."System/Response/ErrorPages/errorlog.php";
				exit;
			}else{
				include SM_ROOT_DIR."System/Response/ErrorPages/niceerror.php";
				exit;
			}
		}
	}
	
	function recordError($message="[No error message given]", $type=100){
		$this->stack[$this->stackIndex] = new SmartestError($message, $type, @$this->errorCodes[$type]);
		$this->stackIndex++;
	}
	
	function getErrors(){
		return $this->stack;
	}
	
}

?>