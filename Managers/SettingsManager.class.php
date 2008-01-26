<?php
/**
 * Implements the installer file
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

    
class SettingsManager {

	private $database;

 /**
  * description
  * @access public
  */
	function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
	}
  
    function getSetting($name){
        $sql = "SELECT setting_value FROM Settings WHERE setting_name='$name'";
        $setting = $this->database->query($sql);
        return $setting[0];
    }
  
    function getSettings(){
        $sql = "SELECT * FROM Settings";
        $setting = $this->database->query($sql);
        $setting_asc = null;
    
        if(is_array($setting)){
            foreach($setting as $setting){
                $setting_asc[$setting['setting_name']] = $setting['setting_value'];
            }
        }
        
        return $setting_asc;  
    }
  
    function setSetting($name, $value){
        
    }

    function setSettings($settings){

      foreach($settings as $key => $setting){
            $sql = "UPDATE Settings SET setting_value='$setting' WHERE setting_name='$key'";     
            $this->database->rawQuery($sql);
        }

    }
  
    function filterSubmit($var){
        if( !strstr( $var , "Update Options") ){
            return true;
        }
    }
  
    function getUserTokens($user_id, $site_id){
    	
    	if($site_id == 'GLOBAL'){
            $sql = "SELECT * FROM UserTokens, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id = '$user_id' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UsersTokensLookup.utlookup_is_global='1'";
	    }else{
	        $sql = "SELECT * FROM UserTokens, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id = '$user_id' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UsersTokensLookup.utlookup_site_id='".$site_id."' AND UsersTokensLookup.utlookup_is_global!='1'";
	    }
	    
    	$utokens=$this->database->queryToArray($sql);
	    return $utokens;
    }
    
    function getUserPermissions($user_id, $site_id){
        
        if($site_id == 'GLOBAL'){
            $sql = "SELECT * FROM UserTokens, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id = '$user_id' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UserTokens.token_type='permission' AND UsersTokensLookup.utlookup_is_global='1'";
	    }else{
	        $sql = "SELECT * FROM UserTokens, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id = '$user_id' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UserTokens.token_type='permission' AND UsersTokensLookup.utlookup_site_id='".$site_id."' AND UsersTokensLookup.utlookup_is_global!='1'";
	    }
	    
    	$utokens = $this->database->queryToArray($sql);
	    return $utokens;
    }
    
    function getRolePermissions($role_id){
    	$sql = "SELECT * FROM UserTokens, RolesTokensLookup WHERE RolesTokensLookup.rtlookup_role_id = '".$role_id."' AND  RolesTokensLookup.rtlookup_token_id=UserTokens.token_id AND UserTokens.token_type='permission'";
    	$utokens = $this->database->queryToArray($sql);
	    return $utokens;
    }

    function getAvailableTokens($user_id, $site_id){
    	
    	$sql = "SELECT * FROM UserTokens";
    	$tokens = $this->database->queryToArray($sql);
	    
	    foreach($tokens as $key=>$value){
		
    		$id = $value['token_id'];
    		$count_query = "SELECT * FROM UsersTokensLookup, UserTokens WHERE UsersTokensLookup.utlookup_user_id = '$user_id' AND UsersTokensLookup.utlookup_token_id='$id' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND (UsersTokensLookup.utlookup_site_id='".$site_id."' OR UsersTokensLookup.utlookup_is_global='1')";
    		$count = $this->database->howMany($count_query);
		
    		if($count <= 0){
    		    $token[]=$value;
    		}
    	}

	    return $token;
    }
    
    function getAvailablePermissions($user_id, $site_id){

        $sql = "SELECT * FROM UserTokens WHERE token_type='permission'";
    	$tokens = $this->database->queryToArray($sql);

	    foreach($tokens as $key=>$value){

    		$id = $value['token_id'];
    		$count_query="SELECT * FROM UsersTokensLookup, UserTokens WHERE UsersTokensLookup.utlookup_user_id = '$user_id' AND UsersTokensLookup.utlookup_token_id='$id' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UserTokens.token_type='permission' AND (UsersTokensLookup.utlookup_site_id='".$site_id."' OR UsersTokensLookup.utlookup_is_global='1')";
    		$count = $this->database->howMany($count_query);

    		if($count <= 0){
    		    $token[] = $value;
    		}
    	}

	    return $token;
    }
    
    function getRoleAvailablePermissions($role_id){

        $sql = "SELECT * FROM UserTokens WHERE token_type='permission'";
    	$tokens = $this->database->queryToArray($sql);
    	
    	$role = new SmartestRole;
    	
    	if($role->hydrate($role_id)){
    	    
    	    $role_tokens = $role->getTokens();
    	    
    	    $role_token_ids = array();
    	    
    	    foreach($role_tokens as $rt){
    	        $role_token_ids[] = $rt->getId();
    	    }
    	    
    	    // print_r($tokens);
    	    
    	    // $remove_keys = 
    	    
    	    // go through the list
    	    foreach($tokens as $key=>$value){

        		/* $id = $value['token_id'];
        		$count_query="SELECT * FROM RolesTokensLookup, UserTokens WHERE RolesTokensLookup.rtlookup_user_id = '$role_id' AND RolesTokensLookup.rtlookup_token_id='$id' AND RolesTokensLookup.rtlookup_token_id=UserTokens.token_id AND UserTokens.token_type='permission'";
    		    $count = $this->database->howMany($count_query);

    		    if($count <= 0){
    		        $token[] = $value;
    		    } */
    		    
    		    // if the item on the list is already attached to the role, unattach it
    		    if(in_array($value['token_id'], $role_token_ids)){
    		        unset($tokens[$key]);
    		        // print_r($tokens[$key]);
    		        // echo 'unset token with ID:'.$value['token_id'].' <br />';
    		    }
    		    
    	    }
    	    
    	    $tokens = array_values($tokens);
    	    
    	    // print_r($role_token_ids);
	    }

	    return $tokens;
    }
    
	function addTokensToUser($tokens, $user_id, $site_id){
		
		$user = new SmartestUser;
		
		if($user->hydrate($user_id)){
		    
		    foreach($tokens as $key => $value){
		        
		        // $user->addToken($value);
		        if($site_id == 'GLOBAL'){
		            $sql = "INSERT INTO UsersTokensLookup (utlookup_user_id, utlookup_token_id, utlookup_granted_timestamp, utlookup_site_id, utlookup_is_global) VALUES ('$user_id', '$value', '".time()."', NULL, 1)";
		        }else{
		            $sql = "INSERT INTO UsersTokensLookup (utlookup_user_id, utlookup_token_id, utlookup_granted_timestamp, utlookup_site_id, utlookup_is_global) VALUES ('$user_id', '$value', '".time()."', '".$site_id."', 0)";
	            }
	            
		        $this->database->rawQuery($sql);
		        // $user->addToken()
		    }
		}
	}
	
	function removeTokensFromUser($roles, $user_id, $site_id){
		
		foreach($roles as $key=>$value){
		    
		    if($site_id == 'GLOBAL'){
	            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_user_id = '$user_id' AND utlookup_token_id='$value' AND utlookup_is_global='1' LIMIT 1";
            }else{
                $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_user_id = '$user_id' AND utlookup_token_id='$value' AND utlookup_site_id='".$site_id."' LIMIT 1";
            }
            
	        $this->database->rawQuery($sql);
		}
	}
	
	function addTokensToRole($tokens, $role_id){
		
		$role = new SmartestRole;
		
		if($role->hydrate($role_id)){
		    
		    foreach($tokens as $key => $value){
		        
		        // $user->addToken($value);
		        
		        $sql = "INSERT INTO RolesTokensLookup (rtlookup_role_id, rtlookup_token_id) VALUES ('".$role_id."', '".$value."')";
		        $this->database->rawQuery($sql);
		    }
		}
	}
	
	function removeTokensFromRole($tokens, $role_id){
		foreach($tokens as $key=>$value){
	        $sql = "DELETE FROM RolesTokensLookup WHERE rtlookup_role_id = '$role_id' AND rtlookup_token_id='".$value."' LIMIT 1";
	        $this->database->rawQuery($sql);
		}
	}
	
//  function updateTokens($user_id,$token){
//     	$sql = "DELETE FROM UsersTokensLookup WHERE utlookup_user_id = '$user_id'";
//     	$this->database->query($sql);
// 	foreach($token as $key=>$value){
// 	$sql="INSERT INTO UsersTokensLookup (utlookup_user_id,utlookup_token_id,utlookup_granted_timestamp) VALUES ('$user_id','$value',".time().")";//echo $sql;
// 	$this->database->rawQuery($sql);	
// 	}
//   }
  
}