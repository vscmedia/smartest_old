<?php

class SmartestNonDbRole implements ArrayAccess{
    
    protected $_tokens = array();
    protected $_label;
    protected $_id;
    
    public function hydrate($data){
        
        $this->_label = $data['label'];
        $token_data = SmartestUsersHelper::getTokenData();
        
        if(is_array($data['tokens'])){
            foreach($data['tokens'] as $k=>$t){
                $t = new SmartestUserToken_new($token_data[$k]);
                $this->_tokens[] = $t;
            }
        }
        
    }
    
    public function getId(){
        return $this->_id;
    }
    
    public function setId($id){
        $this->_id = $id;
    }
    
    public function getLabel(){
        return $this->_label;
    }
    
    public function getTokens(){
        return $this->_tokens;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "id":
            return $this->_id;
            
            case "label":
            return $this->_label;
            
            case "tokens":
            return $this->_tokens;
            
            case "type":
            return 'nondb';
            
            case "num_tokens":
            return count($this->_tokens);
            
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
    
}