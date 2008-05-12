<?php

class SmartestManyToManyQualifyingEntity extends SmartestManyToManyTargetEntity{
    
    protected $_required_value = '';
    
    public function getRequiredValue(){
        return $this->_required_value;
    }
    
    public function setRequiredValue($value){
        $this->_required_value = (int) $value;
    }
    
}