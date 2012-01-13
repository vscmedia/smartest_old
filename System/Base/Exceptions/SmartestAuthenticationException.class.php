<?php

class SmartestAuthenticationException extends SmartestException{
    
    protected $_controller;
    
    public function __construct(){
        $this->_controller = SmartestPersistentObject::get('controller');
    }
    
    public function lockOut(){
        
        $this->setReturnCookie();
        $this->setPostVarsCookie();
        $e = new SmartestRedirectException();
        
        $c = SmartestPersistentObject::get('controller');
        
        if($c->getCurrentRequest()->getNamespace() == 'ajax' || $c->getCurrentRequest()->getNamespace() == 'modal'){
            
            header('HTTP/1.1 401 Unauthorized');
            exit;
            
        }else{
        
            if($this->_controller->getCurrentRequest()->getRequestString() == 'smartest'){
    		    $e->setRedirectUrl($this->_controller->getCurrentRequest()->getDomain().'smartest/login');
    	    }else{
    	        $e->setRedirectUrl($this->_controller->getCurrentRequest()->getDomain().'smartest/login#session');
    	    }
	    
    		$e->redirect(array(401, 303), true);
		
	    }
		
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