<?php

class SmartestSetItemLookup extends SmartestBaseSetItemLookup{
    
    // Auto-Generated By Smartest
    
    public function loadForOrderChange($set_id, $item_id){
        
        $sql = "SELECT * FROM SetsItemsLookup WHERE setlookup_set_id='".$set_id."' AND setlookup_item_id='".$item_id."'";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $this->hydrate($result[0]);
            return true;
        }else{
            return false;
        }
        
    }
    
    public function loadForOrderChangeByPosition($set_id, $position){
        
        $sql = "SELECT * FROM SetsItemsLookup WHERE setlookup_set_id='".$set_id."' AND setlookup_order='".$position."'";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $this->hydrate($result[0]);
            return true;
        }else{
            return false;
        }
        
    }
    
}