<?php

class SmartestDataAppearanceHelper{
    
    protected $database;
    
    public function __construct(){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
    }
    
    public function getItemAppearsOnPage($item_id, $page_id){
        
        /* $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGE_ITEM_APPS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(2, $item_id);
        $q->addQualifyingEntityByIndex(1, $page_id);*/
        $sql = "SELECT mtmlookup_id FROM ManyToManyLookups WHERE `mtmlookup_type` = 'SM_MTMLOOKUP_PAGE_ITEM_APPS' AND mtmlookup_entity_1_foreignkey='".$page_id."' AND mtmlookup_entity_2_foreignkey='".$item_id."'";
        $result = $this->database->queryToArray($sql, true);
        
        // var_dump((bool) count($result));
        return (bool) count($result);
        
    }
    
    public function getDataSetAppearsOnPage($set_id, $page_id){
        
        /* $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGE_SET_APPS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(2, $set_id);
        $q->addQualifyingEntityByIndex(1, $page_id);
        $result = $q->retrieve();*/
        
        $sql = "SELECT mtmlookup_id FROM ManyToManyLookups WHERE `mtmlookup_type` = 'SM_MTMLOOKUP_PAGE_SET_APPS' AND mtmlookup_entity_1_foreignkey='".$page_id."' AND mtmlookup_entity_2_foreignkey='".$set_id."'";
        $result = $this->database->queryToArray($sql, true);
        
        return (bool) count($result);
        
    }
    
    public function setItemAppearsOnPage($item_id, $page_id){
        
        if(!$this->getItemAppearsOnPage($item_id, $page_id)){
            
            $this->_setItemAppearsOnPage($item_id, $page_id);
            
        }
        
    }
    
    public function setDataSetAppearsOnPage($set_id, $page_id){
        
        if(!$this->getDataSetAppearsOnPage($set_id, $page_id)){
            
            $this->_setDataSetAppearsOnPage($set_id, $page_id);
            
        }
        
    }
    
    private function _setItemAppearsOnPage($item_id, $page_id){
        
        // echo "Linking Item $item_id to page $page_id.";
        $a = new SmartestCmsItemAppearance;
        $a->setItemId($item_id);
        $a->setPageId($page_id);
        $a->save();
        
    }
    
    private function _setDataSetAppearsOnPage($set_id, $page_id){
        
        // echo "Linking Set $set_id to page $page_id.";
        $a = new SmartestDataSetAppearance;
        $a->setDataSetId($set_id);
        $a->setPageId($page_id);
        $a->save();
        
    }
    
    public function clearItemAppearancesOnPage($page_id){
        
        
        
    }
    
    public function clearDataSetAppearancesOnPage($page_id){
        
        
        
    }
    
    public function clearItemAppearances($item_id, $site_id){
    
        
    
    }
    
    public function clearDataSetAppearances($set_id, $site_id){
        
        
        
    }
    
}