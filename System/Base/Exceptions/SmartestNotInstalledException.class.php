<?php

class SmartestNotInstalledException extends SmartestException{
    
    protected $_status; 
    
    public function __construct($install_status){
        $this->_status = $install_status;
        parent::__construct("Smartest needs to be properly installed before you can use it.", SM_ERROR_USER);
    }
    
    public function getInstallationStatus(){
        return $this->_status;
    }
    
}