<?php

class SmartestArray extends ArrayObject{
    
    protected $_data = array();
    
    /* public function __construct($array, $flags=0, $iterator_class="ArrayIterator"){
        
    } */
    
    /* public function __toArray(){
        return $_data;
    } */
    
    public function reverse(){
        return array_flip($this);
    }
    
}