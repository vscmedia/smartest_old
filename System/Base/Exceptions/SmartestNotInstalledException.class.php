<?php

class SmartestNotInstalledException extends SmartestException{
    
    protected $_install_status;
    protected $_database_connection_parameters;
    protected $_validation_errors;
    
    public function __construct($install_status){
        $this->_install_status = $install_status;
        parent::__construct("Smartest needs to be properly installed before you can use it.", SM_ERROR_USER);
    }
    
    public function getInstallationStatus(){
        return $this->_install_status;
    }
    
    public function getDatabaseConnectionParameters(){
        return $this->_database_connection_parameters;
    }
    
    public function setDatabaseConnectionParameters(SmartestParameterHolder $ph){
        $this->_database_connection_parameters = $ph;
    }
    
    public function getValidationErrors(){
        return $this->_validation_errors;
    }
    
    public function hasValidationErrors(){
        return ($this->_validation_errors instanceof SmartestParameterHolder) && $this->_validation_errors->hasData();
    }
    
    public function setValidationErrors(SmartestParameterHolder $ph){
        $this->_validation_errors = $ph;
    }
    
}