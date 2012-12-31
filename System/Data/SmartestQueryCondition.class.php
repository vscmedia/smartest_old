<?php

class SmartestQueryCondition{
    
    protected $_value;
    protected $_property;
    protected $_operator = 0;
    protected $_ids_array = array();
    
    public function __construct($value, $property, $operator){
        
        if($value instanceof SmartestStorableValue){
            $this->_value = $value;
        }else{
            throw new SmartestException("SmartestQueryCondition can only accept values that implement SmartestStorableValue");
        }
        
        if($property instanceof SmartestItemProperty || $property instanceof SmartestPseudoItemProperty){
            $this->_property = $property;
        }
        
        $this->_operator = (int) $operator;
        
    }
    
    public function getOperator(){
        return $this->_operator;
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function getValueAsString(){
        return $this->_value->__toString();
    }
    
    public function getProperty(){
        return $this->_property;
    }
    
    public function getIdsArray(){
        return $this->_ids_array;
    }
    
    public function setIdsArray($array){
        $this->_ids_array = $array;
    }
    
    public function getSql(){
        
        switch($this->_operator){

		    case 0:
			return "='".$this->_value->getStorableFormat()."'";

			case 1:
			return " != '".$this->_value->getStorableFormat()."'";

			case 2:
			return " LIKE '%".$this->_value->getStorableFormat()."%'";

			case 3:
			return " NOT LIKE '%".$this->_value->getStorableFormat()."%'";

			case 4:
			return " LIKE '".$this->_value->getStorableFormat()."%'";

			case 5:
			return " LIKE '%".$this->_value->getStorableFormat()."'";
		
			case 6:
			// for dates that are always now
			if($this->_value->getStorableFormat() == '%NOW%'){
			    return " > '".time()."'";
		    }else{
		        return " > '".$this->_value->getStorableFormat()."'";
		    }
		
			case 7:
			// for dates that are always now
			if($this->_value->getStorableFormat() == '%NOW%'){
			    return " < '".time()."'";
		    }else{
		        return " < '".$this->_value->getStorableFormat()."'";
		    }
			
        }
        
    }
    
}