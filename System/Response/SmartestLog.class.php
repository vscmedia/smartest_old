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
    
    const DEBUG = 0;
    const NOTICE = 1;
    const WARNING = 2;
    const ERROR = 4;
    const PERMISSIONS = 8;
    const ACCESS_DENIED = 8;
    const USER_ACTION = 16;
    
    public static function init(){
        
        if(empty(self::$_log_types)){
            
            self::$_log_types = self::getLogTypes();
            
        }
        
    }
    
    public static function getInstance($log_name){
        
        if (self::$_instances[$log_name] == null) {
          
            self::init();
            
            if(isset(self::$_log_types[$log_name])){
                self::$_instances[$log_name] = new SmartestLog(self::$_log_types[$log_name]);
            }else{
    	        
    	        self::getInstance('system')->log('No log \''.$log_name.'\' exists', self::WARNING, -1);
    	        
    	        if(!isset(self::$_instances['default'])){
    	            self::$_instances['default'] = new SmartestLog(self::$_log_types['default']);
	            }
	            
    	        return self::$_instances['default'];
    	    }
          
        }
        
        return self::$_instances[$log_name];
        
    }
    
    public static function getLogTypesXmlData(){
	    
	    $file_path = SM_ROOT_DIR.'System/Core/Types/logs.xml';
	    
	    if(SmartestCache::hasData('logtypes_xml_file_hash', true)){
	        
	        $old_hash = SmartestCache::load('logtypes_xml_file_hash', true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save('logtypes_xml_file_hash', $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['log'];
	            SmartestCache::save('logtypes_xml_file_data', $data, -1, true);
            }else{
                $data = SmartestCache::load('logtypes_xml_file_data', true);
            }
            
            // return $data;
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('logtypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['log'];
            SmartestCache::save('logtypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	public static function getLogTypes(){
	    
	    $data = self::getLogTypesXmlData();
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        
	        if(!isset($types[$raw_type['name']])){
	            $types[$raw_type['name']] = new SmartestLogType($raw_type);
            }
	        
	        if(!defined($raw_type['id'])){
	            define($raw_type['id'], $raw_type['id']);
	        }
	        
	    }
	    
	    return $types;
	    
	}
	
	public static function translateSeverity($value=''){
        
        switch($value) {
            
            case self::DEBUG:
            return "Debug";
            
            case self::NOTICE:
            return "Notice";
            
            case self::WARNING:
            return "Warning";
            
            case self::PERMISSIONS:
            return "Permissions Error";
            
            case self::ERROR:
            return "Error";
            
            case self::USER_ACTION:
            return "User Action";
            
            default:
            return "Unknown";
        }

    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////
    /////// dynamic log-specific stuff
    
    protected $_log_type;
    
    protected function __construct($log_type){
        
        $this->_log_type = $log_type;
        
    }
    
    public function getType(){
        return $this->_log_type;
    }
    
    public function getDebugInfo(){
        
        $e = new SmartestException('');
        $d = $e->getTrace();
        return $d;
        
    }
    
    public function prepareMessage($m, $level, $backtrace_offset){
        
        $backtrace_offset = (int) $backtrace_offset;
        $backtrace_offset = $backtrace_offset * -1;
        
        $bt_data = $this->getDebugInfo();
        $bt = isset($bt_data[3+$backtrace_offset]) ? $bt_data[3+$backtrace_offset] : $bt_data[2+$backtrace_offset];
        
        $message = $this->getType()->getFormatProcessed();
        $message = str_replace('%LEVEL%', self::translateSeverity($level), $message);
        $message = str_replace('%MESSAGE%', $m, $message);
        $message = str_replace('%FILE%', $bt['file'], $message);
        $message = str_replace('%LINE%', $bt['line'], $message);
        $message = str_replace('%CLASS%', $bt['class'], $message);
        $message = str_replace('%CALLTYPE%', $bt['type'], $message);
        $message = str_replace('%FUNCTION%', $bt['function'], $message);
        
        return $message."\n";
        
    }
    
    public function log($m, $l='', $bto=0){
        
        $message = $this->prepareMessage($m, $l, $bto);
        error_log($message, 3, $this->getType()->getLogFile());
        
    }
    
}