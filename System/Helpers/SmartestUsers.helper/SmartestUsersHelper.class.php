<?php

SmartestHelper::register('Users');

class SmartestUsersHelper extends SmartestHelper{
    
    var $database;
    
    public function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
    }
    
    public static function getTokenData(){
        
        $tokens = SmartestPffrHelper::getContentsFast(SM_ROOT_DIR.'System/Core/Types/usertokens.pff');
        return $tokens;
        
    }
    
    public function getTokenId($token){
        
        $all_tokens = self::getTokenData();
        
        foreach($all_tokens as $t){
            if($t['code'] == $token){
                return $t['id'];
            }
        }
        
        return null;
        
    }
    
    public function getUsersThatHaveToken($token, $site_id=''){
        
	    if(is_array($token)){
	        $sql = "SELECT DISTINCT Users.* FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id IN ('".implode("', '", $token)."')";
        }else{                                                                                                                   
	        $sql = 'SELECT DISTINCT Users.* FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id='.$token."'";
	    }
	    
        if(is_numeric($site_id)){
            $sql .= " AND UsersTokensLookup.utlookup_site_id='".$site_id."'";
        }
        
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $record){
            $u = new SmartestUser;
            $u->hydrate($record);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function getUsersThatHaveTokenAsArrays($token, $site_id){
    
        $users = $this->getUsersThatHaveToken($token, $site_id='');
        $arrays = array();
        
        foreach($users as $u){
            
            $arrays[] = $u->__toArray();
            
        }
        
        return $arrays;
    
    }
    
    public function getUsersOnSite($site_id){
        
        $site_id = (int) $site_id;
        $sql = "SELECT Users.* FROM `Users`, `UsersTokensLookup` WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id=21 AND (UsersTokensLookup.utlookup_site_id='".$site_id."' OR UsersTokensLookup.utlookup_is_global='1')";
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $record){
            
            $u = new SmartestUser;
            $u->hydrate($record);
            $users[] = $u;
            
        }
        
        return $users;
        
    }
    
    public function getUsersOnSiteAsArrays($site_id){
        
        $users = $this->getUsersOnSite($site_id);
        $arrays = array();
        
        foreach($users as $u){
            $arrays[] = $u->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getRoles(){
        
        $result = $this->database->queryToArray("SELECT * FROM Roles");
	    
	    $roles = array();
	    
	    foreach($result as $role_array){
	        $role = new SmartestRole;
	        $role->hydrate($role_array);
	        $roles[] = $role;
	    }
	    
	    return $roles;
        
    }
    
    public function getRolesAsArrays(){
        
        $roles = $this->getRoles();
        $arrays = array();
        
        foreach($roles as $r){
            $arrays[] = $r->__toArray();
        }
        
        return $arrays;
        
    }
    
    // older code, prior to SmartestApplication->getUser()->hasToken()
    public function getUserHasToken($token, $db=false){
    	if($db==true){
			
			$sql = "SELECT * FROM UsersTokensLookup, UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UserTokens.token_code=$token";
			$count = $this->database->howMany($sql);
		
			if($count>0){
				$has_token = 0;
			}else{
				$has_token = 1;
			}
			
		}else{
			$has_token=in_array($token,$_SESSION["user"]["tokens"]);
		}
		
		return $has_token;
    }

    public function getUserTokens($db=false){  
    		
    		if($db==true){
			
			$sql = "SELECT UserTokens.token_code FROM UsersTokensLookup,UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id";
			$result = $this->database->queryToArray($sql);
			
			foreach($result as $key=>$token){
				$tokens[$key]=$token['token_code'];
			}
			
		}else{
			// $tokens = $_SESSION["user"]["tokens"];
		}
		
		return $tokens;
    }
}