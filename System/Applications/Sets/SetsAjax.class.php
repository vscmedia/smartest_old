<?php

class SetsAjax extends SmartestSystemApplication{
    
    public function newConditionOperatorSelect(){
        $this->send($this->getRequestParameter('aspect'), 'aspect');
    }
    
    public function newConditionValueSelect(){
        
        $aspect = $this->getRequestParameter('aspect');
        $operator = $this->getRequestParameter('operator');
        
        if(is_numeric($aspect)){
            $property = new SmartestItemProperty;
            if($property->find($aspect)){
                $this->send($property, 'property');
                $this->send($property->renderInput('new_condition_value'), 'property_input_html');
            }
        }
        
        if($operator == 8 || $operator == 9){
            $du  = new SmartestDataUtility;
	        $this->send($du->getTags(), 'tags');
        }
        
        $this->send($this->getRequestParameter('v'), 'selectedValue');
        $this->send($aspect, 'aspect');
        $this->send($operator, 'operator');
    }
    
}