<?php

class SmartestUser extends SmartestBaseUser{
	
	protected $_tokens = array();
	protected $_site_ids = array();
	
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
	
	public function offsetGet($offset){
	    
	    $offset = strtolower($offset);
	    
	    switch($offset){
	        case "password":
	        return null;
	        
	        case "full_name":
	        case "fullname":
	        return $this->getFullName();
	    }
	    
	    return parent::offsetGet($offset);
	    
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
	
}