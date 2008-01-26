<?php

class TLog {
  
  private static $instance = false;
  private $level = false;
  private $logfile = './log/site.log';
  private $siteroot = '';
  
  const DEBUG = 1;
  const NOTE = 2;
  const WARNING = 4;
  const ERROR = 8;
  const OFF = 64000;

  
  // Don't allow "new TLog", force use of getInstance
  private function __construct() {
    $this->siteroot = dirname(dirname(__FILE__));
  }
  

  public function getInstance() {
    
    if (self::$instance === false) {
      self::$instance = new TLog();
      /* if(defined("SM_TLOG_LEVEL")) {
        self::$instance->setLevel(TLOG_LEVEL);
      }*/
    }
    return self::$instance;
  }
  
  
  public function Log ($message = '', $level = false) {
    if ($this->level && ($level >= $this->level)) {
      
      $severity = $this->translateSeverity($level);
      
      $bt_data = debug_backtrace();
      $bt = isset($bt_data[1]) ? $bt_data[1] : $bt_data[0];
      
      $bt['file'] = str_replace($this->siteroot,'',$bt['file']);
      $message = date('Y-m-d H:i:s').": ({$severity}) $message ({$bt['file']}:{$bt['line']} {$bt['class']}{$bt['type']}{$bt['function']}())\n";
      // error_log($message,3,$this->logfile);
    }
  }
  
  
  public function setLevel ($level) {
    if (is_numeric($level)) {
      $this->level = intval($level);
    } else if ($lev = constant("self::$level")) {
      $this->level = $lev;
    }
    return $this->level;
  }
  
  
  public function translateSeverity ( $value='' ) {
    switch($value) {
      case ::DEBUG:
        return "Debug";
      case TLog::NOTE:
        return "Note";
      case TLog::WARNING:
        return "Warning";
      case TLog::ERROR:
        return "Error";
      default:
        return "Unknown";
    }
    
    
  }
  
} 