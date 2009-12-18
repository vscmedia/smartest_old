<?php

// class that enables API for Smartest System Modules

class SmartestSystemApplication extends SmartestBaseApplication{
    
    protected $_userMessages = array();
    protected $_languages = array();
    
    public function __systemModulePreConstruct(){
        
        // transfer messages left over from the last request.
		
		if(SmartestSession::get('user:isAuthenticated')){
		    
		    if(SmartestCache::hasData('user:messages:nextRequest:'.$this->getUser()->getId(), true) && is_array(SmartestCache::load('user:messages:nextRequest:'.$this->getUser()->getId(), true))){
		        $this->_userMessages = SmartestCache::load('user:messages:nextRequest:'.$this->getUser()->getId(), true);
		    }
		    
		    SmartestCache::save('user:messages:nextRequest:'.$this->getUser()->getId(), array(), -1, true);
		    
	    }
	    
	    $language_options = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Languages/options.yml");
		$this->_languages = $language_options['languages'];
		$this->send($this->_languages, '_languages');
	    
	    if($this->getSite() instanceof SmartestSite){
	        $this->send(true, 'show_left_nav_options');
	    }else{
	        $this->send(false, 'show_left_nav_options');
	    }
    }
    
    ///// Communicate with the user /////
	
	final public function addUserMessage($message, $type=1){
		$message = new SmartestUserMessage($message, $type);
		$this->_userMessages[] = $message;
	}
	
	final protected function addUserMessageToNextRequest($message, $type=1){
	    
	    if(SmartestSession::get('user:isAuthenticated')){
	    
	    	$next_request_messages = SmartestCache::load('user:messages:nextRequest:'.$this->getUser()->getId(), true);
		
    		if(!is_array($next_request_messages)){
    		    $next_request_messages = array();
    		}
		
    		$message = new SmartestUserMessage($message, $type);
    		$next_request_messages[] = $message;
    		SmartestCache::save('user:messages:nextRequest:'.$this->getUser()->getId(), $next_request_messages, -1, true);
		
	    }
	}
	
	final public function getUserMessages(){
	    $messages = $this->_userMessages;
		return $messages;
	}
	
	protected function setTitle($page_title){
		$this->getPresentationLayer()->assign("sectionName", $page_title);
	}
	
	///// Authentication Stuff /////
	
	protected function requireAuthenticatedUser(){
		if(!$this->_auth->getUserIsLoggedIn()){
			$this->redirect($this->domain."smartest/login");
		}
	}
	
	protected function getSite(){
	    
	    return SmartestPersistentObject::get('current_open_project');
	    
	}
	
	protected function requireToken($token){
	    if(!$this->getUser()->hasToken($token)){
	        $this->addUserMessageToNextRequest('You do not have sufficient access privileges for that action.', SM_USER_MESSAGE_ACCESS_DENIED);
	        $this->redirect('/smartest');
	    }
	}
	
	///// Form forwarding //////
	
	protected function setFormReturnUri(){
	    if(isset($_GET['from']) && isset($_GET['from']{0})){
	        // do nothing
	    }else{
		    SmartestSession::set("form:return:location", reset(explode("?", $_SERVER["REQUEST_URI"])));
		    SmartestSession::set("form:return:vars", $_GET);
	    }
	}
	
	public function getFormReturnUri($escape=false){
	    
	    if(SmartestSession::hasData("form:return:location")){
			$form_return_uri =& SmartestSession::get("form:return:location");
		}else{
			$form_return_uri = "/smartest";
		}
		
		if(SmartestSession::hasData("form:return:vars") && is_array(SmartestSession::get("form:return:vars"))){
		    
			$form_return_uri .= "?";
			$form_return_uri .= SmartestStringHelper::toQueryString(SmartestSession::get("form:return:vars"), $escape);
			
		}
		
		return $form_return_uri;
	    
	}
	
	protected function setFormCompleteUri(){
		$_SESSION["_FORM_RETURN"] = reset(explode("?", $_SERVER["REQUEST_URI"]));
		$_SESSION["_FORM_RETURN_VARS"] = $_GET;
	}
	
	protected function setFormContinueUri(){
		$_SESSION["_FORM_CONTINUE"] = reset(explode("?", $_SERVER["REQUEST_URI"]));
		$_SESSION["_FORM_CONTINUE_VARS"] = $_GET;
	}
	
	protected function setFormFailUri(){
		$_SESSION["_FORM_FAIL"] = reset(explode("?", $_SERVER["REQUEST_URI"]));
		$_SESSION["_FORM_FAIL_VARS"] = $_GET;
	}
	
	protected function setFormReturnVar($var, $value){
		$_SESSION["_FORM_RETURN_VARS"][$var] = $value;
	}
	
	protected function formForward(){
		
		$this->redirect($this->getFormReturnUri(), true);
		
	}
	
	protected function formContinue(){
		
		if($_SESSION["_FORM_CONTINUE"]){
			$this->_formContinueUri =& $_SESSION["_FORM_CONTINUE"];
		}else{
			$this->_formContinueUri = "/smartest";
		}
		
		$uri = $this->_formContinueUri;
		
		if(is_array($_SESSION["_FORM_CONTINUE_VARS"])){
			$uri .= "?";
			foreach($_SESSION["_FORM_CONTINUE_VARS"] as $var=>$value){
				$uri .= "$var=$value&";
			}
		}
		
		header("Location:".$uri);
		exit;
	}
	
	protected function formFail(){
		
		if($_SESSION["_FORM_FAIL"]){
			$this->_formFailUri =& $_SESSION["_FORM_FAIL"];
		}else{
			$this->_formFailUri = "/";
		}
		
		$uri = $this->_formFailUri;
		
		if(is_array($_SESSION["_FORM_FAIL_VARS"])){
			$uri .= "?";
			foreach($_SESSION["_FORM_FAIL_VARS"] as $var=>$value){
				$uri .= "$var=$value&";
			}
		}
		
		header("Location:".$uri);
		exit;
	}
	
	///// Errors /////
    
    function _error($message, $type=''){
    	
    	if(!$message){
    		$message = "[unspecified error]";
    	}
    	
    	if(!$type){
    		$type = SM_ERROR_USER;
    	}
    	
    	$this->_errorStack->recordError($message, $type, true);
    }
    
}