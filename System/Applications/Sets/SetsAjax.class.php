<?php

class SetsAjax extends SmartestSystemApplication{
    
    public function newConditionOperatorSelect(){
        $this->send($this->getRequestParameter('aspect'), 'aspect');
        if(is_numeric($this->getRequestParameter('aspect'))){
            $property = new SmartestItemProperty;
            if($property->find($this->getRequestParameter('aspect'))){
                $this->send($property, 'property');
                $this->send(true, 'ordinary_property_available');
            }else{
                $this->send(false, 'ordinary_property_available');
            }
        }else{
            $this->send(false, 'ordinary_property_available');
        }
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
    
    public function updateStaticSetOrder(){
        
        $set = new SmartestCmsItemSet;
        
        if($set->find((int) $this->getRequestParameter('set_id'))){
            
            if($set->getType() == 'DYNAMIC'){
	            exit;
	        }
	        
	        if($set->getSortDirection() == 'DESC'){
	            $set->updateOrderFromItemIdsList(array_reverse(explode(',', $this->getRequestParameter('item_ids'))));
            }else{
                $set->updateOrderFromItemIdsList(explode(',', $this->getRequestParameter('item_ids')));
            }
	        
        }
        
        exit;
    }
    
    public function updateSetLabelFromInPlaceEditField(){
        
        $set = new SmartestCmsItemSet;
	    
	    if($set->find($this->getRequestParameter('set_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $set->setLabel($this->getRequestParameter('new_label'));
	        $set->save();
	        echo $this->getRequestParameter('new_label');
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
        
    }
    
    public function updateSetNameFromInPlaceEditField(){
        
        $set = new SmartestCmsItemSet;
	    
	    if($set->find($this->getRequestParameter('set_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $set->setName(SmartestStringHelper::toVarName($this->getRequestParameter('new_name')));
	        $set->save();
	        echo $set->getName();
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
        
    }
    
    public function updateSetShared(){
        
        $set = new SmartestCmsItemSet;
	    
	    if($set->find($this->getRequestParameter('set_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $set->setShared((int) (bool) $this->getRequestParameter('is_shared'));
	        $set->save();
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
    }
    
    public function updateSetSortDirection(){
        
        $set = new SmartestCmsItemSet;
	    
	    if($set->find($this->getRequestParameter('set_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $set->setSortDirection($this->getRequestParameter('sort_direction'));
	        $set->save();
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
        
    }
    
}