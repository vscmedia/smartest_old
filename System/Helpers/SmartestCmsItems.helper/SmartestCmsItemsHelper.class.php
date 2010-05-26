<?php

class SmartestCmsItemsHelper{
    
    protected $database;
    protected $_models;
    
    public function __construct(){
        
        $this->database = SmartestDatabase::getInstance('SMARTEST');
        
    }
    
    public function getModelFromId($id){
        
        if(isset($this->_models[$id])){
            return $this->_models[$id];
        }else{
            $m = new SmartestModel;
            if($m->find($id)){
                $this->_models[$id] = $m;
                return $this->_models[$id];
            }else{
                return null;
            }
        }
        
    }
    
    public function hydrateMixedListFromIdsArray($ids){
        
        $results = $this->getSquareDbDataFromIdsArray($ids);
        $items = array();
        // print_r($results);
        
        foreach($results as $item_id => $result){
            
            // print_r($result['item_itemclass_id']);
            $first = reset($result);
            $model_id = $first['item_itemclass_id'];
            
            if($model = $this->getModelFromId($model_id)){
                
                $class_name = $model->getClassName();
                $item = new $class_name();
                $item->hydrateFromRawDbRecord($result);
                $items[] = $item;
                
            }
            
        }
        
        // print_r($this->database->getDebugInfo());
        
        return $items;
        
    }
    
    public function hydrateUniformListFromIdsArray($ids){
        
        if($model = $this->getModelFromId($result['item_itemclass_id'])){
            
            $class_name = $model->getClassName();
            
            foreach($results as $item_id => $result){
            
                $item = new $class_name();
                $item->hydrateFromRawDbRecord($result);
                $items[] = $item;
                
            }
            
        }
        
        return $items;
        
    }
    
    public function getRawDbDataFromIdsArray($ids){
        
        // $items_sql = "SELECT * FROM Items WHERE Items.item_id IN ('".implode("','", $ids)."')";
        $sql = "SELECT * FROM Items, ItemPropertyValues WHERE Items.item_id=ItemPropertyValues.itempropertyvalue_item_id AND ItemPropertyValues.itempropertyvalue_item_id IN ('".implode("','", $ids)."')";
        // echo $sql;
        // $results = $this->database->queryToArray($sql);
        return $this->database->queryToArray($sql);
        
    }
    
    public function getSquareDbDataFromIdsArray($ids){
        
        $included_item_ids = array();
        // echo count($this->getRawDbDataFromIdsArray($ids));
        
        $items = array();
        
        foreach($this->getRawDbDataFromIdsArray($ids) as $result){
            
            $items[$result['item_id']][$result['itempropertyvalue_property_id']] = $result;
            
        }
        
        return $items;
        
    }

}