<?php

class SmartestSystemUser extends SmartestUser{
    
    protected $_tokens;
    protected $_token_codes;
    protected $_num_allowed_sites = 0;
    
    public function getPermissionEditableSites(){
	    
	    // Retrieves a list of sites where the user is allowed to edit the permissins of other users
	    
	    if($this->hasGlobalPermission('modify_user_permissions')){
	        
            $sql = "SELECT * FROM Sites";
        }else{
            // modify_user_permissions token ID is ALWAYS 13
            $sql = "SELECT DISTINCT Sites.* FROM Users, UsersTokensLookup, Sites WHERE Users.user_id = '".$this->getId()."' AND (UsersTokensLookup.utlookup_token_id = '13' OR UsersTokensLookup.utlookup_token_id = '0') AND Users.user_id = UsersTokensLookup.utlookup_user_id AND Sites.site_id = UsersTokensLookup.utlookup_site_id ORDER BY UsersTokensLookup.utlookup_granted_timestamp ASC";
        }   
        
        $this->_site_ids = array();
        
	    $result = $this->database->queryToArray($sql);
	    $sites = array();
	    
	    foreach($result as $site_array){
	        
	        $site = new SmartestSite;
	        $site->hydrate($site_array);
	        $sites[] = $site;
	        
	        if($site->getId()){
	            $this->_site_ids[] = $site->getId();
            }
	    }
	    
	    return $sites;
	    
	}
	
	public function getPermissionEditableSitesAsArrays(){
	    
	    $site_objects = $this->getPermissionEditableSites();
	    $array = array();
	    
	    foreach($site_objects as $site){
	        $array[] = $site->__toArray();
	    }
	    
	    return $array;
	    
	}
	
	public function getAllowedSites($limit_ids=null){
	    
	    // Retrieves a list of sites the user is allowed to enter/see
	    
	    if($this->hasGlobalPermission('site_access')){
            $sql = "SELECT * FROM Sites";
            if(is_array($limit_ids) && count($limit_ids)){
                $sql .= " WHERE Sites.site_id IN ('".implode("','", $limit_ids)."')";
            }
        }else{
            // site_access token is ALWAYS ID 21
            $sql = "SELECT DISTINCT Sites.* FROM Users, UsersTokensLookup, Sites WHERE Users.user_id = '".$this->getId()."' AND (UsersTokensLookup.utlookup_token_id = '21' OR UsersTokensLookup.utlookup_token_id = '0') AND Users.user_id = UsersTokensLookup.utlookup_user_id AND Sites.site_id = UsersTokensLookup.utlookup_site_id ORDER BY UsersTokensLookup.utlookup_granted_timestamp ASC";
            if(is_array($limit_ids) && count($limit_ids)){
                $sql .= " AND Sites.site_id IN ('".implode("','", $limit_ids)."')";
            }
        }   
        
        $this->_site_ids = array();
        
	    $result = $this->database->queryToArray($sql);
	    $sites = array();
	    
	    foreach($result as $site_array){
	        
	        $site = new SmartestSite;
	        $site->hydrate($site_array);
	        $sites[] = $site;
	        
	        if($site->getId() && !$limit_ids){
	            $this->_site_ids[] = $site->getId();
            }
	    }
	    
	    $this->_num_allowed_sites = count($sites);
	    
	    return $sites;
	}
	
	public function getAllowedSiteIds($refresh=false){
	    
	    if(!count($this->_site_ids) || $refresh){
	        $this->getAllowedSites();
	    }
	    
	    return $this->_site_ids;
	    
	}
	
	public function getSitesWhereUserHasToken($token, $include_root=false){
	    
	    if($this->hasGlobalToken($token)){
            $sites = $this->getAllowedSites();
        }else{
            
            $h = new SmartestUsersHelper;
            
            if($token_id = $h->getTokenId($token)){
                
                if($include_root){
                    $sql = "SELECT DISTINCT Sites.* FROM Users, UsersTokensLookup, Sites WHERE Users.user_id = '".$this->getId()."' AND (UsersTokensLookup.utlookup_token_id = '".$token_id."' OR UsersTokensLookup.utlookup_token_id = '0') AND Users.user_id = UsersTokensLookup.utlookup_user_id AND Sites.site_id = UsersTokensLookup.utlookup_site_id ORDER BY Sites.site_internal_label ASC";
                }else{
                    $sql = "SELECT DISTINCT Sites.* FROM Users, UsersTokensLookup, Sites WHERE Users.user_id = '".$this->getId()."' AND UsersTokensLookup.utlookup_token_id = '".$token_id."' AND Users.user_id = UsersTokensLookup.utlookup_user_id AND Sites.site_id = UsersTokensLookup.utlookup_site_id ORDER BY Sites.site_internal_label ASC";
                }
                
                $result = $this->database->queryToArray($sql);
        	    $sites = array();

        	    foreach($result as $site_array){

        	        $site = new SmartestSite;
        	        $site->hydrate($site_array);
        	        $sites[] = $site;
    	        
        	    }
    	    
	        }else{
	            
	            // token not recognised
    	        SmartestLog::getInstance('system')->log("Tried to look for sites where user has unrecognized token: '".$token_code."'.", SM_LOG_WARNING);
    	        return false;
	            
	        }
            
        }
        
        return $sites;
	    
	}
	
	public function openSiteById($site_id){
	    
	    if(in_array($site_id, $this->getAllowedSiteIds(true))){
	    
	        $site = new SmartestSite;
	    
	        if($site->find($site_id)){
		        
		        SmartestSession::set('current_open_project', $site);
		        $this->reloadTokens();
		        
		        if(!$site->getDirectoryName()){
		        
		            SmartestSiteCreationHelper::createSiteDirectory($site);
        		
    		    }
    		    
    		    return true;
    		    
	        }else{
	            
	            return false;
	            
	        }
	        
        }else{
            
            return false;
            
        }
	    
	}
	
	public function hasToken($token, $include_root=true){
	    if($include_root){
	        return in_array($token, $this->getTokenCodes()) || in_array('root_permission', $this->getTokenCodes());
        }else{
            return in_array($token, $this->getTokenCodes());
        }
	}
	
	public function hasGlobalToken($token){
	    
	    $token_code = SmartestStringHelper::toVarName($token);
	    $h = new SmartestUsersHelper;
	    
	    $token_id = $h->getTokenId($token_code);
	    
	    $sql = "SELECT UsersTokensLookup.* FROM UsersTokensLookup WHERE utlookup_user_id='".$this->getId()."' AND UsersTokensLookup.utlookup_token_id='".$token_id."' AND utlookup_is_global='1'";
	    $result = $this->database->queryToArray($sql);
	    
	    if(count($result)){
	        return true;
	    }else{
	        return false;
	    }
	    
	}
	
	public function hasGlobalPermission($permission){
	    
	    return $this->hasGlobalToken($permission);
	    
	}
	
	// STRICT parameter excludes globally granted tokens and only returns tokens or ids granted on the side having he ID provided
	
	public function getTokenIdsOnSite($site_id, $strict=false){
	    
	    if($site_id == 'GLOBAL'){
	        
	        $sql = "SELECT UsersTokensLookup.* FROM UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id='".$this->getId()."'";
	        
	        if($strict){
	            $sql .= " AND UsersTokensLookup.utlookup_is_global='1'";
	        }
	        
	        $result = $this->database->queryToArray($sql);
	        
	    }else{
	    
	        $site_id = (int) $site_id;
	        
	        if($strict){
	            $sql = "SELECT UsersTokensLookup.* FROM UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id='".$this->getId()."' AND UsersTokensLookup.utlookup_site_id='".$site_id."'";
            }else{
                $sql = "SELECT UsersTokensLookup.* FROM UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id='".$this->getId()."' AND (UsersTokensLookup.utlookup_is_global='1' OR UsersTokensLookup.utlookup_site_id='".$site_id."')";
            }
	        
	        $result = $this->database->queryToArray($sql);
	    
        }
		
		$token_ids = array();
	
		foreach($result as $t){
		    $token_ids[] = $t['utlookup_token_id'];
		}
		
		return $token_ids;
	    
	}
	
	public function getTokensOnSite($site_id, $strict=false){
	
		$all_tokens = SmartestUsersHelper::getTokenData();
		$token_ids = $this->getTokenIdsOnSite($site_id, true);
		$tokens = array();
		
		foreach($all_tokens as $t){
		    if(in_array($t['id'], $token_ids) && strlen($t['code'])){
		        $token = new SmartestUserToken_New($t);
		        $tokens[] = $token;
		    }
		}
		
		return $tokens;
	}
	
	public function getAvailableTokens($site_id){
	    
	    if($site_id=='GLOBAL'){
	        $granted_ids = $this->getTokenIdsOnSite('GLOBAL', true);
	    }else if(is_numeric($site_id)){
	        $global_granted_ids = $this->getTokenIdsOnSite('GLOBAL', true);
	        $granted_ids = $this->getTokenIdsOnSite($site_id, true);
	    }
	    
	    $tokens = SmartestUsersHelper::getTokenData();
	    $available_tokens = array();
	    
	    if($site_id=='GLOBAL'){
	        foreach($tokens as $k=>$t){
	            if(!in_array($t['id'], $granted_ids)){
	                $available_tokens[] = new SmartestUserToken_New($tokens[$k]);
	            }
	        }
	    }else{
	        foreach($tokens as $k=>$t){
	            if(!in_array($t['id'], $granted_ids) && !in_array($t['id'], $global_granted_ids)){
	                $available_tokens[] = new SmartestUserToken_New($tokens[$k]);
	            }
	        }
	    
        }
	    
	    return $available_tokens;
	    
	}
	
	public function getTokens($reload=false){
	
		if(count($this->_tokens) && !$reload){
			return $this->_tokens;
		}else{
		    
		    $all_tokens = SmartestUsersHelper::getTokenData();
		    
		    if(SmartestSession::get('current_open_project') instanceof SmartestSite){
		        // print_r(SmartestSession::get('current_open_project'));
		        $site_id = SmartestSession::get('current_open_project')->getId();
		        $sql = "SELECT UsersTokensLookup.* FROM UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id='".$this->getId()."' AND (UsersTokensLookup.utlookup_is_global='1' OR UsersTokensLookup.utlookup_site_id='".$site_id."')";
		    }else{                                                                                                                                      
			    $sql = "SELECT UsersTokensLookup.* FROM UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id='".$this->getId()."' AND UsersTokensLookup.utlookup_is_global='1'";
			}
			
			$result = $this->database->queryToArray($sql);
			
			$token_ids = array();
		
			foreach($result as $t){
			    $token_ids[] = $t['utlookup_token_id'];
			}
			
			$tokens = array();
			
			foreach($all_tokens as $t){
			    if(in_array($t['id'], $token_ids) && strlen($t['code'])){
			        $token = new SmartestUserToken_New($t);
			        $tokens[] = $token;
			    }
			}
			
			$this->_tokens = $tokens;
			
			return $this->_tokens;
			
		}
	}
	
	public function getTokenCodes($reload=false){
	    
	    if(empty($this->_tokens) || empty($this->_token_codes) || $reload){
	    
	        $tokens = $this->getTokens($reload);
	    
    	    $codes = array();
	    
    	    foreach($tokens as $t){
    	        if(is_object($t)){
    	            $codes[] = $t->getCode();
	            }
    	    }
	    
    	    $this->_token_codes = $codes;
	    
        }
	    
	    return $this->_token_codes;
	    
	}
	
	public function reloadTokens(){
	    // This reloads the permission tokens via $this->getTokens(true);
	    $this->getTokenCodes(true);
	}
	
	public function getUnusedTokens(){
	    
	    // get all tokens
	    
	}
	
	public function addToken($token_code, $site_id){
	    
	    // $token = new SmartestUserToken;
	    $h = new SmartestUsersHelper;
	    
	    if($token_id = $h->getTokenId($token_code)){
	        $this->addTokenById($token_id, $site_id);
		    
		}else{
		    SmartestLog::getInstance('system')->log("Tried to hydrate a non-existent user token: '".$token_code."'.", SM_LOG_WARNING);
		}
		
		$this->reloadTokens();
	}
	
	public function addTokenById($token_id, $site_id, $avoid_duplicates=false){
	    
	    $utl = new SmartestUserTokenLookup;
		$utl->setUserId($this->getId());
	    $utl->setTokenId($token_id);
	    $utl->setGrantedTimestamp(time());
	    
	    if($site_id == "GLOBAL"){
	        $utl->setIsGlobal(1);
	        
	        // Remove any non-global assignments of the same token
	        if($avoid_duplicates){
	            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_token_id='".$token_id."' AND utlookup_user_id='".$this->getId()."'";
            }else{
                $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_token_id='".$token_id."' AND utlookup_is_global != '1' AND utlookup_user_id='".$this->getId()."'";
            }
            
	        $this->database->rawQuery($sql);
	        
	    }else if(is_numeric($site_id)){
	        
	        if($avoid_duplicates){
	            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_token_id='".$token_id."' AND utlookup_is_global != '1' AND utlookup_site_id='".$site_id."' AND utlookup_user_id='".$this->getId()."'";
	            $this->database->rawQuery($sql);
            }
            
	        $utl->setSiteId($site_id);
	    }
	    
	    $utl->save();
	    
	}
	
	public function removeTokenById($token_id, $site_id){
	    
	    $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_token_id='".$token_id."' AND utlookup_user_id='".$this->getId()."'";
	    
	    if($site_id == "GLOBAL"){
	        $sql .= " AND utlookup_is_global = '1'";
	    }else{
	        $sql .= " AND utlookup_is_global != '1' AND utlookup_site_id='".$site_id."'";
	    }
	    
	    $this->database->rawQuery($sql);
	    
	}
	
	// Held Pages
    
    public function getHeldPages($site_id=''){
	    
	    $sql = "SELECT * FROM Pages WHERE page_is_held=1 AND page_held_by='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND page_site_id = '".$site_id."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
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
	    
	    $sql = "UPDATE TodoItems SET todoitem_is_complete='1', todoitem_time_completed='".time()."' WHERE todoitem_type='SM_TODOITEMTYPE_RELEASE_PAGE' AND todoitem_receiving_user_id='".$this->_properties['id']."'";
	    
	    $this->database->rawQuery($sql);
	    
	}
	
	public function getHeldItems($model_id='', $site_id=''){
	    
	    $sql = "SELECT * FROM Items WHERE item_is_held=1 AND item_held_by='".$this->getId()."'";
	    
	    if(is_numeric($model_id)){
	        $sql .= " AND item_itemclass_id='".$model_id."'";
	    }
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND item_site_id = '".$site_id."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    $items = array();
	    
	    foreach($result as $array){
	        $item = new SmartestItem;
	        $item->hydrate($array);
	        $items[] = $item;
	    }
	    
	    return $items;
	    
	}
	
	public function getNumHeldItems($model_id='', $site_id=''){
	    
	    $sql = "SELECT * FROM Items WHERE item_is_held=1 AND item_held_by='".$this->getId()."'";
	    
	    if(is_numeric($model_id)){
	        $sql .= " AND item_itemclass_id='".$model_id."'";
	    }
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND item_site_id = '".$site_id."'";
	    }
	    
	    return $this->database->howMany($sql);
	}
	
	public function releaseItems($model_id, $site_id=''){
	    
	    $sql = "UPDATE Items SET item_is_held=0, item_held_by=0 WHERE item_itemclass_id='".$model_id."' AND item_is_held=1 AND item_held_by='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND item_site_id = '".$site_id."'";
	    }
	    
	    $this->database->rawQuery($sql);
	    
	    $sql = "UPDATE TodoItems SET todoitem_is_complete='1', todoitem_time_completed='".time()."' WHERE todoitem_type='SM_TODOITEMTYPE_RELEASE_ITEM' AND todoitem_receiving_user_id='".$this->_properties['id']."'";
	    
	    $this->database->rawQuery($sql);
	}
	
	// Todo list
	
	public function assignTodo($type_code, $entity_id, $assigner_id, $message='', $send_email=false){
	    
	    $type = SmartestTodoListHelper::getType($type_code);
	    
	    if(isset($message{1})){
	        $input_message = SmartestStringHelper::sanitize($message);
	    }else{
	        $input_message = $type->getDescription();
	    }
	    
	    $task = new SmartestTodoItem;
	    $task->setReceivingUserId($this->_properties['id']);
	    $task->setAssigningUserId($assigner_id);
	    $task->setForeignObjectId($entity_id);
	    $task->setTimeAssigned(time());
	    $task->setDescription($input_message);
	    $task->setType($type_code);
	    $task->save();
	    
	    if($send_email){
	        // code goes in here to send notification email to user
	    }
	    
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
	
	// Recently edited
    
    public function addRecentlyEditedItemById($item_id, $site_id, $force=false){
	    
	    if(!$force){
	        
	        $ri = $this->getRecentlyEditedItems($site_id);
	        $recent_item_ids = array();
	        
	        foreach($ri as $item){
	            $recent_item_ids[] = $item->getId();
	        }
	    }
	    
	    if($force || !in_array($item_id, $recent_item_ids)){
	        $l = new SmartestManyToManyLookup;
	        $l->setType('SM_MTMLOOKUP_RECENTLY_EDITED_ITEMS');
	        $l->setEntityForeignKeyValue(1, (int) $item_id);
	        $l->setEntityForeignKeyValue(2, $this->getId());
	        $l->setEntityForeignKeyValue(3, (int) $site_id);
	        $l->save();
        }
	    
	}
	
	public function getRecentlyEditedItems($site_id, $class_id=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_ITEMS');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        $q->addForeignTableConstraint('Items.item_deleted', 0);
        
        if(is_numeric($class_id)){
            $q->addForeignTableConstraint('Items.item_itemclass_id', $class_id);
        }
        
        $q->addSortField('ManyToManyLookups.mtmlookup_id');
        $q->setSortDirection('DESC');
        $q->setLimit(5);
        
        $items = $q->retrieve();
        
        return $items;
	    
	}
	
	public function clearRecentlyEditedItems($site_id){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_ITEMS');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        $items = $q->delete();
	    
	}
	
	public function addRecentlyEditedAssetById($asset_id, $site_id, $force=false){
	    
	    if(!$force){
	        
	        $ra = $this->getRecentlyEditedAssets($site_id);
	        $recent_asset_ids = array();
	        
	        foreach($ra as $asset){
	            $recent_asset_ids[] = $asset->getId();
	        }
	    }
	    
	    if($force || !in_array($asset_id, $recent_asset_ids)){
	        $l = new SmartestManyToManyLookup;
	        $l->setType('SM_MTMLOOKUP_RECENTLY_EDITED_ASSETS');
	        $l->setEntityForeignKeyValue(1, (int) $asset_id);
	        $l->setEntityForeignKeyValue(2, $this->getId());
	        $l->setEntityForeignKeyValue(3, (int) $site_id);
	        $l->save();
        }
	    
	}
	
	public function getRecentlyEditedAssets($site_id, $type=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_ASSETS');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        $q->addForeignTableConstraint('Assets.asset_deleted', 0);
        
        if(strlen($type)){
            $q->addForeignTableConstraint('Assets.asset_type', $type);
        }
        
        $q->addSortField('ManyToManyLookups.mtmlookup_id');
        $q->setSortDirection('DESC');
        $q->setLimit(5);
        
        $assets = $q->retrieve();
        
        return $assets;
	    
	}
	
	public function clearRecentlyEditedAssets($site_id, $type=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_ASSETS');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        if(strlen($type)){
            $q->addForeignTableConstraint('Assets.asset_type', $type);
        }
        
        $q->delete();
	    
	}
	
	public function addRecentlyEditedPageById($page_id, $site_id, $force=false){
	    
	    if(!$force){
	        
	        $rp = $this->getRecentlyEditedPages($site_id);
	        $recent_page_ids = array();
	        
	        foreach($rp as $page){
	            $recent_page_ids[] = $page->getId();
	        }
	    }
	    
	    if($force || !in_array($page_id, $recent_page_ids)){
	        $l = new SmartestManyToManyLookup;
	        $l->setType('SM_MTMLOOKUP_RECENTLY_EDITED_PAGES');
	        $l->setEntityForeignKeyValue(1, (int) $page_id);
	        $l->setEntityForeignKeyValue(2, $this->getId());
	        $l->setEntityForeignKeyValue(3, (int) $site_id);
	        $l->save();
        }
	    
	}
	
	public function getRecentlyEditedPages($site_id, $type=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_PAGES');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        $q->addForeignTableConstraint('Pages.page_deleted', 'FALSE');
        $q->addSortField('ManyToManyLookups.mtmlookup_id');
        $q->setSortDirection('DESC');
        $q->setLimit(5);
        
        $pages = $q->retrieve();
        
        return $pages;
	    
	}
	
	public function clearRecentlyEditedPages($site_id, $type=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_PAGES');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        $q->delete();
	    
	}
	
	public function addRecentlyEditedTemplateById($asset_id, $site_id, $force=false){
	    
	    if(!$force){
	        
	        $ra = $this->getRecentlyEditedTemplates($site_id);
	        $recent_asset_ids = array();
	        
	        foreach($ra as $asset){
	            $recent_asset_ids[] = $asset->getId();
	        }
	    }
	    
	    if($force || !in_array($asset_id, $recent_asset_ids)){
	        $l = new SmartestManyToManyLookup;
	        $l->setType('SM_MTMLOOKUP_RECENTLY_EDITED_TEMPLATES');
	        $l->setEntityForeignKeyValue(1, (int) $asset_id);
	        $l->setEntityForeignKeyValue(2, $this->getId());
	        $l->setEntityForeignKeyValue(3, (int) $site_id);
	        $l->save();
        }
	    
	}
	
	public function getRecentlyEditedTemplates($site_id, $type=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_TEMPLATES');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        $q->addForeignTableConstraint('Assets.asset_deleted', 0);
        
        if(strlen($type)){
            $q->addForeignTableConstraint('Assets.asset_type', $type);
        }else{
            $h = new SmartestTemplatesLibraryHelper;
            $q->addForeignTableConstraint('Assets.asset_type', $h->getTypeCodes(), SmartestQuery::IN);
        }
        
        $q->addSortField('ManyToManyLookups.mtmlookup_id');
        $q->setSortDirection('DESC');
        $q->setLimit(5);
        
        $assets = $q->retrieve();
        
        return $assets;
	    
	}
	
	public function clearRecentlyEditedTemplates($site_id, $type=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_TEMPLATES');
	    
	    $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        if(strlen($type)){
            $q->addForeignTableConstraint('Assets.asset_type', $type);
        }else{
            $h = new SmartestTemplatesLibraryHelper;
            $q->addForeignTableConstraint('Assets.asset_type', $h->getTypeCodes(), SmartestQuery::IN);
        }
        
        $q->delete();
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "num_allowed_sites":
	        return $this->_num_allowed_sites;
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}

}