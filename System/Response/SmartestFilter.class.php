<?php

class SmartestFilter{
    
    protected $_function_file;
    protected $_directory;
    protected $_name;
    protected $_filter_chain;
    
    public function getName(){
        return $this->_name;
    }
    
    public function setName($n){
        $this->_name = $n;
    }
    
    public function getFunctionFile(){
        return $this->_function_file;
    }
    
    public function setFunctionFile($f){
        $this->_function_file = $f;
    }
    
    public function getDirectory(){
        return $this->_directory;
    }
    
    public function setDirectory($d){
        $this->_directory = $d;
    }
    
    public function attachChain(SmartestFilterChain $c){
        $this->_filter_chain = $c;
    }
    
    public function getFilterChain(){
        return $this->_filter_chain;
    }
    
    public function getDraftMode(){
        return $this->_filter_chain->getDraftMode();
    }
    
    public function execute($html){
        
        $function_name = 'smartest_filter_'.$this->_name;
        
        if(!function_exists($function_name)){
        
            require $this->_function_file;
        
            if(function_exists($function_name)){
            
                $html = call_user_func($function_name, $html, &$this);
                return $html;
            
            }else{
            
            }
            
        }
        
    }
    
}