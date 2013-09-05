<?php

SmartestHelper::register('Authentication');

class SmartestAuthenticationHelper extends SmartestHelper{

	private $database;
	protected $userId;
	protected $user;
	protected $userLoggedIn;
	
	public function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		
		if(SmartestSession::get('user:isAuthenticated')){
			$this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');
		}else{
			$this->userLoggedIn = false;
			SmartestSession::set('user:isAuthenticated', false);
		}		
	}

	public function newLogin($username, $password, $service='smartest'){
		if($user = $this->checkLoginDetails($username, $password, $service)){
			return $user;
		}else{
			return false;
		}
	}
	
	public function checkLoginDetails($username, $password, $service){
		
		// What kind of user object should be instantiated
		if($service == 'smartest'){
		    $userObj = new SmartestSystemUser;
		}else{
		    $userObj = new SmartestUser;
	    }
	    
	    if(strpos($username, ' ') !== false){
	        // there is a space in the username, which Smartest never allows, so this can't be a username. return false for added security
	        return false;
	    }
	    
	    // Does that username exist?
		if($userObj->findBy('username', $username)){
			
			if($userObj->getActivated()){
		        
		        if(strlen($userObj->getPasswordSalt())){
		            
		            if($userObj->getPassword() === md5($password.$userObj->getPasswordSalt())){
		            
    			        $userObj->getTokens();
    			        SmartestSession::set('user:isAuthenticated', true);
    			        $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');
    			        
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
			            
    			        SmartestSession::set('user:isAuthenticated', true);
    			        $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');

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
	
	public function getUserIsLoggedIn(){
		
		if(SmartestSession::get('user:isAuthenticated')){
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