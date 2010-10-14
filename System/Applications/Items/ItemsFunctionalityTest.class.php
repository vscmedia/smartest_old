<?php

class ItemsFunctionalityTest extends SmartestSystemApplication{
  
    public function ipv(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                if($this->getRequestParameter('mode') == 'live'){
                    $mode = 'live';
                }else{
                    $item->setDraftMode(true);
                    $mode = 'draft';
                }
                
                $this->send($item, 'item');
                $this->send($mode, 'mode');
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $this->send($item->getModel()->getPropertyNames(true), 'properties');
                        
                        $property = new SmartestItemProperty;
                        $property->find($this->getRequestParameter('property_id'));
                        $this->send($property, 'property');
                        
                        $value = $item->getPropertyValueByNumericKey($this->getRequestParameter('property_id'));
                        $raw_value = $item->getPropertyRawValueByNumericKey($this->getRequestParameter('property_id'));
                        $this->send($value, 'value');
                        $this->send($raw_value, 'raw_value');
                        $this->send(print_r($value, true), 'output');
                        
                    }
                
                }
                
            }
            
        }
        
    }
    
}