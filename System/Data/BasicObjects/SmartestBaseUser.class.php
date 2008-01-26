<?php

class SmartestBaseUser extends SmartestDataObject{
	
	protected $_tokens = array();
	protected $_site_ids = array();
	
	protected function __objectConstruct(){
		$this->_table_prefix = 'user_';
		$this->_table_name = 'Users';
		$this->_no_prefix = array('username', 'password');
		
		if(method_exists($this, '__myConstructor')){
		    $args = func_get_args();
		    $this->__myConstructor($args);
		}
		
	}
	
	public function hydrate($id){
		
		if(is_array($id)){
			
			if(array_key_exists('username', $id) && array_key_exists('password', $id) && array_key_exists('user_id', $id)){
				
				$this->_properties['username'] = $id['username'];
				$this->_properties['password'] = $id['password'];
			
				foreach($id as $key => $value){
					if(substr($key, 0, strlen($this->_table_prefix)) == $this->_table_prefix){
						$this->_properties[substr($key, strlen($this->_table_prefix))] = $value;
						$this->_came_from_database = true;
					}else if(in_array($name, $this->_no_prefix)){
						$this->_properties[$name] = $value;
					}
					
				}
				
				$this->getTokens();
				
				return true;
			
			}
			
		}else{
		
			if(is_numeric($id)){
				// numeric_id
				$field = 'user_id';
			}else if(SmartestStringHelper::isEmailAddress($id)){
				// 'webid'
				$field = 'user_email';
			}else if(preg_match('/^[a-zA-Z0-9_-]+$/', $id)){
				// name
				$field = 'username';
			}
		
			$sql = "SELECT * FROM ".$this->_table_name." WHERE $field='$id'";
			
			$result = $this->database->queryToArray($sql);
		
			if(count($result)){
			
				foreach($result[0] as $name => $value){
					if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
						$this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
						$this->_properties_lookup[SmartestStringHelper::toCamelCase(substr($name, strlen($this->_table_prefix)))] = substr($name, strlen($this->_table_prefix));
					}else if(in_array($name, $this->_no_prefix)){
					    $this->_properties[$name] = $value;
					    $this->_properties_lookup[SmartestStringHelper::toCamelCase($name)] = $name;
					}
				}
			
				$this->_came_from_database = true;
				return true;
				
			}else{
				return false;
			}
		}
	}
	
	public function getUsername(){
		return $this->_properties['username'];
	}
	
	public function getPermissionEditableSites(){
	    
	    $sites = array();
	    
	    // if the user has global permission to edit user permissions on sites (all sites), then whatever site he was logged into would let him edit permissions, so show a list of all sites.
	    if($this->hasGlobalPermission('modify_user_permissions')){
	        $sql = "SELECT * FROM Sites";
	        // return list of all sites
	    }else{
	        // otherwise find all the sites where he allowed to edit user permissions
	        $sql = "SELECT DISTINCT Sites.* FROM Users, UserTokens, UsersTokensLookup, Sites WHERE Users.user_id =  '".$this->getId()."' AND UserTokens.token_code =  'modify_user_permissions' AND Users.user_id = UsersTokensLookup.utlookup_user_id AND UserTokens.token_id = UsersTokensLookup.utlookup_token_id AND Sites.site_id = UsersTokensLookup.utlookup_site_id";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    
	    foreach($result as $site_array){
	        $site = new SmartestSite;
	        $site->hydrate($site_array);
	        $sites[] = $site;
	    }
	    
	    // echo $sql;
	    // print_r($result);
	    
	    return $sites;
	    
	}
	
	public function getPermissionEditableSitesAsArrays(){
	    
	    $site_objects = $this->getPermissionEditableSites();
	    $array = array();
	    
	    foreach($site_objects as $site){
	        // print_r($site);
	        $array[] = $site->__toArray();
	    }
	    
	    return $array;
	    
	}
	
	public function getAllowedSites(){
	    
	    if($this->hasGlobalPermission('site_access')){
            $sql = "SELECT * FROM Sites";
        }else{
            $sql = "SELECT DISTINCT Sites.* FROM Users, UserTokens, UsersTokensLookup, Sites WHERE Users.user_id = '".$this->getId()."' AND UserTokens.token_code = 'site_access' AND Users.user_id = UsersTokensLookup.utlookup_user_id AND UserTokens.token_id = UsersTokensLookup.utlookup_token_id AND Sites.site_id = UsersTokensLookup.utlookup_site_id";
        }   
        
        $this->_site_ids = array();
        
	    // echo $sql;
	    $result = $this->database->queryToArray($sql);
	    // print_r($result);
	    $sites = array();
	    
	    foreach($result as $site_array){
	        $site = new SmartestSite;
	        $site->hydrate($site_array);
	        // print_r($site);
	        $sites[] = $site;
	        
	        if($site->getId()){
	            $this->_site_ids[] = $site->getId();
            }
	    }
	    
	    return $sites;
	}
	
	public function getAllowedSiteIds(){
	    if(!count($this->_site_ids)){
	        $this->getAllowedSites();
	    }
	    
	    return $this->_site_ids;
	    
	}
	
	public function hasGlobalPermission($permission){
	    
	    $token_code = SmartestStringHelper::toVarName($permission);
	    
	    $sql = "SELECT UsersTokensLookup.*, UserTokens.token_id FROM UsersTokensLookup, UserTokens WHERE utlookup_user_id='".$this->getId()."' AND UserTokens.token_code='".$token_code."' AND UserTokens.token_id=UsersTokensLookup.utlookup_token_id AND utlookup_is_global='1'";
	    $result = $this->database->queryToArray($sql);
	    
	    // echo $sql .'<br />';
	    
	    if(count($result)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	// must have a length of between 4 and 40
	public function setUsername($username){
		if(strlen($username) > 3 && strlen($username) < 41 && preg_match('/^[a-zA-Z0-9_-]+$/', $username)){
			$this->_properties['username'] = $username;
			$this->_modified_properties['username'] = $username;
		}
	}
	
	// returns hashed password for checking
	public function getPassword(){
		return $this->_properties['password'];
	}
	
	// must have a minimum length of 4
	public function setPassword($password){
		if(SmartestStringHelper::isMd5Hash($password)){
			$this->_properties['password'] = $password;
			$this->_modified_properties['password'] = $password;
		}else{
			if(strlen($password) > 3){
				$this->_properties['password'] = md5($password);
				$this->_modified_properties['password'] = md5($password);
			}
		}
	}
	
	public function getTokens($reload=false){
	
		if(count($this->_tokens) && !$reload){
			return $this->_tokens;
		}else{
		    
		    if(SmartestSession::get('current_open_project') instanceof SmartestSite){
		        $site_id = SmartestSession::get('current_open_project')->getId();
		        $sql = "SELECT UserTokens.token_code FROM UsersTokensLookup, UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$this->getId()."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND (UsersTokensLookup.utlookup_is_global='1' OR UsersTokensLookup.utlookup_site_id='$site_id')";
		    }else{
			    $sql = "SELECT UserTokens.token_code FROM UsersTokensLookup, UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$this->getId()."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UsersTokensLookup.utlookup_is_global='1'";
			}
			
			$result = $this->database->queryToArray($sql);
		
			foreach($result as $key=>$token){
				$this->_tokens[$key] = $token['token_code'];
			}
		}
	}
	
	public function reloadTokens(){
	    $this->getTokens(true);
	}
	
	// public function
	
	public function getUnusedTokens(){
	    
	    // get all tokens
	    
	}
	
	function addToken($token_code, $site_id){
	    
	    $token = new SmartestUserToken;
	    
	    if($token->hydrateBy('code', $token_code)){
	        
	        $utl = new SmartestUserTokenLookup;
		    $utl->setUserId($this->getId());
		    $utl->setTokenId($token->getId());
		    $utl->setGrantedTimestamp(time());
            
		    if($site_id == "GLOBAL"){
		        $utl->setIsGlobal(1);
		    }else{
		        // if($site_id instanceof SmartestSite){
    		    // $site_id = SmartestSession::get('current_open_project')->getId();
    		    $utl->setSiteId($site_id);
    		    // }
		    }
		    
		    $utl->save();
		    
		}
		
		$this->reloadTokens();
	}
	
	public function isAuthenicated(){
		
		// only works for the current logged in user
		if(SmartestPersistentObject::get('user:isAuthenticated')){
			return true;
		}else{
			return false;
		}
	}
	
	public function hasToken($token){
	    return in_array($token, $this->_tokens);
	}
	
	public function __toString(){
	    return $this->getFirstname().' '.$this->getLastname();
	}
	
	public function getNumHeldPages($site_id=''){
	    
	    $sql = "SELECT * FROM Pages WHERE page_is_held=1 AND page_held_by='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND page_site_id = '".$site_id."'";
	    }
	    
	    return $this->database->howMany($sql);
	}
	
	public function releasePages($site_id=''){
	    
	    $sql = "UPDATE Pages SET page_is_held=0, page_held_by=0 WHERE page_is_held=1 AND page_held_by='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND page_site_id = '".$site_id."'";
	    }
	    
	    $this->database->rawQuery($sql);
	    
	}
	
	public function getNumHeldItems($model_id, $site_id=''){
	    
	    $sql = "SELECT * FROM Items WHERE item_itemclass_id='".$model_id."' AND item_is_held=1 AND item_held_by='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND item_site_id = '".$site_id."'";
	    }
	    
	    return $this->database->howMany($sql);
	    
	    return 0;
	}
	
	public function releaseItems($model_id, $site_id=''){
	    
	    $sql = "UPDATE Items SET item_is_held=0, item_held_by=0 WHERE item_itemclass_id='".$model_id."' AND item_is_held=1 AND item_held_by='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND item_site_id = '".$site_id."'";
	    }
	    
	    $this->database->rawQuery($sql);
	}
	
}