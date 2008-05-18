<?php

class SmartestTodoItem extends SmartestDataObject{
    
    protected $_type_object;
    protected $_target_object;
    
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
	    $data['action_url'] = $this->getActionUrl();
	    
	    // if($include_object_info && $this->getTargetObject()){
	    $data['object'] = $this->getTargetObject()->__toArray();
	    $data['object_label'] = $this->getTargetObject()->__toString();
	    // }
	    
	    return $data;
	    
	}
	
	public function getActionUrl(){
	    $url = $this->getType()->getAction().'?'.
	    $this->getType()->getUriField().'='.
	    $this->getTargetObject()->getFieldByName($this->getType()->getUriField());
	    return $url;
	}
	
	public function ignore(){
	    $this->_properties['ignore'] = 1;
	    $this->_modified_properties['ignore'] = 1;
	    $this->save();
	}
	
	public function complete(){
	    $this->setIsComplete(1);
	    $this->setTimeAssigned(time());
	    $this->save();
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