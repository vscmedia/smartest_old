<?php

class SmartestException extends Exception{
	
	protected $_redirectUrl = null;
	
	function __construct($message, $code=100, $verboseType="Unknown or Miscellaneous"){
		parent::__construct();
		$this->code = $code;
		$this->message = $message;
	}
	
	function setRedirectUrl($url){
	    $this->_redirectUrl = $url;
	}
	
	function getRedirectUrl(){
	    return $this->_redirectUrl;
	}

}