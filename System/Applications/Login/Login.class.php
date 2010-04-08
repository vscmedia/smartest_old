<?php

class Login extends SmartestSystemApplication{
	
	/* function startPage(){
		$this->setTitle("Start Page");
	}
	
	function preferences(){
	
		$sql = "SELECT * FROM User, UserGroup WHERE User.user_group = UserGroup.usergroup_id";    
		$users = $this->manager->database->rawQuery($sql);
		$sql = "SELECT * FROM UserGroup";    
		$groups = $this->manager->database->rawQuery($sql);

		if(count($groups) && count($users)){
			return ( array("users" => $users, "groups" => $groups) );
		}else{
			return false;
		}    
	} */
	
	public function loginScreen($get){
		
		if($this->getUser() && $this->getUser()->isAuthenticated()){
		    $this->redirect('/smartest');
		}
		
		if(isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) && isset($get['from'])){
		    
		    // echo 'query_string';
		    
		    $vars = array();
		    
		    foreach($get as $key => $refer_var){
		        if($key != 'from'){
		            $vars[$key] = strip_tags($refer_var);
		            $vars[$key] = preg_replace('/[^\w&\._\/-]/', '', $refer_var);
	            }
		    }
		    
		    $refer_url = SmartestStringHelper::toQueryString($vars, true);
		    
		    // echo $refer_url;
		    
		    $this->send($get['from'], 'from');
		    $this->send($refer_url, 'refer');
		}else{
		    $this->send('', 'refer');
		}
		
	}
	
	public function doAuth($get, $post){
		
		if(array_key_exists('service', $post) && strlen($post['service'])){
		    $service = $post['service'];
		}else{
		    $service = 'smartest';
		}
		
		if($user = $this->_auth->newLogin($post['user'], $post['passwd'], $service)){
		    
		    SmartestSession::set('user', $user);
		    
		    if(strlen($post['from']) && $post['from']{0} == '/'){
			    $this->redirect($this->getRequest()->getDomain().substr($post['from'], 1).'?'.$post['refer']);
			}else{
			    $this->redirect("/smartest");
			}
			
		}else{
			$this->redirect("/smartest/login?reason=badauth");
		}
	}
	
	public function doLogOut(){
		$this->_auth->logout();
		$this->redirect("/smartest/login?reason=logout");
	}
	
}