<?php

SmartestHelper::register('Authentication');

class SmartestAuthenticationHelper extends SmartestHelper{

	private $database;
	protected $userId;
	protected $user;
	protected $userLoggedIn;
	
	function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		
		if(SmartestPersistentObject::get('user:isAuthenticated')){
			$this->userLoggedIn =& SmartestPersistentObject::get('user:isAuthenticated');
		}else{
			$this->userLoggedIn = false;
			SmartestPersistentObject::set('user:isAuthenticated', false);
		}		
	}

	function newLogin($username, $password, $service='smartest'){
		if($user = $this->checkLoginDetails($username, $password, $service)){
			// $this->
			return $user;
		}else{
			return false;
		}
	}
	
	function checkLoginDetails($username, $password, $service){
		
		$sql = "SELECT * FROM Users WHERE username='".mysql_real_escape_string($username)."' AND password='".md5($password)."'";
		$user = $this->database->queryToArray($sql);
		
		if(count($user) > 0){
			
			// SmartestSession::set('user:array', $user[0]);
			
			$userObj = new SmartestUser;
			$userObj->hydrate($user[0]);
			
			$userObj->getTokens();
			
			// print_r($userObj);
			
			// SmartestPersistentObject::set('user', $userObj);
			
			// delete these eventually
			// $_SESSION["user"] = $user[0];
			// $this->user =& $_SESSION["user"];
			// $_SESSION["userLoggedIn"] = "TRUE";
			
			SmartestSession::set('user:isAuthenticated', true);
			
			// 
			$this->userLoggedIn =& SmartestPersistentObject::get('user:isAuthenticated');
			$this->getUserPermissionTokens();
			
			return $userObj;
		}else{
			return false;
		}
	}
	
	function getUserIsLoggedIn(){
		
		if($this->userLoggedIn){
			return true;
		}else{
			return false;
		}
	}
	
	function getUserPermissionTokens(){
		
		$sql = "SELECT UserTokens.token_code FROM UsersTokensLookup,UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id";
		
		$result = $this->database->queryToArray($sql);
		
		foreach($result as $key=>$token){
			$tokens[$key]=$token['token_code'];
		}
		
		// $_SESSION["user"]["tokens"] = $tokens;
		
		return $_SESSION["user"]["tokens"];
		
	}
	
	function logout(){
		$this->userLoggedIn = false;
		// SmartestPersistentObject::set('user:isAuthenticated', false);
		SmartestSession::clearAll();
		$this->user = array();
	}

}