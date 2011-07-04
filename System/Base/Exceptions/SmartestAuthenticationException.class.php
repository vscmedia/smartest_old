<?php

class SmartestAuthenticationException extends SmartestException{
    
    protected $_controller;
    
    public function __construct(){
        $this->_controller = SmartestPersistentObject::get('controller');
    }
    
    public function lockOut(){
        
        header("HTTP/1.1 401 Unauthorized");
        
        $this->setReturnCookie();
        $this->setPostVarsCookie();
        $e = new SmartestRedirectException();
        
        if($this->_controller->getCurrentRequest()->getRequestString() == 'smartest'){
		    $e->setRedirectUrl($this->_controller->getCurrentRequest()->getDomain().'smartest/login');
	    }else{
	        $e->setRedirectUrl($this->_controller->getCurrentRequest()->getDomain().'smartest/login#session');
	    }
	    
		$e->redirect();
		exit;
    }
    
    public function setPostVarsCookie(){
        // TODO - any post vars that were submitted should be stored, so that they can be submitted when the user logs in again.
    }
    
    public function setReturnCookie(){
        
        if($this->_controller->getCurrentRequest()->getRequestString() != 'smartest'){
            $domain = $this->_controller->getCurrentRequest()->getDomain();
            SmartestCookiesHelper::setCookie('SMARTEST_RET', $this->_controller->getCurrentRequest()->getRequestStringWithVars(), 1); // The user has a day to log back in before this information is lost
        }
        
    }

}