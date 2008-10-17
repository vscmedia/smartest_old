<?php

class SmartestParameterHolder implements ArrayAccess{
    
    protected $_data = array();
    protected $_name;
    
    public function __construct($name){
        $this->_name = $name;
    }
    
    public function __toString(){
        return $this->_name;
    }
    
    public function getParameter($n){
        return isset($this->_data[$n]) ? $this->_data[$n] : null;
    }
    
    public function g($n){
        return $this->getParameter($n);
    }
    
    public function getParameters(){
        return $this->_data;
    }
    
    public function hasParameter($n){
        return isset($this->_data[$n]);
    }
    
    public function setParameter($n, $v){
        $this->_data[$n] = $v;
        return true;
    }
    
    public function clearParameter($n){
        if(isset($this->_data[$n])){
            unset($this->_data[$n]);
            return true;
        }else{
            return false;
        }
    }
    
    public function getSimpleObject(){
        $o = new stdClass;
        foreach($this->_data as $n => $v){
            $o->$n = $v;
        }
        return $o;
    }
    
    public function toJson(){
        return json_encode($this->getSimpleObject());
    }
    
    public function offsetGet($offset){
        return $this->getParameter($offset);
    }
    
    public function offsetExists($offset){
        return $this->hasParameter($offset);
    }
    
    public function offsetSet($offset, $value){
        return $this->setParameter($offset, $value);
    }
    
    public function offsetUnset($offset){
        return $this->clearParameter($offset);
    }
    
}