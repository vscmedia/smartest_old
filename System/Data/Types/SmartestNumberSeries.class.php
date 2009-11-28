<?php

class SmartestNumberSeries{
    
    protected $_name;
    protected $_values;
    
    public function setName($n){
        $this->_name = $n;
    }
    
    public function insertValue($value){
        if(is_numeric($value)){
            $this->_values[] = $value;
        }else{
            SmartestLog::getInstance('system')->log("SmartestNumberSeries::insertValue() expects a numeric value.");
            $this->_values[] = (int) $value;
        }
    }
    
    public function removeLastValue(){
        array_pop($this->_values);
    }
    
    public function getSum(){
        return array_sum($this->_values);
    }
    
    public function getAverage(){
        return array_sum($this->_values) / count($this->_values);
    }
    
    public function getValues(){
        return $this->_values;
    }
    
}