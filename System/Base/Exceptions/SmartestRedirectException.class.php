<?php

class SmartestRedirectException extends SmartestException{

    protected $_redirectUrl = null;
    protected $_status_codes = array(301=>"Moved Permanently", 302=>"Found", 303=>"See Other", 304=>"Not Modified", 305=>"Use Proxy", 307=>"Temporary Redirect");
    protected $_default_status_code = null;
    
    const PERMANENT = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const TEMPORARY = 307;
    
    public function __construct($url=false, $default_status_code=null){
        if(strlen($url)){
            $this->_redirectUrl = $url;
        }
        $this->setDefaultStatusCode($default_status_code);
    }
    
    public function setRedirectUrl($url){
	    $this->_redirectUrl = $url;
	}
	
	public function getRedirectUrl(){
	    return $this->_redirectUrl;
	}
	
	public function setDefaultStatusCode($code){
	    if($this->isValidRedirectCode($code)){
	        $this->_default_status_code = $code;
	    }
	}
	
	public function getDefaultStatusCode(){
	    return $this->_default_status_code;
	}
	
	public function redirect($sc=false, $exit=true){
	    
	    if($this->isValidRedirectCode($sc)){
	        $status_code = $sc;
	    }else{
	        $status_code = $this->isValidRedirectCode($this->getDefaultStatusCode()) ? $this->getDefaultStatusCode() : 303;
	    }
	    
	    header("HTTP/1.1 ".$status_code." ".$this->_status_codes[$status_code]);
        header("Location: ".$this->getRedirectUrl());
        exit;
	    
	}
	
	public function isValidRedirectCode($code){
	    return isset($this->_status_codes[$code]);
	}

}