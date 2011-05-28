<?php

// class that enables API for Smartest System Modules

class SmartestSystemApplication extends SmartestBaseApplication{
    
    // protected $_userMessages = array();
    protected $_languages = array();
    protected $_smartest_request_info = null;
    
    public function __systemModulePreConstruct(){
        
        // transfer messages left over from the last request.
		$this->transferUserMessages();
	    
	    $this->_smartest_request_info = new SmartestParameterHolder("Request information");
	    
	    $language_options = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Languages/options.yml");
		$this->_languages = $language_options['languages'];
		$this->send($this->_languages, '_languages');
	    
	    $this->send($this->getFormReturnUri(), 'sm_cancel_uri');
	    
    }
    
    final protected function transferUserMessages(){
        
        if(SmartestSession::get('user:isAuthenticated')){
		    
		    if(SmartestCache::hasData('user:messages:nextRequest:'.$this->getUser()->getId(), true)){
		        $msgs = SmartestCache::load('user:messages:nextRequest:'.$this->getUser()->getId(), true);
		        if(is_array($msgs) && count($msgs)){
		            SmartestResponse::$user_messages = SmartestCache::load('user:messages:nextRequest:'.$this->getUser()->getId(), true);
	            }
		    }
		    
		    SmartestCache::save('user:messages:nextRequest:'.$this->getUser()->getId(), array(), -1, true);
		    
	    }
	    
    }
    
    ///// Communicate with the user /////
	
	final public function addUserMessage($message, $type=1){
		$message = new SmartestUserMessage($message, $type);
		SmartestResponse::$user_messages[] = $message;
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
	    return SmartestResponse::$user_messages;
	}
	
	protected function setTitle($interface_title){
	    $this->_smartest_request_info->setParameter('interface_title', $interface_title);
		$this->getPresentationLayer()->assign("_interface_title", $interface_title);
	}
	
	///// Authentication Stuff /////
	
	protected function requireAuthenticatedUser(){
		if(!$this->_auth->getUserIsLoggedIn()){
			$this->redirect($this->domain."smartest/login");
		}
	}
	
	protected function getSite(){
	    
	    return SmartestSession::get('current_open_project');
	    
	}
	
	protected function requireOpenProject($message=''){
	    if(!SmartestSession::get('current_open_project') instanceof SmartestSite){
	        $user_message = strlen($message) ? $message : 'You need to open a site before you can access that screen.';
	        $this->addUserMessageToNextRequest($user_message, SmartestUserMessage::INFO);
	        $this->redirect('/smartest');
	    }
	}
	
	protected function requireToken($token, $exclude_root=false){
	    if(!$this->getUser()->hasToken($token, $exclude_root)){
	        $this->addUserMessageToNextRequest('You do not have sufficient access privileges for that action.', SM_USER_MESSAGE_ACCESS_DENIED);
	        $this->redirect('/smartest');
	    }
	}
	
	///// Form forwarding //////
	
	// You can pass it a URI, or if not, it will use the current request URI
	protected function setFormReturnUri($uri=''){
	    
	    $d = $this->getRequest()->getDomain();
	    
	    if(strlen($uri)){
	        
	        $uri_parts = explode("?", $uri);
	        
	        $fn = $uri_parts[0];
	        
	        if(substr($fn, 0, strlen($d)) != $d){
	            if($fn{0} == '/'){
	                $request_filename = $d.$fn;
	            }else{
	                $request_filename = $d.'/'.$fn;
	            }
	        }else{
	            $request_filename = $fn;
	        }
	        
	        if(count($uri_parts) > 1){
	            $qs = $uri_parts[1];
	            $request_vars = SmartestStringHelper::parseQueryString($qs);
            }else{
                $request_vars = array();
            }
	        
	    }else{
	        $request_vars = $_GET;
	        $request_filename = reset(explode("?", $_SERVER["REQUEST_URI"]));
        }
        
        if(isset($request_vars['from']) && isset($request_vars['from']{0})){
	        // do nothing
	    }else{
		    SmartestSession::set("form:return:location", $request_filename);
		    $vars = new SmartestParameterHolder("Form failure request variables");
            $vars->loadArray($request_vars);
		    SmartestSession::set("form:return:vars", $vars);
	    }
        
	}
	
	public function setTemporaryFormReturnUri($uri=''){
	    
	    $d = $this->getRequest()->getDomain();
	    
	    if(strlen($uri)){
	        
	        $uri_parts = explode("?", $uri);
	        
	        $fn = $uri_parts[0];
	        
	        if(substr($fn, 0, strlen($d)) != $d){
	            if($fn{0} == '/'){
	                $request_filename = $d.$fn;
	            }else{
	                $request_filename = $d.'/'.$fn;
	            }
	        }else{
	            $request_filename = $fn;
	        }
	        
	        if(count($uri_parts) > 1){
	            $qs = $uri_parts[1];
	            $request_vars = SmartestStringHelper::parseQueryString($qs);
            }else{
                $request_vars = array();
            }
	        
	    }else{
	        $request_vars = $_GET;
	        $request_filename = reset(explode("?", $_SERVER["REQUEST_URI"]));
        }
        
        /* if(isset($request_vars['from']) && isset($request_vars['from']{0})){
	        // do nothing
	    }else{ */
		    SmartestSession::set("form:return:temp_location", $request_filename);
		    $vars = new SmartestParameterHolder("Form failure request variables");
            $vars->loadArray($request_vars);
            SmartestSession::set("form:return:temp_vars", $vars);
	    // }
	    
	}
	
	public function getFormReturnUri($escape=false){
	    
	    if(SmartestSession::hasData("form:return:temp_location")){
			$form_return_uri =& SmartestSession::get("form:return:temp_location");
	    }else if(SmartestSession::hasData("form:return:location")){
			$form_return_uri =& SmartestSession::get("form:return:location");
		}else{
			$form_return_uri = "/smartest";
		}
		
		if(SmartestSession::hasData("form:return:temp_vars") && (SmartestSession::get("form:return:temp_vars") instanceof SmartestParameterHolder) && SmartestSession::get("form:return:temp_vars")->hasData()){
		    
			$form_return_uri .= "?".SmartestStringHelper::toQueryString(SmartestSession::get("form:return:temp_vars")->getParameters(), $escape);
		
		}else if(SmartestSession::hasData("form:return:vars") && (SmartestSession::get("form:return:vars") instanceof SmartestParameterHolder) && SmartestSession::get("form:return:vars")->hasData()){
		    
			$form_return_uri .= "?".SmartestStringHelper::toQueryString(SmartestSession::get("form:return:vars")->getParameters(), $escape);
			
		}
		
		return $form_return_uri;
	    
	}
	
	public function getFormReturnDescription(){
	    
	    if(SmartestSession::hasData("form:return:temp_description")){
	        return SmartestSession::get("form:return:temp_description");
	    }else{
	        return SmartestSession::get("form:return:description");
	    }
	    
	}
	
	protected function setFormReturnDescription($rd){
	    return SmartestSession::set("form:return:description", $rd);
	}
	
	protected function setTemporaryFormReturnDescription($rd){
	    return SmartestSession::set("form:return:temp_description", $rd);
	}
	
	protected function setFormReturnVar($var, $value){
    	SmartestSession::get('form:return_vars')->setParameter($var, $value);
    }

    protected function getFormReturnVar($var){
        return SmartestSession::get('form:return:vars')->getParameter($var);
    }

    protected function hasFormReturnVar($var){
        return SmartestSession::get('form:return:vars')->hasParameter($var);
    }
	
	/* protected function setFormCompleteUri(){
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
	} */
	
	protected function formForward($clear_temp=true){
		
		$uri = $this->getFormReturnUri();
		
		if($clear_temp){
		    SmartestSession::clear('form:return:temp_description');
		    SmartestSession::clear('form:return:temp_vars');
		    SmartestSession::clear('form:return:temp_location');
	    }
	    
		$this->redirect($uri, true);
		
	}
	
	/* protected function formContinue(){
		
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
	} */
	
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