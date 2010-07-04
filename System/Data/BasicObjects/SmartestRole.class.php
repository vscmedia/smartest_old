<?php

class SmartestRole extends SmartestBaseRole{
	
	protected $_tokens = array();
	protected $_token_ids = array();
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'role_';
		$this->_table_name = 'Roles';
		
	}
	
	public function getTokenIds(){
	    
	    if(!count($this->_token_ids)){
	    
	        $sql = "SELECT rtlookup_token_id FROM RolesTokensLookup WHERE rtlookup_role_id='".$this->getId()."'";
            $result = $this->database->queryFieldsToArrays(array('rtlookup_token_id'), $sql);
            $this->_token_ids = array_values($result['rtlookup_token_id']);
        
        }
        
        return $this->_token_ids;
	    
	}
	
	public function getTokens(){
	    
	    if(!count($this->_tokens)){
	        
	        $tokens = array();
	        $ids = $this->getTokenIds();
	        $all_tokens = SmartestPffrHelper::getContentsFast(SM_ROOT_DIR.'System/Core/Types/usertokens.pff');
	        
	        foreach($ids as $id){
	            $t = new SmartestUserToken_New($all_tokens[$id]);
	            $tokens[] = $t;
	        }
	        
	        $this->_tokens = $tokens;
	        
	    }
	    
	    return $this->_tokens;
	}
	
	public function getUnusedTokens(){
	    
	    $role_token_ids = $this->getTokenIds();
	    $h = new SmartestUsersHelper;
	    $tokens = $h->getTokens();
	    
	    // go through the list of all tokens
	    foreach($tokens as $key=>$value){

    		// if the item on the list is already attached to the role, unattach it
		    if(in_array($value['token_id'], $role_token_ids)){
		        unset($tokens[$key]);
		    }
		    
	    }
	    
	    $tokens = array_values($tokens);

	    return $tokens;
	    
	}
	
	public function addTokensById($ids){
	    
	    if(!is_array($ids)){
	        $ids = array($ids);
	    }
	    
	    // print_r($ids);
	    
	    foreach($ids as $id){
	    
	        if(!in_array($id, $this->getTokenIds())){
	            $l = new SmartestRoleTokenLookup;
	            $l->setTokenId($id);
	            $l->setRoleId($this->getId());
	            $l->save();
	            $this->_token_ids[] = $id;
	        }
	    
        }
	    
	}
	
	public function removeTokenId($ids){
	    
	    if($k = array_search($id, $this->_token_ids)){
	        unset($this->_token_ids[$k]);
	    }
	    
	}
	
	public function removeTokensById($ids){
	    
	    if(!is_array($ids)){
	        $ids = array($ids);
	    }
	    
	    foreach($ids as $id){
	    
	        $id = (int) $id;
	        $sql = "DELETE FROM RolesTokensLookup WHERE rtlookup_role_id='".$this->getId()."' AND rtlookup_token_id='".$id."'";
	        $this->database->rawQuery($sql);
	        $this->removeTokenId($id);
	    
        }
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "type":
	        return 'db';
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
}