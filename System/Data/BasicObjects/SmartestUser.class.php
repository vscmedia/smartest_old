<?php

class SmartestUser extends SmartestBaseUser{
	
	protected $_tokens = array();
	protected $_site_ids = array();
	protected $_model_plural_names = array();
	protected $_parameters; // Only useful when the user is being stored in the session
	
	protected function __objectConstruct(){
		$this->_table_prefix = 'user_';
		$this->_table_name = 'Users';
		$this->_no_prefix = array('username'=>1, 'password'=>1);
		
		if(method_exists($this, '__myConstructor')){
		    $args = func_get_args();
		    $this->__myConstructor($args);
		}
		
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
				return true;
				
			}else{
				return false;
			}
		}
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
		}else{
			if(strlen($password) > 3){
				$this->_properties['password'] = md5($password);
				$this->_modified_properties['password'] = md5($password);
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
	    
	    if(in_array($offset, array_keys($this->getModelPluralNames()))){
            return $this->getCreditedItemsOnCurrentSite($this->_model_plural_names[$offset]);
        }
	    
	    switch($offset){
	        case "password":
	        return null;
	        
	        case "full_name":
	        case "fullname":
	        return $this->getFullName();
	        
	        case "profile_pic":
	        return $this->getProfilePic();
	        
	        case "profile_pic_asset_id":
	        return $this->getProfilePicAssetId();
	        
	        case "bio":
	        return $this->getBio();
	        
	        case "website":
	        case "website_url":
	        $url = $this->_properties['website'];
	        // TODO: check this value
	        return new SmartestExternalUrl($url);
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
	public function getCreditedItemsOnCurrentSite($model_id=null, $mode=9){
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
	
}