<?php

class SmartestNumberSeries implements IteratorAggregate, Countable, SmartestStorableValue, SmartestSubmittableValue{
    
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
    
    public function count(){
        return count($this->_values);
    }
    
    public function &getIterator(){
        return new ArrayIterator($this->_values);
    }
    
    public function getSum(){
        return new SmartestNumeric(array_sum($this->_values));
    }
    
    public function getAverage(){
        return new SmartestNumeric(array_sum($this->_values) / count($this->_values));
    }
    
    public function getValues(){
        return $this->_values;
    }
    
    public function getStorableFormat(){
        return implode(',', $this->_values);
    }
    
    // Expects a comma-separated list of numeric ids
    public function hydrateFromStorableFormat($comma_separated_ids){
        $numbers_array = preg_split('/[,\s]+/', $comma_separated_ids);
        $this->_values = array();
        foreach($numbers_array as $n){
            if(is_numeric($n)){
                $this->_values[] = $n;
            }
        }
        return true;
    }
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($comma_separated_ids){
        $numbers_array = preg_split('/[,\s]+/', $comma_separated_ids);
        $this->_values = array();
        foreach($numbers_array as $n){
            if(is_numeric($n)){
                $this->_values[] = $n;
            }
        }
    }
    
}