<?php

SmartestHelper::register('Users');

class SmartestUsersHelper extends SmartestHelper{
    
    protected $database;
    
    public function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
    }
    
    public static function getTokenData(){
        
        $tokens = SmartestPffrHelper::getContentsFast(SM_ROOT_DIR.'System/Core/Types/usertokens.pff');
        return $tokens;
        
    }
    
    public function getTokens(){
        
        $data = self::getTokenData();
        $tokens = array();
        
        foreach($data as $rt){
            $t = new SmartestUserToken_New($rt);
            $tokens[$rt['id']] = $t;
        }
        
        return $tokens;
        
    }
    
    public function getUsers(){
        
    }
    
    public function getSystemUsers(){
        
        $raw_users = $this->database->queryToArray("SELECT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users WHERE username != 'smartest' ORDER BY user_fullname");
        $users = array();
        
        foreach($raw_users as $ru){
            $u = new SmartestSystemUser;
            $u->hydrate($ru);
            $users[] = $u;
        }
        
        return $users;
        
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
	        $sql = "SELECT DISTINCT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id IN ('".implode("', '", $token)."')";
        }else{                                                                                                                   
	        $sql = 'SELECT DISTINCT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id='.$token."'";
	    }
	    
        if(is_numeric($site_id)){
            $sql .= " AND UsersTokensLookup.utlookup_site_id='".$site_id."'";
        }
        
        $sql .= " ORDER BY user_fullname";
        
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
        $sql = "SELECT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM `Users`, `UsersTokensLookup` WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id=21 AND (UsersTokensLookup.utlookup_site_id='".$site_id."' OR UsersTokensLookup.utlookup_is_global='1') ORDER BY user_fullname";
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
    
    public function getCreditableUsersOnSite($site_id){
        
        $site_id = (int) $site_id;
        $sql = "SELECT DISTINCT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM `Users`, `UsersTokensLookup` WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND (UsersTokensLookup.utlookup_token_id=1 OR UsersTokensLookup.utlookup_token_id=0) AND (UsersTokensLookup.utlookup_site_id='".$site_id."' OR UsersTokensLookup.utlookup_is_global='1') ORDER BY user_fullname";
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $record){
            
            $u = new SmartestUser;
            $u->hydrate($record);
            $users[] = $u;
            
        }
        
        return $users;
        
    }
    
    public function getRoles($include_system_roles=true){
        
        $result = $this->database->queryToArray("SELECT * FROM Roles");
	    
	    if($include_system_roles){
	        $roles = $this->getSystemRoles();
	    }else{
	        $roles = array();
	    }
	    
	    foreach($result as $role_array){
	        $role = new SmartestRole;
	        $role->hydrate($role_array);
	        $roles[] = $role;
	    }
	    
	    return $roles;
        
    }
    
    public function getSystemRoles(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/roles.yml');
        $raw_roles = $data['roles'];
        $roles = array();
        
        foreach($raw_roles as $k=>$rr){
            $r = new SmartestNonDbRole;
            $r->hydrate($rr);
            $r->setId($k);
            $roles[$k] = $r;
        }
        
        return $roles;
        
    }
    
    public function distributeAuthorCreditTokenFromPage(SmartestPage $page){
        $author_ids = $page->getAuthorIds();
        $this->addTokenToMultipleUsersByUserIdsArray('author_credit', $author_ids, $page->getSiteId());
    }
    
    public function distributeAuthorCreditTokenFromItem(SmartestItem $item, $site_id=null){
        $author_ids = $item->getAuthorIds();
        $this->addTokenToMultipleUsersByUserIdsArray('author_credit', $author_ids, $site_id);
    }
    
    public function addTokenToMultipleUsersByUserIdsArray($token_code, $ids, $site_id=null){
        
        $token_id = $this->getTokenId($token_code);
        
        foreach($this->getUsersArrayFromIdsArray($ids, true) as $user){
            $user->addTokenById($token_id, $site_id, true);
        }
        
    }
    
    public function getUsersArrayFromIdsArray($ids, $create_system_users=false){
        
        $users = array();
        
        if(count($ids)){
            
            $sql = "SELECT * FROM Users WHERE user_id IN (".implode(', ', $ids).")";
            $result = $this->database->queryToArray($sql);
            
            foreach($result as $u){
                
                if($create_system_users){
                    $user = new SmartestSystemUser;
                }else{
                    $user = new SmartestUser;
                }
                
                $user->hydrate($u);
                $users[] = $user;
            }
        }
        
        return $users;
        
    }
    
    /* public function getRolesAsArrays(){
        
        $roles = $this->getRoles();
        $arrays = array();
        
        foreach($roles as $r){
            $arrays[] = $r->__toArray();
        }
        
        return $arrays;
        
    } */
    
    // older code, prior to SmartestApplication->getUser()->hasToken()
    /* public function getUserHasToken($token, $db=false){
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
    } */

    /* public function getUserTokens(){  
    		
    	/* if($db==true){
			
			$sql = "SELECT UserTokens.token_code FROM UsersTokensLookup,UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id";
			$result = $this->database->queryToArray($sql);
			
			foreach($result as $key=>$token){
				$tokens[$key]=$token['token_code'];
			}
			
		}else{
			// $tokens = $_SESSION["user"]["tokens"];
		}
		
		return $tokens;
    } */
}