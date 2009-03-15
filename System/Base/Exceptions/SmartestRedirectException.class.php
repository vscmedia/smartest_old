<?php

class SmartestRedirectException extends SmartestException{

    protected $_redirectUrl = null;
    protected $_status_codes = array(301=>"Moved Permanently", 302=>"Found", 303=>"See Other", 304=>"Not Modified", 305=>"Use Proxy", 307=>"Temporary Redirect");
    
    const PERMANENT = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const TEMPORARY = 307;
    
    public function __construct($url=false){
        if(strlen($url)){
            $this->_redirectUrl = $url;
        }
    }
    
    public function setRedirectUrl($url){
	    $this->_redirectUrl = $url;
	}
	
	public function getRedirectUrl(){
	    return $this->_redirectUrl;
	}
	
	public function redirect($sc=301){
	    
	    header("HTTP/1.1 ".$sc." ".$this->_status_codes[$sc]);
        header("Location: ".$this->getRedirectUrl());
        exit;
	    
	}

}