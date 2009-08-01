<?php

class SmartestSystemUser extends SmartestUser{
    
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
	
	public function getAllowedSiteIds($refresh=false){
	    
	    if(!count($this->_site_ids) || $refresh){
	        $this->getAllowedSites();
	    }
	    
	    return $this->_site_ids;
	    
	}
	
	public function hasToken($token){
	    return in_array($token, $this->_tokens);
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
	
	public function addToken($token_code, $site_id){
	    
	    $token = new SmartestUserToken;
	    
	    if($token->hydrateBy('code', $token_code)){
	        
	        $utl = new SmartestUserTokenLookup;
		    $utl->setUserId($this->getId());
		    $utl->setTokenId($token->getId());
		    $utl->setGrantedTimestamp(time());
		    
		    if($site_id == "GLOBAL"){
		        $utl->setIsGlobal(1);
		    }else{
		        $utl->setSiteId($site_id);
		    }
		    
		    $utl->save();
		    
		}else{
		    SmartestLog::getInstance('system')->log("Tried to hydrate a non-existent user token.", SM_LOG_WARNING);
		}
		
		$this->reloadTokens();
	}
	
	// Held Pages
    
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
	
	public function getNumHeldItems($model_id, $site_id=''){
	    
	    $sql = "SELECT * FROM Items WHERE item_itemclass_id='".$model_id."' AND item_is_held=1 AND item_held_by='".$this->getId()."'";
	    
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
        $q->setLimit(10);
        
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
        $q->setLimit(10);
        
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
        $q->setLimit(10);
        
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

}