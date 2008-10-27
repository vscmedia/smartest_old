<?php

class SmartestLogType extends SmartestParameterHolder{
    
    public function __construct($log_id){
        $this->_log_id = $log_id;
        $this->_name = 'Smartest Log: '.$log_id;
        $this->_read_only = true;
    }
    
    public function getLogFile(){
        
        
        
    }
    
}