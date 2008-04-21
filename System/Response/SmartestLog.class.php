<?php

require_once "Libraries/Tlog/TLog.class.php";

class SmartestLog extends TLog{

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
	
}