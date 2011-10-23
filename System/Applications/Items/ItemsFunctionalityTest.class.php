<?php

class ItemsFunctionalityTest extends SmartestSystemApplication{
  
    public function ipv(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                if($this->getRequestParameter('mode') == 'live'){
                    $mode = 'live';
                    $draft = false;
                }else{
                    $item->setDraftMode(true);
                    $mode = 'draft';
                    $draft = true;
                }
                
                $this->send($item, 'item');
                $this->send($mode, 'mode');
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $this->send($item->getModel()->getPropertyNames(true), 'properties');
                        
                        /* $property = new SmartestItemProperty;
                        $property->find($this->getRequestParameter('property_id'));
                        $this->send($property, 'property'); */
                        
                        $ipv = SmartestDataUtility::getItemPropertyValue($this->getRequestParameter('item_id'), $this->getRequestParameter('property_id'));
                        $value = $draft ? $ipv->getDraftContent() : $ipv->getContent();
                        $raw_value = $ipv->getRawValue($draft);
                        $property = $ipv->getProperty();
                        $this->send($property, 'property');
                        $this->send($ipv, 'ipv');
                        
                        // $value = $item->getPropertyValueByNumericKey($this->getRequestParameter('property_id'));
                        // $raw_value = $item->getPropertyRawValueByNumericKey($this->getRequestParameter('property_id'));
                        $this->send($value, 'value');
                        $this->send($raw_value, 'raw_value');
                        $this->send(print_r($value, true), 'output');
                        
                    }
                
                }
                
            }
            
        }
        
    }
    
}