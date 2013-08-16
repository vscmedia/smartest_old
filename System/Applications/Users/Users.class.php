<?php

class Users extends SmartestSystemApplication{
    
    public function startPage(){
		
		$this->setFormReturnUri();
		$this->setFormReturnDescription('user accounts');
		$this->setTitle('User accounts');
		
		$h = new SmartestUsersHelper;
		$users = $h->getSystemUsers();
		// $database = SmartestDatabase::getInstance('SMARTEST');
		// $users = $database->queryToArray("SELECT * FROM Users WHERE username != 'smartest'");
		$this->send($users, 'users');
		$this->send(count($users), 'count');
		
	}
	
	///////////////////////////////// USERS ////////////////////////////////////
	
	public function addUser(){
	    
	    $uhelper = new SmartestUsersHelper;
	    $roles = $uhelper->getRoles();
	    $this->setTitle('Add a user');
	    
	    if($this->getUser()->hasToken('create_users')){
	        
	        $this->send(new SmartestArray($this->getUser()->getSitesWhereUserHasToken('create_users')), 'sites');
	        $this->send($roles, 'roles');
	        
	    }else{
	        $this->addUserMessageToNextRequest("You don't have permission to add new user accounts", SmartestUserMessage::ACCESS_DENIED);
	        $this->redirect('/smartest/users');
	    }
	    
	}
	
	public function insertUser($get, $post){
		
		$user = new SmartestSystemUser;
        
        $username = SmartestStringHelper::toUsername($this->getRequestParameter('username'));
        
        if($user->findBy('username', $username)){
            
            $this->addUserMessage("The username you entered is already is use.", SmartestUserMessage::WARNING);
            $this->forward('users', 'addUser');
            
        }else{
            
            // print_r($_POST);
            
		    $password = $this->getRequestParameter('password');
    		$firstname = $this->getRequestParameter('user_firstname');
    		$lastname = $this->getRequestParameter('user_lastname');
    		$email = $this->getRequestParameter('user_email');
    		$website = $this->getRequestParameter('user_website');
    		$salt = SmartestStringHelper::random(40);
    		$hash = md5($this->getRequestParameter('password').$salt);
    		
    		if(!SmartestStringHelper::isValidExternalUri($website)){
    		    $website = 'http://'.$website;
    		}
		    
		    $user->setUsername($username);
    		$user->setPassword($hash);
    		$user->setPasswordSalt($salt);
    		$user->setFirstname($firstname);
    		$user->setLastname($lastname);
    		$user->setEmail($email);
    		$user->setWebsite($website);
    		$user->setRegisterDate(time());
    		$user->setIsSmartestAccount(1);
    		
    		$user->save();
		    
    		// add user tokens
    		
    		if(is_numeric($this->getRequestParameter('user_role'))){
                
                // User-created role is being used to assign tokens
                $role = new SmartestRole;
                
                if($role->find($this->getRequestParameter('user_role'))){
                    $tokens = $role->getTokens();
                }else{
                    $tokens = array();
                }
                
                $l = new SmartestManyToManyLookup;
    	        $l->setType('SM_MTMLOOKUP_USER_INITIAL_ROLE');
    	        $l->setEntityForeignKeyValue(1, $user->getId());
    	        $l->setEntityForeignKeyValue(2, $this->getRequestParameter('user_role'));
    	        $l->save();
            
            }else if(substr($this->getRequestParameter('user_role'), 0, 7) == 'system:'){
                
                $role_id = substr($this->getRequestParameter('user_role'), 7);
                $h = new SmartestUsersHelper;
                $system_roles = $h->getSystemRoles();
                
                if(isset($system_roles[$role_id])){
                    $role = $system_roles[$role_id];
                    $tokens = $role->getTokens();
                }else{
                    $tokens = array();
                }
                
            }else{
                
                $tokens = array();
                
            }
    		
    		if($this->getRequestParameter('global_site_access')){
    		    if($this->getUser()->hasToken('grant_global_permissions')){
    		        
    		        // Add tokens from role globally
    		        foreach($tokens as $t){
                        $user->addTokenById($t->getId(), 'GLOBAL');
                    }
                    
    		    }else{
    		        $this->addUserMessageToNextRequest('You do not have permission to grant global site access or other tokens');
    		    }
		    }else{
		        $site_ids = $this->getRequestParameter('user_sites');
		        
		        if(is_array($site_ids)){
		            
		            // Add tokens from role for each site
		            foreach($site_ids as $site_id){
		                // print_r($site_id);
		                foreach($tokens as $t){
                            $user->addTokenById($t->getId(), $site_id);
                        }
		                
		            }
		        }
		    }
        
        }
    
		$this->formForward(); 
		
	}

    public function editUser($get){
  
		if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
		
		    $user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
    		    $this->setTitle('Edit user | '.$user->__toString());
    		    $this->send($user, 'user');
            }else{
                $this->addUserMessageToNextRequest("The User ID was not recognised.");
                $this->formForward();
            }
            
            $this->send($this->getUser()->hasToken('modify_user_permissions'), 'show_tokens_edit_tab');
            $this->send($this->getUser()->hasToken('require_user_password_change'), 'require_password_changes');
        
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to modify users other than yourself.");
            $this->formForward();
            
        }
        
	}
	
	public function editUserTokens($get){
	    
	    $this->requireOpenProject();
	    
	    if($this->getUser()->hasToken('modify_user_permissions')){
	    
	        $permission_editable_sites = $this->getUser()->getPermissionEditableSites();
	        
	        $user_id = $get['user_id'];	
        	$user = new SmartestSystemUser;
    	
        	if($user->find($user_id)){
    	
        	    $this->setFormReturnUri();
    	        
    	        $allow_global = ($this->getUser()->hasGlobalPermission('modify_user_permissions') || $this->getUser()->hasToken('grant_global_permissions')) ? true : false;
    	        
    	        if(isset($get['site_id']) && strlen($get['site_id'])){
    	            $site_id = $get['site_id'];
    	        }else{
    	            $site_id = $this->getSite()->getId();
    	        }
    	        
    	        $this->send(true, 'show_tokens_edit_tab');
    	        
    	        $utokens = $user->getTokensOnSite($site_id, true);
        	    
        	    $tokens = $user->getAvailableTokens($site_id);
    		    
    		    $this->send($user, 'user');
    		    $this->send($utokens, 'utokens');
    		    $this->send($tokens, 'tokens');
    		    $this->send($permission_editable_sites, 'sites');
    		    $this->send($site_id, 'site_id');
    		    $this->send($allow_global, 'allow_global');
    	
    	    }else{
    	        $this->addUserMessageToNextRequest("The user ID was not recognised");
    	        $this->formForward();
    	    }
	    
        }else{
            $this->addUserMessageToNextRequest("You don't have permission to edit user permissions.");
	        $this->formForward();
        }
    	
	}
	
	public function editUserProfilePic(){
	    
	    if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
		
		    $user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
    		    
    		    $this->send($user, 'user');
    		    $uh = new SmartestUsersHelper;
    		    
    		    if($g = $uh->getUserProfilePicsGroup()){
    		        $this->send($g->getMembers(1, $this->getSite()), 'assets');
    		    }else{
    		        $helper = new SmartestAssetsLibraryHelper;
            	    $this->send($helper->getAttachableFiles($this->getSite()->getId()), 'assets');
    		    }
    		    
            }else{
                $this->addUserMessageToNextRequest("The user ID was not recognised.");
                $this->formForward();
            }
            
            $this->send($this->getUser()->hasToken('modify_user_permissions'), 'show_tokens_edit_tab');
        
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to modify users other than yourself.");
            $this->formForward();
            
        }
	    
	}
	
	public function saveUserProfilePic(){
	    
	    if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
		
		    $user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
    		    
    		    if($this->getRequestParameter('profile_pic_asset_id')){
    		        $a = new SmartestAsset;
    		        if($a->find($this->getRequestParameter('profile_pic_asset_id'))){
    		            $user->setProfilePicAssetId($this->getRequestParameter('profile_pic_asset_id'));
    		            $user->save();
    		            $this->formForward();
    		        }else{
    		            
    		        }
		        }else{
		            
		        }
    		    
            }else{
                $this->addUserMessageToNextRequest("The user ID was not recognised.");
                $this->formForward();
            }
            
            $this->send($this->getUser()->hasToken('modify_user_permissions'), 'show_tokens_edit_tab');
        
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to modify users other than yourself.");
            $this->formForward();
            
        }
	    
	}
	
	public function transferTokens($get, $post){
    	
    	if($this->getUser()->hasToken('modify_user_permissions')){
    	    
    	    $user = new SmartestSystemUser;
    	    
    	    if($user->find($post['user_id'])){
    	        
    	        if($post['transferAction'] == 'add'){
			        foreach($post['tokens'] as $token_id){
			            $user->addTokenById($token_id, $post['site_id']);
			        }
		        }else{
		            foreach($post['sel_tokens'] as $token_id){
			            $user->removeTokenById($token_id, $post['site_id']);
			        }
		        }
		    
	        }else{
	            
	            $this->addUserMessageToNextRequest('The user ID was not recognized.', SmartestUserMessage::ERROR);
	            
	        }
		
	    }else{
	        
	        $this->addUserMessageToNextRequest('You do not have the permissions needed to modify the permissions of other users.', SmartestUserMessage::ERROR);
	        
	    }
		
		$this->formForward();
		
	}
	
	public function deleteUser($get){
	    
    	$user = new SmartestUser;
    	$user_id = (int) $get['user_id'];
    	
    	if($user_id == $this->getUser()->getId()){
    	
    	    $this->addUserMessageToNextRequest("You can't delete your own account.", SmartestUserMessage::WARNING);
    	
    	}else{
    	
    	    if($user->hydrate($user_id)){
    	        
    	        $this->addUserMessageToNextRequest("The user '".$user->getUsername()."' was successfully deleted.", SmartestUserMessage::SUCCESS);
		        $user->delete();
		
	        }else{
	            
	            $this->addUserMessageToNextRequest("The user ID was not recognized.", SmartestUserMessage::ERROR);
	            
	        }
	    
        }
		
		$this->formForward();
	}
    
    public function updateUser($get, $post){
		
		if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
		
    		$user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
		    
    		    $user->setFirstname($post['user_firstname']);
    		    $user->setLastname($post['user_lastname']);
    		    $user->setEmail($post['user_email']);
    		    $user->setWebsite($post['user_website']);
    		    $user->setBio(addslashes($post['user_bio']));
    		    
    		    if(isset($post['password']) && strlen($post['password']) && $post['password'] == $post['passwordconfirm']){
    		        $user->setPasswordWithSalt($post['password'], SmartestStringHelper::random(40));
    		        $this->addUserMessageToNextRequest("The user has been updated, including a new password.");
    	        }else{
    		        $this->addUserMessageToNextRequest("The user has been updated.");
    		        if($this->getUser()->hasToken('require_user_password_change')){
    		            if($this->getRequestParameter('require_password_change')){
        		            $user->setPasswordChangeRequired(1);
    		            }else{
    		                $user->setPasswordChangeRequired(0);
    		            }
        		    }
    	        }
	        
    	        $user->save();
	        
    		}else{
    		    $this->addUserMessageToNextRequest("The User ID was not recognised.", SmartestUserMessage::ERROR);
    		}
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You do not have permission to edit other users.", SmartestUserMessage::ACCESS_DENIED);
	        
	    }
		
		$this->formForward();
		
	}
	
	public function uploadUserProfilePic(){
	    
	    if($this->getRequestParemeter('user_id')){
	        
	        $user = new SmartestSystemUser;
	        
	        if($user->find($this->getRequestParemeter('user_id'))){
	            
	            
	            
	        }else{
	            $this->addUserMessageToNextRequest("The user ID was not recognized.", SmartestUserMessage::ERROR);
	        }
	        
	    }
	    
	}
	
	///////////////////////////////// ROLES ////////////////////////////////////
	
	public function listRoles(){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('roles');
	    $h = new SmartestUsersHelper;
		$roles = $h->getRoles(false);
		$this->send($roles, 'roles');
		$this->send(count($roles), 'num_roles');
	    
	}
	
	public function addRole(){
	    
	}
	
	public function insertRole($get, $post){
	    
	    $h = new SmartestUsersHelper;
	    
	    if($h->roleNameExists($post['role_label'])){
	        $this->addUserMessage('A role with that name already exists', SmartestUserMessage::WARNING);
	        $this->forward('users', 'addRole');
	    }else if(!strlen($post['role_label'])){
	        $this->addUserMessage('You must enter a name for the role', SmartestUserMessage::WARNING);
	        $this->forward('users', 'addRole');
	    }
	    
	    if(strlen($post['role_label'])){
	        
	        $role = new SmartestRole;
	        $role->setLabel($post['role_label']);
	        $role->save();
	        
	        $this->redirect('/'.$this->getRequest()->getModule().'/editRoleTokens?role_id='.$role->getId());
	        
	    }
	}
	
	public function deleteRole($get){
	    
	    $role = new SmartestRole;
	    
	    if($role->hydrate($get['role_id'])){
	        $role->delete();
	    }
	    
	    $this->formForward();
	    
	}
	
	public function editRoleTokens($get){
	    
	    $this->setFormReturnUri();
	    
    	$role_id = $get['role_id'];	
    	$role = new SmartestRole;
    	
    	if($role->find($role_id)){
    	
    	    $tokens = $role->getTokens();
		    $utokens = $role->getUnusedTokens();
		    
		    // $this->addUserMessage('Editing this role will not affect users created with it. To edit the permission of specific users, select the user and choose the \'Edit user tokens\' option.');
		    
		    $this->send($role, 'role');
		    $this->send($tokens, 'tokens');
		    $this->send($utokens, 'utokens');
		    
	    }else{
	        
	    }
    	
	}

	public function transferTokensToRole($get, $post){
    	
    	$role = new SmartestRole;
    	
    	if($role->find($this->getRequestParameter('role_id'))){
    	    if($this->getRequestParameter('transferAction') == 'add'){
    	        $role->addTokensById($this->getRequestParameter('tokens'));
    		}else{
    		    $role->removeTokensById($this->getRequestParameter('sel_tokens'));
    		}
    	}else{
    	    $this->addUserMessageToNextRequest("The role ID was not recognized.", SmartestUserMessage::ERROR);
    	}
    	
    	$this->formForward();
		
	}
	
	public function editMyProfile(){
	    
	    $this->send($this->getUser(), 'user');
	    $this->send($this->getUser()->hasToken('allow_username_change'), 'allow_username_change');
	    $this->send($this->getUser()->getTwitterHandle(), 'twitter_handle');
	    
	}
	
	public function updateMyProfile(){
	    
	    $username = str_replace(' ', '_', $this->getRequestParameter('username'));
	    
	    if($this->getUser()->hasToken('allow_username_change') && strlen($username) > 3 && strlen($username) < 41){
	        if($username != $this->getUser()->getUsername()){
	            $suh = new SmartestUsersHelper;
	            if(!$suh->usernameExists($username, $this->getUser()->getId())){
	                $this->getUser()->setUsername($this->getRequestParameter('username'));
                }
            }
	    }
	    
	    if($this->getUser()->hasToken('allow_username_change') && strlen($this->getRequestParameter('user_firstname')) > 3){
	        $this->getUser()->setFirstName($this->getRequestParameter('user_firstname'));
	    }
	    
	    $this->getUser()->setLastName($this->getRequestParameter('user_lastname'));
	    
	    if(SmartestStringHelper::isEmailAddress($this->getRequestParameter('user_email'))){
	        $this->getUser()->setEmail($this->getRequestParameter('user_email'));
        }
	    
	    $this->getUser()->setTwitterHandle($this->getRequestParameter('user_twitter'));
	    
	    if($this->getRequestParameter('user_website') != 'http://'){
	        $this->getUser()->setWebsite($this->getRequestParameter('user_website'));
        }
        
	    $this->getUser()->setBio($this->getRequestParameter('user_bio'));
	    $this->getUser()->save();
	    
	    $this->addUserMessageToNextRequest('Your user profile has been updated.', SmartestUserMessage::SUCCESS);
	    $this->redirect('/smartest/profile');
	    
	}
	
	public function setMyPassword(){
	    
	    
	}
	
	public function updateMyPassword(){
	    
	    if(strlen($this->getRequestParameter('password_1')) < 8){
	        $this->addUserMessage("Your password must be eight or more characters.", SmartestUserMessage::INFO);
	        $this->forward('users', 'setMyPassword');
	    }else if($this->getRequestParameter('password_1') != $this->getRequestParameter('password_2')){
	        $this->addUserMessage("The passwords you entered didn't match.", SmartestUserMessage::WARNING);
	        $this->forward('users', 'setMyPassword');
        }else if($this->getRequestParameter('password_1') == 'password'){
	        $this->addUserMessage("Your password can't be 'password'. Come on, you can do better than that!", SmartestUserMessage::INFO);
	        $this->forward('users', 'setMyPassword');
	    }else{
	        $salt = SmartestStringHelper::random(40);
	        
	        if($this->getUser()->setPasswordWithSalt($this->getRequestParameter('password_1'), $salt)){
	            $this->getUser()->setPasswordChangeRequired('0');
	            $this->getUser()->save();
    	        $this->addUserMessageToNextRequest("Your password has been successfully updated.", SmartestUserMessage::SUCCESS);
    	        $this->redirect('/smartest/profile');
    	    }else{
    	        $this->addUserMessage("That password is the same. Please try again.", SmartestUserMessage::INFO);
    	        $this->forward('users', 'setMyPassword');
    	    }
	    }
	}
    
}