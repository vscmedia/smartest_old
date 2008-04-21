<?php

class SmartestApplicationInfoHolder{
    
    protected $_name;
    protected $_label;
    protected $_method;
    protected $_class;
    protected $_class_file;
    protected $_directory;
    protected $_manager_class_file;
    protected $_settings = array();
    
    public function __construct($name, $class, $method, $class_file='', $directory='', $label=''){
        $this->_name = $name;
        $this->_class = $class;
        $this->_method = $method;
        $this->_class_file = $class_file;
        $this->_directory = $directory;
        $this->_label = $label;
    }
    
    public function getName(){
        return $this->_name;
    }
    
    public function getLabel(){
        return $this->_label;
    }
    
    public function getMethod(){
        return $this->_method;
    }
    
    public function getClass(){
        return $this->_class;
    }
    
    public function getClassFile(){
        return $this->_class_file;
    }
    
    public function getDirectory(){
        return $this->_directory;
    }
    
    public function getManagerClassFile(){
        return $this->_manager_class_file;
    }
    
}