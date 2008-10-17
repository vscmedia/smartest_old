<?php

class SmartestRole extends SmartestBaseRole{
	
	protected $_tokens = array();
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'role_';
		$this->_table_name = 'Roles';
		
	}
	
	public function getTokens(){
	    if(!count($this->_tokens)){
	        
	        $sql = "SELECT * FROM RolesTokensLookup WHERE rtlookup_role_id='".$this->getId()."'";
	        $result = $this->database->queryToArray($sql);
	        
	        $tokens = array();
	        
	        foreach($result as $rtlookup){
	            $token = new SmartestUserToken;
	            if($token->hydrate($rtlookup['rtlookup_token_id'])){
	                $tokens[] = $token;
	            }
	        }
	        
	        $this->_tokens = $tokens;
	        
	    }
	    
	    return $this->_tokens;
	}
	
}