<?php

class SmartestTodoItem extends SmartestDataObject{
    
    protected $_type_object;
    protected $_target_object;
    protected $_assigning_user;
    protected $_receiving_user;
    
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'todoitem_';
		$this->_table_name = 'TodoItems';
		
	}
	
	public function getTypeInfo(){
	    
	    /* if(!$this->_type_info){
	    
	        $types = SmartestTodoListHelper::getTypes();
	        $type = $types[$this->_properties['type']];
	        $this->_type_info = $type;
	    
        }
        
        return $this->_type_info; */
        
        return $this->getType()->__toArray();
	    
	}
	
	public function hydrate($data){
	    
	    if(is_array($data)){
	        if(isset($data['user_id'])){
	            // user information has been passed too
	            
	            $u = new SmartestUser;
                $u->hydrate($data, false);
	            
	            // the user passed was the one who assigned this todo
	            if($data['user_id'] == $data['todoitem_assigning_user_id']){
	                
	                $this->_assigning_user = $u;
	                
	            }
	            
	            if($data['user_id'] == $data['todoitem_receiving_user_id']){
	                
	                $this->_receiving_user = $u;
	                
	            }
	        }
	    }
	    
	    return parent::hydrate($data);
	    
	}
	
	public function getType(){
	    
	    if(!$this->_type_object){
	        $this->_type_object = SmartestTodoListHelper::getType($this->getTypeCode());
        }
        
        return $this->_type_object;
	}
	
	public function getTypeCode(){
	    return $this->_properties['type'];
	}
	
	public function __toArray($include_object_info=false){
	    
	    $data = parent::__toArray();
	    $data['type'] = $this->getType()->__toArray();
	    $data['assigning_user'] = $this->getAssigningUser()->__toArray();
	    
	    if($this->getType()->getId() == 'SM_TODOITEMTYPE_GENERIC'){
	        
	        // there's no action object
	        
	    }else{
	    
	        if($this->getTargetObject()){
	            $data['action_url'] = $this->getActionUrl();
	            $data['object'] = $this->getTargetObject()->__toArray();
	            $data['object_label'] = $this->getTargetObject()->__toString();
	            return $data;
	        }else{
	            // action object not found - write to log and delete this to-do
	            $this->delete();
	            return array();
	        }
	    
        }
	    
	}
	
	public function getActionUrl(){
	    
	    $url = $this->getType()->getAction();
	    
	    if($object = $this->getTargetObject()){
	        
	        preg_match_all('/\$'.$object->getTablePrefix().'([\w_]+)/', $url, $matches);
	        
	        foreach($matches[0] as $key=>$variable){
    	        $url = str_replace($variable, $object->getFieldByName($matches[1][$key]), $url);
    	    }
    	    
    	    return $url;
    	    
        }else{
            // action object not found - write to log
        }
	    
	}
	
	public function ignore(){
	    $this->_properties['ignore'] = 1;
	    $this->_modified_properties['ignore'] = 1;
	    $this->save();
	}
	
	public function complete($send_email=false){
	    
	    $this->setIsComplete(1);
        $this->setTimeCompleted(time());
        $this->save();
        
        if($send_email && ((int) $this->_properties['assigning_user_id']) > 0){
            $this->getAssigningUser()->sendEmail('Task Completed', 'Hi '.$this->getAssigningUser()->getFirstname().",\n\nThe task you assigned to ".$this->getUser()->getFirstname()." has now been completed.");
        }
        
	}
	
	public function isSelfAssigned(){
	    return ($this->_properties['assigning_user_id'] == $this->_properties['receiving_user_id']);
	}
	
	public function getAssigningUser(){
	    
	    if(!$this->_assigning_user){
	        $u = new SmartestUser;
	        if($u->hydrate($this->_properties['assigning_user_id'])){
	            $this->_assigning_user = $u;
	        }
	    }
	    
	    return $this->_assigning_user;
	}
	
	public function getUser(){
	    
	    if(!$this->_receiving_user){
	        $u = new SmartestUser;
	        if($u->hydrate($this->_properties['receiving_user_id'])){
	            $this->_receiving_user = $u;
	        }
	    }
	    
	    return $this->_receiving_user;
	    
	}
	
	public function getTargetObject(){
	    
	    if(!$this->_target_object){
	        
	        $class = $this->getType()->getClass();
	        
	        if(class_exists($class)){
	        
    	        $object = new $class;
	        
    	        if($object->hydrate($this->_properties['foreign_object_id'])){
    	            return $object;
    	        }else{
    	            $this->ignore();
    	            return false;
    	        }
	        
    	    }else{
    	        throw new SmartestException('Class "'.$this->getType()->getClass().'" specified in todoitemtypes for type "'.$this->getType()->getId().'" was not found');
    	    }
	    
        }
        
        return $this->_target_object;
	    
	}
	
	public function getCategoryInfo(){
	    
	}
	
}