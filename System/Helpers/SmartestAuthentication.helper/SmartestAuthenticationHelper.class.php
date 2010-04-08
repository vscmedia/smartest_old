<?php

SmartestHelper::register('Authentication');

class SmartestAuthenticationHelper extends SmartestHelper{

	private $database;
	protected $userId;
	protected $user;
	protected $userLoggedIn;
	
	function __construct(){
		
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
	
	function checkLoginDetails($username, $password, $service){
		
		$sql = "SELECT * FROM Users WHERE username='".mysql_real_escape_string($username)."' AND password='".md5($password)."'";
		$user = $this->database->queryToArray($sql);
		
		if(count($user) > 0){
			
			if($service == 'smartest'){
			    $userObj = new SmartestSystemUser;
			}else{
			    $userObj = new SmartestUser;
		    }
		    
			$userObj->hydrate($user[0]);
			$userObj->getTokens();
			
			SmartestSession::set('user:isAuthenticated', true);
			
			$this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');
			
			return $userObj;
		}else{
			return false;
		}
	}
	
	function getUserIsLoggedIn(){
		
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
		// $this->userLoggedIn = false;
		SmartestSession::set('user:isAuthenticated', false);
		SmartestSession::clearAll();
		$this->user = array();
	}

}