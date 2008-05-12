<?php

class SmartestManyToManyLookupType{
    
    protected $_id;
    protected $_return;
    protected $_method;
    protected $_entities = array();
    protected $_usesInstances = false;
    protected $_network = null;
    
    public function __construct($type){
        
        $this->_id = $type['id'];
        $this->_return = $type['return'];
        $this->_label = $type['label'];
        $this->_method = $type['method'];
        $this->_usesInstances = SmartestStringHelper::toRealBool($type['instances']);
        
        // build entity objects
        
        $entities = $type['entity'];
        
        if(is_array($entities) && $type['method'] != 'SM_MTMLOOKUPMETHOD_NETWORK'){
            foreach($entities as $e){
                $this->_entities[$e['index']] = new SmartestManyToManyEntity($e['table'], $e['foreignkey'], $e['index'], $e['class'], SmartestStringHelper::toRealBool($e['required']));
            }
        }
        
        if($type['method'] == 'SM_MTMLOOKUPMETHOD_NETWORK' && isset($type['network'])){
            $this->_network = new SmartestManyToManyNetwork($type['network']['table'], $type['network']['foreignkey'], $type['network']['class']);
        }
        
    }
    
    public function getId(){
        return $this->_id;
    }
    
    public function getReturnValueType(){
        return $this->_return;
    }
    
    public function getLabel(){
        return $this->_label;
    }
    
    public function getMethod(){
        return $this->_method;
    }
    
    public function usesInstances(){
        return $this->_usesInstances;
    }
    
    public function getNumberOfEntities(){
        return count($this->_entities);
    }
    
    public function getEntityByIndex($index){
        if(array_key_exists($index, $this->_entities)){
            return $this->_entities[$index];
        }
    }
    
    public function getNetwork(){
        if($this->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
            return $this->_network;
        }
    }
    
}
