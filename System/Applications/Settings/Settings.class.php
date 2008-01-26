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


class Settings extends SmartestApplication{

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

	function cartSettings(){
		return $this->manager->getSettings();    
	}
  
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
		$users = $this->database->queryToArray("SELECT * FROM Users");
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
	    // get roles
	    $result = $this->database->queryToArray("SELECT * FROM Roles");
	    
	    $roles = array();
	    
	    foreach($result as $role_array){
	        $role = new SmartestRole;
	        $role->hydrate($role_array);
	        $roles[] = $role->__toArray();
	    }
	    
	    $this->send($roles, 'roles');
	    
	}

 	function addUserAction($get, $post){
		
		$user = new SmartestUser;
        
		$username = $post['username'];
		$password = $post['password'];
		$firstname = $post['user_firstname'];
		$lastname = $post['user_lastname'];
		$email = $post['user_email'];
		$website = $post['user_website'];
		$hash = md5($post['password']);
		
		$user->setUsername($username);
		$user->setPassword($hash);
		$user->setFirstname($firstname);
		$user->setLastname($lastname);
		$user->setEmail($email);
		$user->setWebsite($website);
		
		$user->save();
		
		$site_id = $post['site_id'];
		
        // add user tokens
        $role_id = $post['user_role'];
        
        $role = new SmartestRole;
        
        if($role->hydrate($role_id)){
            $tokens = $role->getTokens();
            
            foreach($tokens as $t){
                $user->addToken($t->getId(), $site_id);
            }
        }
        
        // print_r($user);
        
    	/* $update_password = null;
    	$update_password_wordpress = null;
    
    	if(strlen($password) > 1){
    		$update_password = "password='$hash',"; 
    		$update_password_wordpress = "password='$hash',"; 
    	}
    
    	$sql = "INSERT INTO Users (username, password, user_firstname, user_lastname, user_displayname,  user_email, user_website, user_aim, user_yahooim, user_gtalk) 
VALUES ('$username', '$hash', '$firstname',  '$lastname', 'displayname', '$email', '$website', '$aim', '$yahooim', '$gtalk' ) ";
    	$this->manager->database->rawQuery($sql);  */
    
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
    	$database = $_SESSION['database'];
		$user_id = $get['user_id'];
		$query = "DELETE FROM Users WHERE user_id='$user_id' LIMIT 1";
		$database->query($query);
		$this->formForward();
	}
	
	function showModels($get){
		
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
	}
	
	function editUserTokens($get){
	    
	    if($this->getUser()->hasToken('modify_user_permissions')){
	    
	        $permission_editable_sites = $this->getUser()->getPermissionEditableSitesAsArrays();
	        
	        // print_r($permission_editable_sites);
	        
    	    $user_id = $get['user_id'];	
        	$user_being_edited = new SmartestUser;
    	
        	if($user_being_edited->hydrate($user_id)){
    	
        	    $this->setFormReturnUri();
    	        
    	        $allow_global = ($this->getUser()->hasGlobalPermission('modify_user_permissions') || $this->getUser()->hasToken('grant_global_permissions')) ? true : false;
    	        
    	        if(isset($get['site_id']) && strlen($get['site_id'])){
    	            $site_id = $get['site_id'];
    	        }else{
    	            $site_id = $this->getSite()->getId();
    	        }
    	        
        	    $utokens = $this->manager->getUserPermissions($user_id, $site_id); //print_r($utokens);
    		    $tokens = $this->manager->getAvailablePermissions($user_id, $site_id);
    		    
		        $this->send($user_being_edited->__toArray(), 'user');
    		    $this->send($utokens, 'utokens');
    		    $this->send($tokens, 'tokens');
    		    $this->send($permission_editable_sites, 'sites');
    		    $this->send($site_id, 'site_id');
    		    $this->send($allow_global, 'allow_global');
		    
        	    // return array("user_id"=>$user_id, "utokens"=>$utokens,"tokens"=>$tokens);
    	
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
		    
		    $this->addUserMessage('Editing this role will not affect users created with it. To edit the permission of specific users, ');
		    
		    // print_r($tokens);
		    
		    // print_r($user);
		    
		    $this->send($role->__toArray(), 'role');
		    $this->send($utokens, 'utokens');
		    $this->send($tokens, 'tokens');
		    
    	    // return array("user_id"=>$user_id, "utokens"=>$utokens,"tokens"=>$tokens);
    	
	    }else{
	        
	    }
    	
	}

	function transferTokens($get, $post){
    	
    	if($this->getUser()->hasToken('modify_user_permissions')){
    	
    	    if($post['transferAction'] == 'add'){
			    $this->manager->addTokensToUser($post['tokens'], $post['user_id'], $post['site_id']);
		    }else{
			    $this->manager->removeTokensFromUser($post['sel_tokens'], $post['user_id'], $post['site_id']);
		    }
		
	    }else{
	        
	        $this->addUserMessageToNextRequest('You do not have the permissions needed to modify the permissions of other users.');
	        
	    }
		
		$this->formForward();
		
	}
	
	function transferTokensToRole($get, $post){
    	
    	// if($this->getUser()->hasToken('modify_user_permissions')){
    	
    	    if($post['transferAction'] == 'add'){
			    $this->manager->addTokensToRole($post['tokens'], $post['role_id']);
		    }else{
			    $this->manager->removeTokensFromRole($post['sel_tokens'], $post['role_id']);
		    }
		    
		    // print_r($this->database);
		
	    // }else{
	        
	    //    $this->addUserMessageToNextRequest('You do not have the permissions needed to modify the permissions of other users');
	        
	    // }
		
		$this->formForward();
		
	}

}