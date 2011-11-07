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
    
    public function hydrateMixedListFromIdsArray($ids, $draft_mode=false){
        
        $results = $this->getSquareDbDataFromIdsArray($ids);
        $items = array();
        
        foreach($results as $item_id => $result){
            
            $first = reset($result);
            $model_id = $first['item_itemclass_id'];
            
            if($model = $this->getModelFromId($model_id)){
                
                $class_name = $model->getClassName();
                $item = new $class_name();
                $item->hydrateFromRawDbRecord($result);
                $item->setDraftMode($draft_mode);
                $items[] = $item;
                
            }
            
        }
        
        return $items;
        
    }
    
    protected function _hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode=false){

           $results = $this->getSquareDbDataFromIdsArray($ids, $model_id);
           $items = array();

           if($model = $this->getModelFromId($model_id)){

               $class_name = $model->getClassName();

               foreach($results as $item_id => $result){

                   $item = new $class_name();
                   $item->hydrateFromRawDbRecord($result);
                   $item->setDraftMode($draft_mode);
                   $items[$item->getId()] = $item;

               }

           }

           return $items;

       }
    
    public function hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode=false){
        
        return array_values($this->_hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode));
        
    }
    
    public function hydrateUniformListFromIdsArrayPreservingOrder($ids, $model_id, $draft_mode=false){
        
        $items = array();
        $raw_items = $this->_hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode);
        
        foreach($ids as $id){
            $items[] = $raw_items[$id];
        }
        
        return $items;
        
    }
    
    public function getRawDbDataFromIdsArray($ids, $model_id=''){
        
        $sql = "SELECT * FROM Items, ItemPropertyValues WHERE Items.item_deleted !=1 AND Items.item_id=ItemPropertyValues.itempropertyvalue_item_id AND ItemPropertyValues.itempropertyvalue_item_id IN ('".implode("','", $ids)."')";
        
        if(is_numeric($model_id)){
            $sql .= " AND Items.item_itemclass_id='".$model_id."'";
        }
        
        return $this->database->queryToArray($sql);
        
    }
    
    public function getSquareDbDataFromIdsArray($ids, $model_id=''){
        
        $included_item_ids = array();
        // echo count($this->getRawDbDataFromIdsArray($ids));
        
        $items = array();
        
        foreach($this->getRawDbDataFromIdsArray($ids, $model_id) as $result){
            
            $items[$result['item_id']][$result['itempropertyvalue_property_id']] = $result;
            
        }
        
        return $items;
        
    }

}