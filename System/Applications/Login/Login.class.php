<?php

class Login extends SmartestSystemApplication{
	
	protected $_new_user;
	
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
		    // $this->redirect('/smartest');
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
		    
		    /* $refer_url = SmartestStringHelper::toQueryString($vars, true);
		    
		    $this->send($get['from'], 'from');
		    $this->send($refer_url, 'refer'); */
		    
		}else{
		    $this->send('', 'refer');
		}
		
	}
	
	public function doAuth($get, $post){
		
		if(array_key_exists('service', $post) && strlen($post['service'])){
		    $service = $this->getRequestParameter('service');
		}else{
		    $service = 'SMARTEST';
		}
		
		if($this->getUser() && $this->getUser()->isAuthenticated()){
		    $this->redirect('/smartest');
		}
		
		if($user = $this->_auth->newLogin($this->getRequestParameter('user'), $this->getRequestParameter('passwd'), $service)){
		    
		    SmartestSession::set('user', $user);
		    
		    if($this->getUser()->getId()){
			    
			    $last_site_id = $this->getCookie('SMARTEST_LPID');
			    $allowed_site_ids = $this->getUser()->getAllowedSiteIds();
			    
			    SmartestLog::getInstance('site')->log("{$this->getUser()->__toString()} logged in.", SmartestLog::USER_ACTION);
			    
    	        if(is_numeric($last_site_id)){
    	            
    	            if(in_array($last_site_id, $allowed_site_ids)){
    	                
    	                if(strlen($this->getCookie('SMARTEST_RET'))){
    	                    
    	                    $url = '/'.$this->getCookie('SMARTEST_RET');
                            $this->clearCookie('SMARTEST_RET');
    	                    
    	                    // user still has access to last edited site, so return to what they were last doing
    	                    $site = new SmartestSite;

            		        if($site->find($last_site_id)){

            			        SmartestSession::set('current_open_project', $site);
            			        $this->getUser()->reloadTokens();
                                $this->redirect($url);
        		        
            		        }else{
            		            
            		            // They have access to a site ID which doesn't exist
            		            
            		        }
        		        
    		            }else{
    		                
    		                $this->redirect("/smartest");
    		                
    		            }
        		        
    	            }else{
    	                // user no longer has access to that site
    	                $this->addUserMessageToNextRequest("Smartest could not return you to what you were last working on because you no longer have permission to work on that site.", SmartestUserMessage::ACCESS_DENIED);
    	                $this->redirect("/smartest");
    	            }
    	            
    	        }else{
    	            
    	            $this->redirect("/smartest");
    	            
    	        }
    	        
    	    }else{
    	        // User is not hydrated
    	        $this->redirect("/smartest");
    	    }
			
		}else{
			$this->redirect("/smartest/login#badauth");
		}
	}
	
	public function doLogOut(){
	    $this->clearCookie('SMARTEST_RET');
	    $this->clearCookie('SMARTEST_LPID');
		$this->_auth->logout();
		$this->redirect("/smartest/login#logout");
	}
	
}