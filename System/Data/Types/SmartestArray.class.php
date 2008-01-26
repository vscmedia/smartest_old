<?php

class SmartestArray extends ArrayObject{
    
    protected $_data = array();
    
    public function __construct(){
        
    }
    
    public function __toArray(){
        return $_data;
    }
    
}