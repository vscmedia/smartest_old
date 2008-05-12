<?php

class SmartestManyToManyQueryForeignTableConstraint{
    
    protected $_field;
    protected $_value;
    protected $_operator;
    
    public function __construct($field, $value, $operator){
        $this->_field = $field;
        $this->_value = $value;
        $this->_operator = $operator;
    }
    
    public function getField(){
        return $this->_field;
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function getEscapedValue(){
        return mysql_real_escape_string($this->_value);
    }
    
    public function getOperator(){
        return $this->_operator;
    }
    
}
