<?php

class SmartestParameterHolder implements ArrayAccess, IteratorAggregate, Countable, SmartestBasicType{
    
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
    
    public function absorb(SmartestParameterHolder $d){
        $this->loadArray($d->getParameters());
    }
    
    public function setValue($value){
        if(is_array($value)){
            $this->_data = $value;
        }else{
            throw new SmartestException("SmartestArray::setValue() expects an array; ".gettype($value)." given.");
        }
    }
    
    public function getValue(){
        return $this->getData();
    }
    
    public function __toString(){
        return 'SmartestParameterHolder: '.$this->_name;
    }
    
    public function getParameter($n, $default=null){
        return isset($this->_data[$n]) ? $this->_data[$n] : (isset($default) ? $default : null);
    }
    
    public function g($n, $d=null){
        return $this->getParameter($n, $d);
    }
    
    public function getParameters(){
        return $this->_data;
    }
    
    public function getParameterNames(){
        return array_keys($this->_data);
    }
    
    public function d(){ // D for Data
        return $this->getParameters();
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
        return $a;
    }
    
    public function a(){
        return $this->toArray();
    }
    
    public function hasParameter($n){
        return $this->h($n);
    }
    
    public function h($n){
        return isset($this->_data[$n]);
    }
    
    public function setParameter($n, $v){
        // var_dump($n);
        $this->_data[$n] = $v;
        return true;
    }
    
    public function s($n, $v){
        return $this->setParameter($n, $v);
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
            if($v instanceof SmartestParameterHolder){
                $o->$n = $v->getSimpleObject();
            }else{
                $o->$n = stripslashes($v);
            }
        }
        return $o;
    }
    
    public function o(){
        return $this->getSimpleObject();
    }
    
    public function toJson(){
        return json_encode($this->getSimpleObject());
    }
    
    public function j(){
        return $this->toJson();
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "_count":
            return count($this->_data);
            case "_first":
            return reset($this->_data);
            case "_last":
            return end($this->_data);
            case "_json":
            return $this->toJson();
            case "_keys":
            return array_keys($this->_data);
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
    
    public function &getIterator(){
        return new ArrayIterator($this->_data);
    }
    
    public function count(){
        return count($this->_data);
    }
    
    public function hasValue($v){
        return in_array($v, $this->_data);
    }
    
}