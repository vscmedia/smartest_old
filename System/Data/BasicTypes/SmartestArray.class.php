<?php

class SmartestArray implements ArrayAccess, IteratorAggregate, Countable, SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_data = array();
    
    public function __construct(){
        
        $d = func_get_args();
        
        if(count($d) == 1 && is_array($d[0])){
            $this->_data = $d[0];
        }else{
            $this->_data = func_get_args();
        }
        
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
        return SmartestStringHelper::toCommaSeparatedList(array_slice($this->_data, 0, 10));
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return serialize($this->_value);
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue(unserialize($v));
        return true;
    }
    
    // and these two for the SmartestSubmittableValue interface
    public function hydrateFromFormData($v){
        if(is_array($v)){
            $this->_data = $v;
            return true;
        }else{
            return false;
        }
    }
    
    public function renderInput($params){
        return "SmartestArray does not have a direct input.";
    }
    
    public function getHtmlFormFormat(){
        return $this->_value;
    }
    
    public function getData(){
        return $this->_data;
    }
    
    public function getIds(){
        $ids = array();
        foreach($this->_data as $item){
            if(is_object($item) && method_exists($item, 'getId()')){
                $ids[] = $item->getId();
            }
        }
        return $ids;
    }
    
    public function add($value, $offset=null){
        if($offset){
            $this->_data[$offset] = $value;
        }else{
            $this->_data[] = $value;
        }
    }
    
    public function count(){
        return count($this->_data);
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "_ids":
            return $this->getIds();
            case "_data":
            case "_items":
            case "_objects":
            return $this->getData();
            case "_count":
            return count($this->_data);
            case "_keys":
            return array_keys($this->_data);
            case "_first":
            return reset($this->_data);
            case "_last":
            return end($this->_data);
            
        }
        
        return $this->_data[$offset];
    }
    
    public function offsetExists($offset){
        return isset($this->_data[$offset]);
    }
    
    public function offsetSet($offset, $value){
        if($offset){
            $this->_data[$offset] = $value;
        }else{
            $this->_data[] = $value;
        }
    }
    
    public function offsetUnset($offset){
        unset($this->_data[$offset]);
    }
    
    /* public function next(){
        return next($this->_data);
    }
    
    public function seek($index){
        
        $this->rewind();
        $position = 0;
        
        while($position < $index && $this->valid()) {
            $this->next();
            $position++;
        }
        
        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid seek position');
        }
        
    } */
    
    public function &getIterator(){
        return new ArrayIterator($this->_data);
    }
    
    /* public function current(){
        return current($this->_data);
    }
    
    public function key(){
        return array_search(current($this->_data), $this->_data);
    }
    
    public function rewind(){
        reset($this->_data);
    } */
    
    public function append($value){
        $this->_data[] = $value;
    }
    
    public function asort(){
        sort($this->_data);
    }
    
    public function ksort(){
        ksort($this->_data);
    }
    
    public function natcasesort(){
        natcasesort($this->_data);
    }
    
    public function natsort(){
        natsort($this->_data);
    }
    
    public function reverse(){
        return array_reverse($this->_data);
    }
    
}