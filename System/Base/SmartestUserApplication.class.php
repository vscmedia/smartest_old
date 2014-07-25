<?php

class SmartestUserApplication extends SmartestBaseApplication{
    
    protected $_default_background_page;

    protected function __userModulePreConstruct(){
        $GLOBALS['user_action_has_page'] = false;
        $GLOBALS['user_action_page_manually_assigned'] = false;
    }
    
    public function __post(){
        parent::__post();
        if(!$this->hasDefaultBackgroundPage()){
            $this->setDefaultBackgroundPage($this->getSite()->getHomePage());
        }
    }
    
    protected function requireAuthenticatedUser($authservicename=null){
	    if(!$this->userIsLoggedIn()){
	        if($authdata = $this->getAuthenticationInfo()){
	            // $this->redirect($authdata['login_point'].'?reason=session');
	            // exit;
	            if(isset($authdata['login_point'])){
	                $e = new SmartestAuthenticationException;
        	        $e->lockOut('session', $authdata['login_point']);
	            }else{
	                die("Login required. No login point specified in ".'Configuration/auth.yml');
	            }
	        }else{
	            // user is not logged in, but there is no information about where to send them
	            die("Login required. No login point specified in ".'Configuration/auth.yml');
	        }
	    }
	}
	
	protected function requireAuthenticatedSystemUser($login_route=null){
	    if(!$this->userIsLoggedInToCms()){
	        $e = new SmartestAuthenticationException;
	        $e->lockOut('session', $login_route);
            exit;
	    }
	}
	
	protected function startSessionAsUser(SmartestUser $u){
	    
	    if($this->userIsLoggedIn()){
	        // Session is already active.
	        return false;
	    }else{
	        if($u->getId()){
	            $helper = new SmartestAuthenticationHelper;
	            return $helper->startSessionAsUser($u);
            }else{
                // This is either a new user or the Smartest user
                return false;
            }
	    }
	    
	}
	
	protected function getAuthenticationInfo(){
	    $file_path = $this->getRequest()->getMeta('_module_dir').'Configuration/auth.yml';
	    if(is_file($file_path)){
	        $data = SmartestYamlHelper::fastLoad($file_path);
	        return $data['auth'];
	    }else{
	        return null;
	    }
	}
	
	protected function processAuthenticationRequest($handle, $password, $return_redirect=null, $authservicename=null){
	    
	    if(strtolower($authservicename) == 'smartest'){
	        if(!$this->requestParameterIsSet('user')){
	            $this->setRequestParameter('user', $handle);
	        }
	        if(!$this->requestParameterIsSet('passwd')){
	            $this->setRequestParameter('passwd', $password);
	        }
	        if(!$this->requestParameterIsSet('service')){
	            $this->setRequestParameter('service', 'SMARTEST');
	        }
	        $this->forward('login', 'doAuth');
	    }
	    
	    $use_email = SmartestStringHelper::isEmailAddress($handle);
	    
	    $helper = new SmartestAuthenticationHelper;
	    
	    if($user = $helper->newLogin($handle, $password, $authservicename, $use_email)){
	        SmartestSession::set('user', $user);
	        if($return_redirect == 'COOKIE' && SmartestCookiesHelper::cookieExists('SMARTEST_RET')){
	            $redirect = SmartestCookiesHelper::getCookie('SMARTEST_RET');
	            SmartestCookiesHelper::clearCookie('SMARTEST_RET');
	            $this->redirect($redirect);
	        }else if($return_redirect && $return_redirect != 'COOKIE'){
	            $this->redirect($return_redirect);
	        }else{
	            return $user;
	        }
	    }else{
	        return false;
	    }
	    
	}
	
	protected function processSystemAuthenticationRequest($handle, $password, $return_redirect=null){
	    
	    $use_email = SmartestStringHelper::isEmailAddress($handle);
	    
	    $helper = new SmartestAuthenticationHelper;
	    
	    if($user = $helper->newLogin($handle, $password, 'SMARTEST', $use_email)){
	        SmartestSession::set('user', $user);
	        if($return_redirect == 'COOKIE' && SmartestCookiesHelper::cookieExists('SMARTEST_RET')){
	            $redirect = SmartestCookiesHelper::getCookie('SMARTEST_RET');
	            SmartestCookiesHelper::clearCookie('SMARTEST_RET');
	            $this->redirect($redirect);
	        }else if($return_redirect && $return_redirect != 'COOKIE'){
	            $this->redirect($return_redirect);
	        }else{
	            return $user;
	        }
	    }else{
	        return false;
	    }
	    
	}
	
	protected function endSession($redirect_after=null){
	    
	    $helper = new SmartestAuthenticationHelper;
	    $helper->logout();
	    
	    if(strlen($redirect_after)){
	        $this->redirect($redirect_after);
	    }
	    
	}
	
	protected function setDefaultBackgroundPage($page_name){
	    
	    if($page_name instanceof SmartestPage){
	        $GLOBALS['user_action_page'] = $page_name;
	        $GLOBALS['user_action_has_page'] = true;
	        $GLOBALS['user_action_page_manually_assigned'] = true;
	        $this->getPresentationLayer()->assignPage($page_name);
	        $this->_default_background_page = $page_name;
	        return;
	    }
	    
	    $p = new SmartestPage;
	    
	    if($p->findBy('name', $page_name)){
	        
	        $GLOBALS['user_action_page'] = $p;
	        $GLOBALS['user_action_has_page'] = true;
	        $GLOBALS['user_action_page_manually_assigned'] = true;
	        $this->getPresentationLayer()->assignPage($p);
	        $this->_default_background_page = $p;
	        
	    }else{
	        // echo "Page not found";
	    }
	    
	}
	
	protected function hasDefaultBackgroundPage(){
	    return ($this->getPresentationLayer()->hasPage() && is_object($this->_default_background_page));
	}
	
	protected function getDefaultBackgroundPage(){
	    return $this->_default_background_page;
	}

}