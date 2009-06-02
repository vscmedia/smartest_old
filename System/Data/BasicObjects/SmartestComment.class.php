<?php

class SmartestComment extends SmartestBaseComment{
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "user":
            return $this->getUser();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function getUser(){
        
        if($this->_user){
            return $this->_user;
        }else if($this->getAuthorId()){
            $user = new SmartestUser;
            if($user->find($this->getAuthodId())){
                return $user;
            }
        }
        
    }
    
}