<?php

SmartestHelper::register('Authentication');

class SmartestAuthenticationHelper extends SmartestHelper{

	private $database;
	protected $userId;
	protected $user;
	// protected $userLoggedIn;
	
	public function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		
		if(SmartestSession::get('user:isAuthenticated')){
			// $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');
		}else{
			// $this->userLoggedIn = false;
			SmartestSession::set('user:isAuthenticated', false);
		}		
	}

	public function newLogin($username, $password, $service='smartest', $use_email=false){
		if($user = $this->checkLoginDetails($username, $password, $service, $use_email)){
			return $user;
		}else{
			return false;
		}
	}
	
	public function checkLoginDetails($username, $password, $service, $use_email=false){
		
		// What kind of user object should be instantiated
		if(strtolower($service) == 'smartest'){
		    $userObj = new SmartestSystemUser;
		    $require_smartest = true;
		}else{
		    $userObj = new SmartestUser;
		    $require_smartest = false;
	    }
	    
	    if($use_email && SmartestStringHelper::isEmailAddress($username)){
	        $findby_field = 'email';
	    }else{
	        $findby_field = 'username';
	    }
	    
	    if(strpos($username, ' ') !== false){
	        // there is a space in the username, which Smartest never allows, so this can't be a username. return false for added security
	        return false;
	    }
	    
	    // Does that username exist?
		if($userObj->findBy($findby_field, $username)){
			
			if($require_smartest && $userObj->getType() != 'SM_USERTYPE_SYSTEM_USER'){
			    return false;
			}
			
			if($userObj->getActivated()){
		        
		        if(strlen($userObj->getPasswordSalt())){
		            
		            if($userObj->getPassword() === md5($password.$userObj->getPasswordSalt())){
		            
    			        $userObj->getTokens();
    			        SmartestSession::set('user:isAuthenticated', true);
    			        
    			        if($userObj->getType() == 'SM_USERTYPE_SYSTEM_USER'){
    			            SmartestSession::set('user:isAuthenticatedToCms', true);
    			        }
    			        
    			        // $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');
    			        
    			        // Give the user a new password salt every time they log in
    			        $userObj->setPasswordWithSalt($password, SmartestStringHelper::random(40), true);
    			        $userObj->setLastVisit(time());
    			        $userObj->save();
		
        			    return $userObj;
    			    
			        }else{
			            
			            return false;
			            
			        }
    			
			    }else{
			        
			        if($userObj->getPassword() === md5($password)){
			            
			            $userObj->getTokens();
			            $userObj->setPasswordWithSalt($password, SmartestStringHelper::random(40), true);
			            $userObj->setLastVisit(time());
			            $userObj->save();
			            
			            if($userObj->getType() == 'SM_USERTYPE_SYSTEM_USER'){
    			            SmartestSession::set('user:isAuthenticatedToCms', true);
    			        }
			            
    			        SmartestSession::set('user:isAuthenticated', true);
    			        // $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');

        			    return $userObj;
			            
			        }else{
			            
			            return false;
			            
			        }
			        
			    }
		
		    }else{
	            
	            // The user is not activated
		        return false;
	        
		    }
			
		}else{
			return false;
		}
	}
	
	public function startSessionAsUser(SmartestUser $u){
	    
	    if(SmartestSession::get('user:isAuthenticated')){
			// User is already logged in
			return false;
		}else{
			
			$u->setLastVisit(time());
	        $u->save();
			
			if($u->getType() == 'SM_USERTYPE_SYSTEM_USER'){
	            SmartestSession::set('user:isAuthenticatedToCms', true);
	        }
            
	        SmartestSession::set('user:isAuthenticated', true);
	        
	        SmartestSession::set('user', $u);
	        
	        return true;
			
		}
	    
	}
	
	public function getUserIsLoggedIn(){
		
		if(SmartestSession::get('user:isAuthenticated')){
			return true;
		}else{
			return false;
		}
	}
	
	public function getSystemUserIsLoggedIn(){
		
		if(SmartestSession::get('user:isAuthenticatedToCms')){
			return true;
		}else{
			return false;
		}
	}
	
	/* function getUserPermissionTokens(){
		
		$sql = "SELECT UserTokens.token_code FROM UsersTokensLookup,UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id";
		
		$result = $this->database->queryToArray($sql);
		
		foreach($result as $key=>$token){
			$tokens[$key]=$token['token_code'];
		}
		
		// $_SESSION["user"]["tokens"] = $tokens;
		
		// return $_SESSION["user"]["tokens"];
		
	} */
	
	public function logout(){
		SmartestSession::set('user:isAuthenticated', false);
		SmartestSession::clearAll();
		$this->user = array();
	}

}