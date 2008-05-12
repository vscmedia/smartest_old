<?php

class SmartestManyToManyTargetEntity{
    
    protected $_entity;
    
    public function __construct(SmartestManyToManyEntity $entity){
        $this->_entity = $entity;
    }
    
    public function getEntity(){
        return $this->_entity;
    }
    
    public function getFieldName(){
        return 'ManyToManyLookups.mtmlookup_entity_'.$this->_entity->getEntityIndex().'_foreignkey';
    }
    
}