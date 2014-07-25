<?php

class SmartestUser extends SmartestBaseUser implements SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue{
	
	protected $_tokens = array();
	protected $_site_ids = array();
	protected $_model_plural_names = array();
	protected $_parameters; // Only useful when the user is being stored in the session
	protected $_user_info;
    protected $_user_info_modified;
	
	protected function __objectConstruct(){
	    
		if(method_exists($this, '__myConstructor')){
		    $args = func_get_args();
		    $this->__myConstructor($args);
		}
		
		$this->_preferences_helper = SmartestPersistentObject::get('prefs_helper');
		
		$this->_user_info = new SmartestDbStorageParameterHolder("User info");
		
	}
	
	public function hydrate($id, $bother_with_tokens=true){
		
		if(is_array($id)){
			
			if(array_key_exists('username', $id) && array_key_exists('password', $id) && array_key_exists('user_id', $id)){
				
				$this->_properties['username'] = $id['username'];
				$this->_properties['password'] = $id['password'];
			
				foreach($id as $key => $value){
					if(substr($key, 0, strlen($this->_table_prefix)) == $this->_table_prefix){
						$this->_properties[substr($key, strlen($this->_table_prefix))] = $value;
						$this->_came_from_database = true;
					}else if(isset($this->_no_prefix[$name])){
						$this->_properties[$name] = $value;
					}
					
				}
				
				if($bother_with_tokens){
				    $this->getTokens();
			    }
                
                if(method_exists($this, '__postHydrationAction')){
                    $this->__postHydrationAction();
                }
				
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
					}else if(isset($this->_no_prefix[$name])){
					    $this->_properties[$name] = $value;
					    $this->_properties_lookup[SmartestStringHelper::toCamelCase($name)] = $name;
					}
				}
			
				$this->_came_from_database = true;
                if(method_exists($this, '__postHydrationAction')){
                    $this->__postHydrationAction();
                }
				return true;
				
			}else{
				return false;
			}
		}
	}
	
	public function __postHydrationAction(){
	    
	    if(!$this->_user_info){
	        $this->_user_info = new SmartestDbStorageParameterHolder("Info for user '".$this->_properties['username']."'");
        }
        
        $this->_user_info->loadArray(unserialize($this->_properties['info']));
	    
	}
	
	public function getUsername(){
		return $this->_properties['username'];
	}
	
	// must have a length of between 4 and 40
	public function setUsername($username){
		if(strlen($username) > 3 && strlen($username) < 41){
		    $username = SmartestStringHelper::toUsername($username);
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
			return true;
		}else{
			if(strlen($password) > 3){
				$this->_properties['password'] = md5($password);
				$this->_modified_properties['password'] = md5($password);
				return true;
			}else{
			    return false;
			}
		}
	}
	
	public function isAuthenticated(){
		
		// only works for the current logged in user
		if(SmartestSession::get('user:isAuthenticated')){
			return true;
		}else{
			return false;
		}
	}
	
	public function __toString(){
	    
	    return $this->getFullName();
	    
	}
	
	public function getFullName(){
	    
	    $full_name = $this->_properties['firstname'];
	    
	    if($this->_properties['firstname']){
	        $full_name .= ' ';
	    }
	    
	    if($this->_properties['lastname']){
	        $full_name .= $this->_properties['lastname'];
	    }
	    
	    return trim($this->_properties['firstname'].' '.$this->_properties['lastname']);
	    
	}
	
	public function __toArray(){
	    
	    $data = parent::__toArray();
	    $data['full_name'] = $this->getFullName();
	    
	    return $data;
	    
	}
	
	protected function getModelPluralNames(){
        
        if(!count($this->_model_plural_names)){
            $du = new SmartestDataUtility;
            $this->_model_plural_names = $du->getModelPluralNamesLowercase($this->getCurrentSiteId());
        }
        
        return $this->_model_plural_names;
    }
	
	public function offsetGet($offset){
	    
	    $offset = strtolower($offset);
	    
	    switch($offset){
	        case "password":
	        case "password_salt":
	        return null;
	        
	        case "full_name":
	        case "fullname":
	        return $this->getFullName();
	        
	        case "profile_pic":
	        return $this->getProfilePic();
	        
	        case "profile_pic_asset_id":
	        return $this->getProfilePicAssetId();
	        
	        case "bio":
	        return new SmartestString($this->getBio());
	        
	        case "website":
	        case "website_url":
	        $url = new SmartestExternalUrl($this->_properties['website']);
	        return $url;
	        
	        case "email":
	        return new SmartestEmailAddress($this->_properties['email']);
	        
	        case "has_twitter_handle":
	        case "has_twitter_account":
	        case "has_twitter_acct":
	        case "has_twitter_username":
	        return (bool) strlen($this->getTwitterHandle());
	        
	        case "twitter_account_object":
	        case "twitter_handle_object":
	        if(strlen($this->getTwitterHandle())){
	            return new SmartestTwitterAccountName($this->getTwitterHandle());
            }else{
                break;
            }
            
            case "info":
            return $this->_user_info;
	        
	        default:
	        if(in_array($offset, array_keys($this->getModelPluralNames()))){
                return $this->getCreditedItemsOnCurrentSite($this->_model_plural_names[$offset]);
            }else if($this->_user_info->hasParameter($offset)){
                return $this->_user_info->getParameter($offset);
            }else{
                return parent::offsetGet($offset);
            }
	        
	    }
	    
	}
	
	public function setDraftMode($mode){
	    $this->_draft_mode = (bool) $mode;
	}
	
	public function getDraftMode(){
	    return $this->_draft_mode;
	}
	
	public function getCreditedItemsOnCurrentSite($model_id=null, $mode='DEFAULT_MODE'){
	    
	    if($mode == 'DEFAULT_MODE'){
	        if($this->getDraftMode()){
	            $mode = 0;
	        }else{
	            $mode = 9;
	        }
	    }
	    
	    if($site_id = $this->getCurrentSiteId()){
            return $this->getCreditedItems($site_id, $model_id, $mode);
        }
    }
    
    public function getProfilePicAssetId(){
        
        if($this->baseClassHasField('profile_pic_asset_id')){
            if($this->_properties['profile_pic_asset_id']){
                return $this->_properties['profile_pic_asset_id'];
            }else{
                return $this->getDefaultProfilePicAssetId();
            }
        }else{
            $this->__call('getProfilePicAssetId', null);
        }
        
    }
    
    public function getDefaultProfilePicAssetId(){
        
        $ph = new SmartestPreferencesHelper;
        
        // does the setting exist?
        if($ph->getGlobalPreference('default_user_profile_pic_asset_id', null, $this->getCurrentSiteId(), true)){
            
            // if so, what is it's value?
            return $ph->getGlobalPreference('default_user_profile_pic_asset_id', null, $this->getCurrentSiteId());
            
        }else{
            
            // if not, create the asset and set the value of the preference to the id of the new asset
            $a = new SmartestAsset;
            $a->setUrl('default_user_profile_pic.jpg');
            $a->setWebid(SmartestStringHelper::random(32));
            $a->setIsSystem(1);
            $a->setStringId('default_user_profile_pic_asset_id');
            $a->setLabel('Default User Profile Picture');
            $a->setType('SM_ASSETTYPE_JPEG_IMAGE');
            $a->setUserId('0');
            $a->setCreated(time());
            $a->setSiteId(1);
            $a->setShared(1);
            $a->save();
            
            $p = $a->getId();
            
            $ph->setGlobalPreference('default_user_profile_pic_asset_id', $p, null, $this->getCurrentSiteId());
            return $p;
            
        }
        
    }
    
    public function getProfilePic(){
        
        if(is_object($this->_profile_pic_asset)){
            
            return $this->_profile_pic_asset;
            
        }else{
            
            $asset = new SmartestRenderableAsset;
            
            if($this->getRequest()->getAction() == 'renderEditableDraftPage'){
                $asset->setDraftMode(true);
            }
            
            if($asset->find($this->getProfilePicAssetId())){
                $this->_profile_pic_asset = $asset;
            }
            
            return $asset;
            
        }
        
    }
	
	public function sendEmail($subject, $message, $from=""){
	    
	    if(!isset($from{0})){
	        $from = 'Smartest <smartest@'.$_SERVER['HTTP_HOST'].'>';
	    }
	    
	    $to = $this->_properties['email'];
	    
	    if(SmartestStringHelper::isEmailAddress($to)){
	        mail($to, $subject, $message, "From: ".$from."\r\nReply-to: ".$from);
	        return true;
        }else{
            SmartestLog::getInstance('system')->log("Could not send e-mail to invalid e-mail address: '".$to."'.");
        }
	    
	}
	
	public function getCreditedItems($site_id=null, $model_id=null, $mode=9){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_ITEM_AUTHORS');
	    $q->setTargetEntityByIndex(2);
	    $q->addQualifyingEntityByIndex(1, $this->_properties['id']);
	    $q->addForeignTableConstraint('Items.item_deleted', '0');
	    $draft_mode = $mode < 6;
	    
	    if($mode > 5){
            $q->addForeignTableConstraint('Items.item_public', 'TRUE');
        }
        
        if(is_numeric($model_id)){
            $q->addForeignTableConstraint('Items.item_itemclass_id', $model_id);
        }
        
        if(is_numeric($site_id)){
            $q->addForeignTableOrConstraints(
	            array('field'=>'Items.item_site_id', 'value'=>$site_id),
	            array('field'=>'Items.item_shared', 'value'=>'1')
	        );
        }
        
        if(in_array($mode, array(1,4,7,10))){
	    
	        $q->addForeignTableConstraint('Items.item_is_archived', '1');
	    
        }else if(in_array($mode, array(2,5,8,11))){
            
            $q->addForeignTableConstraint('Items.item_is_archived', '0');
            
        }
        
        $ids = $q->retrieveIds();
        $ih = new SmartestCmsItemsHelper;
        
        if(is_numeric($model_id)){
            $items = $ih->hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode);
        }else{
            $items = $ih->hydrateMixedListFromIdsArray($ids, $draft_mode);
        }
        
        return new SmartestArray($items);
	    
	}
	
	public function getCreditedWorkOnSite($site_id='', $draft=false){
        
        /*  $master_array = array();
        
        $pages = $this->getPages($site_id, $draft);
        $items = $this->getItems($site_id, $draft);
        
        foreach($pages as $p){
            
            $key = $p->getDate();
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $p;
            
        }
        
        foreach($items as $i){
            
            $key = $i->getDate();
            if($key instanceof SmartestDateTime){
                $key = $key->getUnixFormat();
            }
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $i;
            
        }
        
        krsort($master_array);
        
        return $master_array; */
        
    }
    
    public function getBio(){
        return stripslashes($this->_properties['bio']);
    }

    
    protected function instantiateParameters(){
        if(!is_object($this->_parameters)){
            $this->_parameters = new SmartestParameterHolder("Parameters for user: ".$this->__toString());
        }
    }
    
    public function getParameter($param){
        $this->instantiateParameters();
        return $this->_parameters->getParameter($param);
    }
    
    public function setParameter($param, $value){
        $this->instantiateParameters();
        $this->_parameters->setParameter($param, $value);
    }
    
    public function hasParameter($param){
        $this->instantiateParameters();
        return $this->_parameters->hasParameter($param);
    }

    
    public function passwordIs($password){
        
        return $this->getPassword() == md5($password.$this->getPasswordSalt());
        
    }
    
    public function setPasswordWithSalt($raw_password, $salt, $ignore_repeat_password=false){
        if($this->passwordIs($raw_password) && !$ignore_repeat_password){
            return false;
        }else{
            $this->setPasswordSalt($salt);
            $this->setField('password', md5($raw_password.$salt));
            $this->setPasswordLastChanged(time());
            return true;
        }
    }
    
    public function setInfoValue($field, $new_data){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    // URL Encoding is being used to work around a bug in PHP's serialize/unserialize. No actual URLS are necessarily in use here:
	    $this->_user_info->setParameter($field, rawurlencode(utf8_decode($new_data)));
        $this->_user_info_modified = true;
	    $this->_modified_properties['info'] = SmartestStringHelper::sanitize(serialize($this->_user_info->getArray()));
	    
	}
	
	public function getInfoValue($field){
	    
	    $field = SmartestStringHelper::toVarName($field);
        
        if($this->_user_info->hasParameter($field)){
            return $this->_user_info->getParameter($field);
	    }else{
	        return null;
	    }
	}
    
    public function delete(){
        
        if($this->getId() > 0 && $this->getUsername() != 'smartest'){ // The Smartest user, ID zero, should never be deletable
        
            // release all pages, files and items
            $sql = "UPDATE Pages SET page_held_by='0', page_is_held='0', page_createdby_userid='0' WHERE page_held_by='".$this->getId()."'";
            $this->database->rawQuery($sql);
            $sql = "UPDATE Items SET item_held_by='0', item_is_held='0', item_createdby_userid='0' WHERE item_held_by='".$this->getId()."'";
            $this->database->rawQuery($sql);
            $sql = "UPDATE Assets SET asset_held_by='0', asset_is_held='0', asset_user_id='0' WHERE asset_held_by='".$this->getId()."'";
            $this->database->rawQuery($sql);
        
            // delete "recently edited" records
            $sql = "DELETE FROM ManyToManyLookups WHERE (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_ASSETS' AND mtmlookup_entity_2_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_PAGES' AND mtmlookup_entity_2_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_ITEMS' AND mtmlookup_entity_2_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_TEMPLATES' AND mtmlookup_entity_2_foreignkey='".$this->getId()."')";
            $this->database->rawQuery($sql);
        
            // delete authorship records
            $sql = "DELETE FROM ManyToManyLookups WHERE (mtmlookup_type='SM_MTMLOOKUP_ITEM_AUTHORS' AND mtmlookup_entity_1_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_PAGE_AUTHORS' AND mtmlookup_entity_1_foreignkey='".$this->getId()."')";
            $this->database->rawQuery($sql);
        
            // delete all tokens/permissions
            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_user_id='".$this->getId()."'";
            $this->database->rawQuery($sql);
        
            // delete all settings
            $sql = "DELETE FROM Settings WHERE setting_user_id='".$this->getId()."'";
            $this->database->rawQuery($sql);
        
            parent::delete();
        
        }
        
    }
    
    public function getStorableFormat(){
        return $this->getId();
    }
    
    public function hydrateFromStorableFormat($v){
        return $this->find($v);
    }
    
    public function hydrateFromFormData($v){
        return $this->find($v);
    }
    
    public function renderInput($params){}
    
    public function setValue($v){
        if(is_numeric($v)){
            return $this->find($v);
        }else if(SmartestStringHelper::isValidUsername($v)){
            return $this->findBy('username', $v);
        }else{
            return false;
        }
    }
    
    public function getValue(){
        return $this->getId();
    }
    
    // Note - __toString() is above
    
    public function isPresent(){
        return $this->_came_from_database || count($this->_modified_properties);
    }
    
    ////////////////////////////////// Todo list stuff ///////////////////////////////////////
	
	public function assignTodo($type_code, $entity_id, $assigner_id=0, $input_message='', $send_email=false){
	    
	    /* $type = SmartestTodoListHelper::getType($type_code);
	    
	    if(isset($message{1})){
	        $input_message = SmartestStringHelper::sanitize($message);
	    }else{
	        $input_message = $type->getDescription();
	    } */
	    
	    $task = new SmartestTodoItem;
	    $task->setReceivingUserId((int) $this->_properties['id']);
	    $task->setAssigningUserId((int) $assigner_id);
	    $task->setForeignObjectId((int) $entity_id);
	    $task->setTimeAssigned(time());
	    $task->setDescription(strip_tags(SmartestStringHelper::sanitize($input_message)));
	    $task->setType($type_code);
	    $task->save();
	    
	    /* if($send_email){
	        // code goes in here to send notification email to user
	    } */
	    
	}
	
	public function hasTodo($type, $entity_id){
	    $id = (int) $entity_id;
	    $type = SmartestStringHelper::sanitize($type);
	    $sql = "SELECT todoitem_id FROM TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_foreign_object_id='".$id."' AND todoitem_type='".$type."' AND todoitem_is_complete !=1";
	    return (bool) count($this->database->queryToArray($sql));
	    
	}
	
	public function getTodo($type, $entity_id){
	    
	    $id = (int) $entity_id;
	    $type = SmartestStringHelper::sanitize($type);
	    $sql = "SELECT * FROM TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_foreign_object_id='".$id."' AND todoitem_type='".$type."' AND todoitem_is_complete !=1";
	    $result = $this->database->queryToArray($sql);
	    
	    if(isset($result[0])){
	        $todo = new SmartestTodoItem;
	        $todo->hydrate($result[0]);
	        return $todo;
        }else{
            return false;
        }
	    
	}
	
	public function getNumTodoItems($get_assigned=false){
	    
	    if($get_assigned){
	        $sql = "SELECT todoitem_id FROM TodoItems WHERE todoitem_assigning_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1";
	    }else{
	        $sql = "SELECT todoitem_id FROM TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1";
        }
	    
	    return count($this->database->queryToArray($sql));
	    
	}
	
	public function getTodoItems($get_assigned=false){
	    
	    if($get_assigned){
	        $sql = "SELECT * FROM Users, TodoItems WHERE todoitem_assigning_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1 AND TodoItems.todoitem_receiving_user_id=Users.user_id ORDER BY todoitem_time_assigned DESC";
	    }else{
	        $sql = "SELECT * FROM Users, TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1 AND TodoItems.todoitem_assigning_user_id=Users.user_id ORDER BY todoitem_time_assigned DESC";
        }
	    
	    $result = $this->database->queryToArray($sql);
	    $tasks = array();
	    
	    if(count($result)){
	        foreach($result as $t){
	            $task = new SmartestTodoItem;
	            $task->hydrate($t);
	            $tasks[] = $task;
	        }
	    }
	    
	    return $tasks;
	    
	}
	
	public function getTodoItemsAsArrays($get_assigned=false, $get_foreign_object_info=false){
	    
	    $tasks = $this->getTodoItems($get_assigned);
	    $arrays = array();
	    
	    foreach($tasks as $t){
	        $arrays[] = $t->__toArray($get_foreign_object_info);
	    }
	    
	    return $arrays;
	    
	}
    
    public function clearCompletedTodos(){
	    
	    $sql = "DELETE FROM TodoItems WHERE todoitem_is_complete=1 AND todoitem_receiving_user_id=".$this->getId()."";
	    
	}
    
    //////////////////////// NEW USER PROFILE STUFF/////////////////////////
    
    public function getProfile($service_name='_default'){
        
        
        
    }
	
}