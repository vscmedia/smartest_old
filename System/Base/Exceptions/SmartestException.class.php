<?php

class SmartestException extends Exception{
	
	protected $_redirectUrl = null;
	
	public function __construct($message, $code=100){
		parent::__construct();
		$this->code = $code;
		$this->message = $message;
	}
	
	public function setMessage($message){
	    $this->message = $message;
	}
	
	public function setRedirectUrl($url){
	    $this->_redirectUrl = $url;
	}
	
	public function getRedirectUrl(){
	    return $this->_redirectUrl;
	}

}