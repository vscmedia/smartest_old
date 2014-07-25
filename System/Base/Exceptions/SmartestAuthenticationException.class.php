<?php

class SmartestAuthenticationException extends SmartestException{
    
    protected $_controller;
    
    public function __construct(){
        $this->_controller = SmartestPersistentObject::get('controller');
    }
    
    public function lockOut($hash='session', $login_route=null){
        
        $this->setReturnCookie();
        $this->setPostVarsCookie();
        $e = new SmartestRedirectException();
        
        if(strlen($login_route) && $this->_controller->getUrlFor($login_route)){
            $login_url = $this->_controller->getUrlFor($login_route);
        }else{
            $login_url = $this->_controller->getUrlFor('@loginscreen:login');
        }
        
        if($this->_controller->getCurrentRequest()->getNamespace() == 'ajax' || $this->_controller->getCurrentRequest()->getNamespace() == 'modal'){
            
            header('HTTP/1.1 401 Unauthorized');
            // This is so that if a modal is summoned when the system has timed out, the modal will redirect the user to the login screen
            if($this->_controller->getCurrentRequest()->getNamespace() == 'modal'){
                echo '<script type="text/javascript">window.location="'.$login_url.'#'.$hash.'";</script>';
            }
            exit;
            
        }else{
            
            $helper = new SmartestAuthenticationHelper;
    	    // return $helper->getUserIsLoggedIn();
    	    
    	    // echo $login_url;
    	    // exit;
            
            if($this->_controller->getCurrentRequest()->getRequestString() == 'smartest'){
                if($helper->getUserIsLoggedIn()){
    		        $e->setRedirectUrl($login_url.'#unauthorized');
		        }else{
		            $e->setRedirectUrl($login_url);
		        }
    	    }else{
    	        $e->setRedirectUrl($login_url.'#'.$hash);
    	    }
	    
    		$e->redirect(array(401, 303), true);
		
	    }
		
    }
    
    public function setPostVarsCookie(){
        // TODO - any post vars that were submitted should be stored, so that they can be submitted when the user logs in again.
    }
    
    public function setReturnCookie(){
        
        if($this->_controller->getCurrentRequest()->getRequestString() != 'smartest'){
            if($this->_controller->getCurrentRequest()->getNamespace() != 'modal' && $this->_controller->getCurrentRequest()->getNamespace() != 'ajax' && !$this->_controller->getCurrentRequest()->isAjax()){
                $domain = $this->_controller->getCurrentRequest()->getDomain();
                SmartestCookiesHelper::setCookie('SMARTEST_RET', $domain.$this->_controller->getCurrentRequest()->getRequestStringWithVars(), 1); // The user has a day to log back in before this information is lost
            }
        }
        
    }

}