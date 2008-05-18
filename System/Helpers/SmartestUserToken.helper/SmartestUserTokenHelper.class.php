<?php

SmartestHelper::register('UserToken');

class SmartestUserTokenHelper extends SmartestHelper{
    
    var $database;
    
    function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
    }
    
    public function getUsersThatHaveToken($token, $site_id=''){
        
        $sql = 'SELECT * FROM Users, UsersTokensLookup, UserTokens WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UserTokens.token_code='.$token."'";
        
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
    
    }
    
    // older code, prior to SmartestApplication->getUser()->hasToken()
    function getUserHasToken($token, $db=false){
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

    function getUserTokens($db=false){  
    		
    		if($db==true){
			
			$sql = "SELECT UserTokens.token_code FROM UsersTokensLookup,UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id";
			$result = $this->database->queryToArray($sql);
			
			foreach($result as $key=>$token){
				$tokens[$key]=$token['token_code'];
			}
			
		}else{
			$tokens = $_SESSION["user"]["tokens"];
		}
		
		return $tokens;
    }
}

?>
