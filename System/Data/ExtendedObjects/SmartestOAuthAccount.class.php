<?php

class SmartestOAuthAccount extends SmartestUser{
    
    protected $_service = null;
    
    public function hasAccessToken(){
        return (bool) $this->getOAuthAccessToken();
    }
    
    public function hasAccessTokenAndSecret(){
        return (bool) $this->getOAuthAccessToken() && (bool) $this->getOAuthAccessTokenSecret();
    }
    
    public function hasConsumerToken(){
        return (bool) $this->getOAuthConsumerToken();
    }
    
    public function hasConsumerTokenAndSecret(){
        return (bool) $this->getOAuthConsumerToken() && (bool) $this->getOAuthConsumerSecret();
    }
    
    // OAuth 2.0 uses slightly different terminology
    public function hasClientId(){
        return $this->hasConsumerToken();
    }
    
    public function hasClientIdAndSecret(){
        return $this->hasConsumerToken();
    }
    
    public function getClientId(){
        return $this->getOAuthConsumerToken();
    }
    
    public function setClientId($client_id){
        return $this->setOAuthConsumerToken($client_id);
    }
    
    /* public function getClientId(){
        return $this->getInfoValue('oauth_client_id');
    }
    
    public function setClientId($client_id){
        return $this->setInfoValue('oauth_client_id', $client_id);
    } */
    
    public function getService(){
        if(!$this->_service){
            $services = SmartestOAuthHelper::getServices();
            if(isset($services[$this->getOAuthServiceId()])){
                $this->_service = $services[$this->getOAuthServiceId()];
            }
        }
        return $this->_service;
    }
    
    public function getLabel(){
        return $this->getFirstName();
    }
    
    public function setLabel($label){
        $this->setFirstName($label);
    }
    
    public function setUsername($username){
		if(strlen($username) > 3 && strlen($username) < 41){
		    // $username = SmartestStringHelper::toUsername($username);
			$this->_properties['username'] = $username;
			$this->_modified_properties['username'] = $username;
		}
	}
	
	public function setPassword($password){
	    $this->_properties['password'] = $password;
		$this->_modified_properties['password'] = $password;
	}
    
    public function __toString(){
        
        $string = $this->getLabel();
        $string .= $this->hasAccessToken() ? '' : ' (Not authorised)';

        return $string;
        
    }
    
    public function isOAuthClient(){
        return $this->getType() == 'SM_USERTYPE_OAUTH_CLIENT_INTERNAL';
    }
    
    public function save(){
        if($this->getType() != 'SM_USERTYPE_OAUTH_CLIENT_INTERNAL'){
            $this->setType('SM_USERTYPE_OAUTH_CLIENT_INTERNAL');
            $this->setIsSmartestAccount(0);
        }
        parent::save();
    }
    
    public function otherAccountsForSameServiceExistWithClientId(){
        
        $sql = "SELECT * FROM Users WHERE user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' AND user_oauth_service_id='".$this->getOAuthServiceId()."' AND user_id != '".$this->getId()."' AND user_oauth_consumer_token != '' AND user_oauth_consumer_secret != ''";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            
            $alternatives = array();
            
            foreach($result as $r){
                $a = new SmartestOAuthAccount;
                $a->hydrate($r);
                $alternatives[] = $a;
            }
            
            return $alternatives;
            
        }else{
            return array();
        }
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "label":
            return $this->getLabel();
            
            case "display_name":
            return $this->__toString();
            
            case "service":
            return $this->getService();
            
            case "client_id":
            case "oauth_client_id":
            return $this->getClientId();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}