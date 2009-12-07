<?php

/**
 * Contains the Settings module for website
 *
 * PHP versions 5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Eddie Tejeda <eddie@visudo.com>
 */


class Settings extends SmartestSystemApplication{

	function __moduleConstruct(){
				
	}
	
	function startPage(){
		
	}
	
	function checkForUpdates(){
		
		// latest
		$contents = file_get_contents("http://update.visudo.net/smartest");
		$unserializer = &new XML_Unserializer(); 
		$status = $unserializer->unserialize($contents); 		
		
		if (PEAR::isError($status)) { 
			 die($status->getMessage()); 
		}
		
		$latest = $unserializer->getUnserializedData();

		//current
		$contents = file_get_contents("System/CoreInfo/package.xml");
		$unserializer = &new XML_Unserializer(); 
		$status = $unserializer->unserialize($contents); 		
		
		if (PEAR::isError($status)) { 
			 die($status->getMessage()); 
		}
		
		$current = $unserializer->getUnserializedData();
		
		$release = false; 
		
		if($latest['release']['version'] > $current['release']['version']){
			$release = $latest;
		}else if($latest['release']['version'] < $current['release']['version']){
			$release = "downgrade";
		}
		
		return (array("release"=>$release,"settings"=>$this->manager->getSettings())); 
	}

	function updateGeneral($get, $post){
    
		$post = array_filter($post, array($this->manager, "filterSubmit"));
		return $this->manager->setSettings($post);
    
	}

	/* function cartSettings(){
		return $this->manager->getSettings();    
	} */
  
	function updateUser($get, $post){
		
		$user = new SmartestUser;
		
		if($user->hydrate($post['user_id'])){
		    
		    $user->setFirstname($post['user_firstname']);
		    $user->setLastname($post['user_lastname']);
		    $user->setEmail($post['user_email']);
		    $user->setWebsite($post['user_website']);
		    $user->setBio(addslashes($post['user_bio']));
		    
		    if(isset($post['password']) && strlen($post['password']) && $post['password'] == $post['passwordconfirm']){
		        $user->setPassword(md5($post['password']));
		        $this->addUserMessageToNextRequest("The user has been updated, including a new password.");
	        }else{
		        $this->addUserMessageToNextRequest("The user has been updated.");
	        }
	        
	        $user->save();
	        
		}else{
		    $this->addUserMessageToNextRequest("The User ID was not recognised.");
		}
		
		/* $user_id = $post['user_id'];
		$username = $post['username'];
		$password = $post['password'];
		$firstname = $post['user_firstname'];
		$lastname = $post['user_lastname'];
		$email = $post['user_email'];
		$website = $post['user_website'];
		
		$hash = md5($post['password']);
		
		$update_password = null;
		$update_password_wordpress = null;
		
		if(strlen($password) > 1){
			$update_password = "password='$hash',"; 
			$update_password_wordpress = "password='$hash',";
		}

		$sql = "UPDATE Users SET ".$update_password."
			user_firstname='$firstname', 
			user_lastname='$lastname',
			user_displayname= '$displayname' ,
			user_email='$email' ,
			user_website= '$website',
			user_aim='$aim',
			user_yahooim='$yahooim',
			user_gtalk='$gtalk'";
		
		$sql .=	" WHERE user_id=$user_id";
		
		$this->database->rawQuery($sql); */
		
		$this->formForward();
	}

    function listUsers(){
		
		$this->setFormReturnUri();
		$users = $this->database->queryToArray("SELECT * FROM Users WHERE username != 'smartest'");
		return (array("users"=>$users, "count"=>count($users)));
		
	}
	
	function listRoles(){
	    
	    $this->setFormReturnUri();
		$roles = $this->database->queryToArray("SELECT * FROM Roles");
		return (array("roles"=>$roles, "count"=>count($roles)));
	    
	}
	
	function addRole(){
	    
	}
	
	function addRoleAction($get, $post){
	    if(strlen($post['role_label'])){
	        
	        $role = new SmartestRole;
	        $role->setLabel($post['role_label']);
	        $role->save();
	        
	        $this->redirect('/'.SM_CONTROLLER_MODULE.'/editRoleTokens?role_id='.$role->getId());
	        
	    }
	}
	
	function deleteRole($get){
	    $role = new SmartestRole;
	    
	    if($role->hydrate($get['role_id'])){
	        $role->delete();
	    }
	    
	    $this->formForward();
	    
	}
    
	function addUser(){
	    
	    $uhelper = new SmartestUsersHelper;
	    $roles = $uhelper->getRolesAsArrays();
	    
	    if($this->getUser()->hasToken('create_users')){
	        $this->send($roles, 'roles');
	    }else{
	        $this->addUserMessageToNextRequest("You don't have permission to add new user accounts", SmartestUserMessage::ACCESS_DENIED);
	        $this->redirect('/smartest/users');
	    }
	    
	}

 	function addUserAction($get, $post){
		
		$user = new SmartestUser;
        
        $username = $post['username'];
        
        if($user->hydrateBy('username', $username)){
            
            $this->addUserMessageToNextRequest("The username you entered is already is use.");
            
        }else{
        
		    $password = $post['password'];
    		$firstname = $post['user_firstname'];
    		$lastname = $post['user_lastname'];
    		$email = $post['user_email'];
    		$website = $post['user_website'];
    		$hash = md5($post['password']);
    		
    		if(!SmartestStringHelper::isValidExternalUri($website)){
    		    $website = 'http://'.$website;
    		}
		
    		$user->setUsername($username);
    		$user->setPassword($hash);
    		$user->setFirstname($firstname);
    		$user->setLastname($lastname);
    		$user->setEmail($email);
    		$user->setWebsite($website);
		    
		    $user->save();
		
    		// add user tokens
            $role_id = $post['user_role'];
        
            $role = new SmartestRole;
            $site_id = $this->getSite()->getId();
        
            if($role->hydrate($role_id)){
                $tokens = $role->getTokens();
            
                foreach($tokens as $t){
                    // echo "Trying to add token ".$t->getCode()."<br />";
                    $user->addToken($t->getCode(), $site_id);
                }
            }
        
        }
    
		$this->formForward();
		
	}

    function editUser($get){
  
		// $database = $_SESSION['database'];
		$user = new SmartestUser;
		
		if($user->hydrate($get['user_id'])){
		    // $user = $this->database->queryToArray("SELECT * FROM Users WHERE user_id=".$get['user_id']);
            // return array("userdetails"=>$user->__toArray());
            $this->send($user->__toArray(), 'user');
        }else{
            $this->addUserMessageToNextRequest("The User ID was not recognised.");
            $this->formForward();
        }
        
	}
	
	function deleteUser($get){
	    
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
	
	/* function showModels($get){
		
		$user_id = $get['user_id'];
		$sql = "SELECT * FROM ItemClasses WHERE itemclass_userid = '$user_id'";
		$models = $this->database->queryToArray($sql);
		$username = $this->database->specificQuery("username", "user_id", $user_id, "Users");
		return array("models" =>$models, "username"=>$username, "itemClassCount"=>count($models));
		
	}

	function showPages($get){

		$user_id = $get['user_id'];
		$sql = "SELECT * FROM Pages WHERE page_createdby_userid = '$user_id'";
		$pages = $this->database->queryToArray($sql);
		$username = $this->database->specificQuery("username", "user_id", $user_id, "Users");
		return array("pages" =>$pages, "username"=>$username, "pageCount"=>count($pages));
	} */
	
	public function editUserTokens($get){
	    
	    if($this->getUser()->hasToken('modify_user_permissions')){
	    
	        $permission_editable_sites = $this->getUser()->getPermissionEditableSites();
	        
	        $user_id = $get['user_id'];	
        	$user = new SmartestSystemUser;
    	
        	if($user->hydrate($user_id)){
    	
        	    $this->setFormReturnUri();
    	        
    	        $allow_global = ($this->getUser()->hasGlobalPermission('modify_user_permissions') || $this->getUser()->hasToken('grant_global_permissions')) ? true : false;
    	        
    	        if(isset($get['site_id']) && strlen($get['site_id'])){
    	            $site_id = $get['site_id'];
    	        }else{
    	            $site_id = $this->getSite()->getId();
    	        }
    	        
    	        // $h = new SmartestUsersHelper;
        	    // $utokens = $this->manager->getUserPermissions($user_id, $site_id); // print_r($utokens);
        	    // $tokens = $h->getUserPermissions();
        	    // print_r($user);
        	    $utokens = $user->getTokensOnSite($site_id, true);
        	    
        	    // $t = $user->getTokenCodes();
        	    // print_r($utokens);
    		    
    		    $tokens = $user->getAvailableTokens($site_id);
    		    
    		    // $tokens_old = $this->manager->getAvailablePermissions($user_id, $site_id);
    		    // $tokens = SmartestUsersHelper::getTokenData();
    		    
    		    /* print_r($tokens);
    		    print_r($tokens_old); */
    		    
		        $this->send($user, 'user');
    		    $this->send($utokens, 'utokens');
    		    $this->send($tokens, 'tokens');
    		    $this->send($permission_editable_sites, 'sites');
    		    $this->send($site_id, 'site_id');
    		    $this->send($allow_global, 'allow_global');
		    
        	    // return array("user_id"=>$user_id, "utokens"=>$utokens,"tokens"=>$tokens);
        	    
        	    // header("Location: /");
    	
    	    }else{
    	        $this->addUserMessageToNextRequest("The user ID was not recognised");
    	        $this->formForward();
    	    }
	    
        }else{
            $this->addUserMessageToNextRequest("You don't have permission to edit user permissions.");
	        $this->formForward();
        }
    	
	}
	
	function editRoleTokens($get){
	    
	    $this->setFormReturnUri();
	    
    	$role_id = $get['role_id'];	
    	$role = new SmartestRole;
    	
    	if($role->hydrate($role_id)){
    	
    	    $utokens = $this->manager->getRolePermissions($role_id); //print_r($utokens);
		    $tokens = $this->manager->getRoleAvailablePermissions($role_id);
		    
		    // $this->addUserMessage('Editing this role will not affect users created with it. To edit the permission of specific users, select the user and choose the \'Edit user tokens\' option.');
		    
		    $this->send($role->__toArray(), 'role');
		    $this->send($utokens, 'utokens');
		    $this->send($tokens, 'tokens');
		    
	    }else{
	        
	    }
    	
	}

	function transferTokens($get, $post){
    	
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
	
	function transferTokensToRole($get, $post){
    	
    	if($post['transferAction'] == 'add'){
		    $this->manager->addTokensToRole($post['tokens'], $post['role_id']);
		}else{
		    $this->manager->removeTokensFromRole($post['sel_tokens'], $post['role_id']);
		}
		
		$this->formForward();
		
	}

}