<?php

class SmartestBaseProcess{
    
    private $_type;
    private $_name;
    private $_long_name;
    private $_basedir;
    
    public function setProcessType($type){
        if(!isset($this->_type)){
            $this->_type = $type;
        }
    }
    
    public function getProcessType(){
        return $this->_type;
    }
    
    public function setProcessName($name){
        if(!isset($this->_name)){
            $this->_name = $name;
        }
    }
    
    public function getProcessName(){
        return $this->_name;
    }
    
    public function setProcessLongName($name){
        if(!isset($this->_long_name)){
            $this->_long_name = $name;
        }
    }
    
    public function getProcessLongName(){
        return $this->_long_name;
    }
    
    public function setProcessDirectory($basedir){
        if(!isset($this->_basedir)){
            $this->_basedir = $basedir;
        }
    }
    
    public function getProcessDirectory(){
        return $this->_basedir;
    }
    
    public function getDataTypes(){
        return SmartestDataUtility::getDataTypes();
    }
    
    public function getAssetTypes(){
        return SmartestDataUtility::getAssetTypes();
    }
    
}