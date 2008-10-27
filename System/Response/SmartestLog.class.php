<?php

// require_once "Libraries/Tlog/TLog.class.php";

/* class SmartestLog extends TLog{

	private static $instance = false;
	private $level = false;
	private $logfile;
	private $siteroot = '';
	
	function __construct(){
		$this->level = parent::DEBUG;
		$this->logfile = "Logs/".date("Y_m_d").".log";
	}
	
	function log($message = '', $level = false){
		if ($this->level && ($level >= $this->level)) {
      
			$severity = $this->translateSeverity($level);
      
			$bt_data = debug_backtrace();
			$bt = isset($bt_data[1]) ? $bt_data[1] : $bt_data[0];
      
			$bt['file'] = str_replace($this->siteroot,'',$bt['file']);
			$message = date('Y-m-d H:i:s').": ({$severity}) $message ({$bt['file']}:{$bt['line']} {$bt['class']}{$bt['type']}{$bt['function']}())\n";
			
			@error_log($message, 3, $this->logfile);
		}
	}
	
} */

class SmartestLog{
    
    // static/factory stuff
    private static $_instances = array();
    private static $_log_types = array();
    
    protected function __construct($log_id){
        
    }    

    public static function getInstance(){
        
        if (self::$_instances[$log_id] === false) {
          
            if(empty(self::$_log_types)){
                
                self::$_log_types = self::getLogTypes();
                
            }
            
            self::$_instances[$log_id] = new SmartestLog($log_id);
          
        }
        
        return self::$_instances[$log_id];
        
    }
    
    public static function getLogTypes(){
        
    }
    
    // dynamic logging stuff
    public function log($message, $type=''){
        
    }
    
}