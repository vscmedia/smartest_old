<?php

class SmartestParameterHolder implements ArrayAccess{
    
    protected $_data = array();
    protected $_name;
    protected $_read_only = false;
    
    public function __construct($name, $read_only=false){
        $this->_name = $name;
        $this->_read_only = $read_only;
    }
    
    public function loadArray($array, $create_phobjects=true){
        foreach($array as $key=>$value){
            if(is_array($value)){
                if($create_phobjects){
                    $data = new SmartestParameterHolder('Param: '.$key, $this->_read_only);
                    $data->loadArray($value, true);
                    $this->setParameter($key, $data);
                }else{
                    $this->setParameter($key, $value);
                }
            }else{
                $this->setParameter($key, $value);
            }
        }
    }
    
    public function __toString(){
        return 'SmartestParameterHolder: '.$this->_name;
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
    
    public function toArray(){
        $a = array();
        foreach($this->_data as $k=>$v){
            if($v instanceof SmartestParameterHolder){
                $a[$k] = $v->toArray();
            }else{
                $a[$k] = $v;
            }
        }
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
    
    public function hasData(){
        return (bool) count($this->_data);
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
        
        switch($offset){
            case "_count":
            return count($this->_data);
            case "_first":
            return reset($this->_data);
            case "_last":
            return end($this->_data);
        }
        
        return $this->getParameter($offset);
    }
    
    public function offsetExists($offset){
        return $this->hasParameter($offset);
    }
    
    public function offsetSet($offset, $value){
        if(!$this->_read_only){
            return $this->setParameter($offset, $value);
        }
    }
    
    public function offsetUnset($offset){
        if(!$this->_read_only){
            return $this->clearParameter($offset);
        }
    }
    
}