<?php

class SmartestManyToManyMappedRelationship{

    protected $_type;
    protected $_helper;
    protected $_central_entity_item_id;
    protected $_central_entity_index;
    protected $_target_entity_index;
    protected $_stored_new_connections = array();
    protected $_all_ids = array();
    protected $_all_ids_query_performed = false;
    protected $database;
    protected $_new_connections = array();
    protected $_deleted_connections = array();
    
    public function __construct($type_code){
        
        $this->_helper = new SmartestManyToManyHelper;
        $this->database = SmartestDatabase::getInstance('SMARTEST');
        // echo $type_code;
        // var_dump($this->_helper->isValidType($type_code));
        if($this->_helper->isValidType($type_code)){
            $this->_type = $this->_helper->buildTypeObject($type_code);
            // print_r($this->_type);
        }else{
            throw new SmartestException('The provided many-to-many relationship type:'.$type_code.' was invalid', SM_ERROR_USER);
        }
        
    }
    
    public function setCentralEntityObjectId($entity_id){
        
        $this->_central_entity_item_id = (int) $entity_id;
        
    }
    
    public function setCentralEntityByIndex($entity_index){
        
        $this->_central_entity_index = (int) $entity_index;
        
    }
    
    public function setTargetEntityByIndex($entity_index){
        
        $this->_target_entity_index = (int) $entity_index;
        
    }
    
    public function getExistingMappedObjects(){
        // $ids = $this->getExistingMappedObjectIds();
    }
    
    public function getExistingLookupObjects(){
        if($this->_central_entity_index){
            if($this->_central_entity_item_id){
                
                $q = new SmartestManyToManyQuery($this->_type->getId());
                $q->setTargetEntityByIndex($this->_target_entity_index);
                $q->addQualifyingEntityByIndex($this->_central_entity_index, $this->_central_entity_item_id);
                $lookups = $q->retrieveLookups(true);
                return $lookups;
                
            }else{
                throw new SmartestException('Cannot perform query because central entity object id is not set', SM_ERROR_CONFIG);
            }
        }else{
            throw new SmartestException('Cannot perform query because central entity index is not set', SM_ERROR_USER);
        }
    }
    
    public function getExistingMappedObjectIds($mode=SM_MTMLOOKUPMODE_ALL){
        
        if(!$this->_all_ids_query_performed){
            
            $connections = $this->getExistingLookupObjects();
            $ids = array();
        
            foreach($connections as $v){
                $ids[$v->getEntityForeignKeyValue($this->_target_entity_index)] = $v->getStatusFlag();
            }
            
            $this->_all_ids_query_performed = true;
            $this->_all_ids = $ids;
            
        }
        
        if($mode==SM_MTMLOOKUPMODE_DRAFT){
            $draft_ids = array();
            foreach($this->_all_ids as $k => $v){
                if($v != 'SM_MTMLOOKUPSTATUS_OLD'){
                    $draft_ids[$k] = $v;
                }
            }
            return $draft_ids;
        }else if($mode==SM_MTMLOOKUPMODE_PUBLIC){
            $draft_ids = array();
            foreach($this->_all_ids as $k => $v){
                if($v != 'SM_MTMLOOKUPSTATUS_DRAFT'){
                    $draft_ids[$k] = $v;
                }
            }
            return $draft_ids;
        }else{
            return $this->_all_ids;
        }
        
    }
    
    public function getIds($mode=SM_MTMLOOKUPMODE_PUBLIC){
        $ids = $this->getExistingMappedObjectIds($mode);
        $raw = array();
        foreach($ids as $id=>$status){
            $raw[] = $id;
        }
        return $raw;
    }
    
    public function addConnection($object_id){
        $this->_new_connections[] = $object_id;
    }
    
    public function removeConnection($object_id){
        $this->_deleted_connections[] = $object_id;
    }
    
    public function saveUpdates(){
        $this->saveNewConnections();
        $this->deleteRemovedConnections();
    }
    
    public function deleteRemovedConnections(){
        
        $existing_ids = $this->getExistingMappedObjectIds();
        $live_ids = array();
        $draft_only_ids = array();
        $phased = $this->_type->isPhased();
        
        foreach($this->_deleted_connections as $id){
            if(array_key_exists($id, $existing_ids)){
                
                if($phased){
                    if($existing_ids[$id] == 'SM_MTMLOOKUPSTATUS_DRAFT'){
                        $draft_only_ids[] = $id;
                    }else if($existing_ids[$id] == 'SM_MTMLOOKUPSTATUS_LIVE'){
                        $live_ids[] = $id;
                    }
                }else{
                    $live_ids[] = $id;
                }
            }
        }
        
        if($phased){
            
            if(count($live_ids)){
                $update_sql = "UPDATE ManyToManyLookups SET mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_OLD' WHERE mtmlookup_type='".$this->_type->getId()."' AND mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_LIVE' AND mtmlookup_entity_".$this->_central_entity_index."_foreignkey='".$this->_central_entity_item_id."' AND mtmlookup_entity_".$this->_target_entity_index."_foreignkey IN (".implode(', ', $live_ids).")";
                $this->database->rawQuery($update_sql);
            }
            
            if(count($draft_only_ids)){
                $delete_sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='".$this->_type->getId()."' AND mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_DRAFT' AND mtmlookup_entity_".$this->_central_entity_index."_foreignkey='".$this->_central_entity_item_id."' AND mtmlookup_entity_".$this->_target_entity_index."_foreignkey IN (".implode(', ', $draft_only_ids).")";
                $this->database->rawQuery($delete_sql);
            }
            
        }else{
            
            if(count($live_ids)){
                $delete_sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='".$this->_type->getId()."' AND mtmlookup_entity_".$this->_central_entity_index."_foreignkey='".$this->_central_entity_item_id."' AND mtmlookup_entity_".$this->_target_entity_index."_foreignkey IN (".implode(', ', $draft_only_ids).")";
                $this->database->rawQuery($delete_sql);
            }
        }
    }
    
    public function saveNewConnections(){
        
        $existing_ids = $this->getExistingMappedObjectIds();
        $reinstate_ids = array();
        $new_ids = array();
        $phased = $this->_type->isPhased();
        
        if($phased){
            $newstatus = 'SM_MTMLOOKUPSTATUS_DRAFT';
        }else{
            $newstatus = 'SM_MTMLOOKUPSTATUS_LIVE';
        }
        
        foreach($this->_new_connections as $id){
            if(array_key_exists($id, $existing_ids)){
                // if it's live and draft, do nothing
                // if it's draft only, do nothing
                // but if it's live only (OLD (ie deleted)), add it to connections that should be made live and draft (reinstated)
                if($phased && $existing_ids[$id] == 'SM_MTMLOOKUPSTATUS_OLD'){
                    $reinstate_ids[] = $id;
                }
                
            }else{
                // add its ID to IDs that should be inserted later in this function
                $new_ids[] = $id;
                // add its ID to $this->_all_ids
                $this->_all_ids[$id] = 'SM_MTMLOOKUPSTATUS_DRAFT';
            }
        }
        
        if(count($reinstate_ids)){
            $update_sql = "UPDATE ManyToManyLookups SET mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_LIVE' WHERE mtmlookup_type='".$this->_type->getId()."' AND mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_OLD' AND mtmlookup_entity_".$this->_central_entity_index."_foreignkey='".$this->_central_entity_item_id."' AND mtmlookup_entity_".$this->_target_entity_index."_foreignkey IN (".implode(', ', $reinstate_ids).")";
            $this->database->rawQuery($update_sql);
        }
        
        if(count($new_ids)){
            
            $insert_sql = "INSERT INTO ManyToManyLookups (mtmlookup_type, mtmlookup_status_flag, mtmlookup_entity_".$this->_target_entity_index."_foreignkey, mtmlookup_entity_".$this->_central_entity_index."_foreignkey) VALUES ";
            $fragments = array();
            
            if($this->_type->isPhased()){
                $newstatus = 'SM_MTMLOOKUPSTATUS_DRAFT';
            }else{
                $newstatus = 'SM_MTMLOOKUPSTATUS_LIVE';
            }
            
            foreach($new_ids as $nid){
                $fragments[] = "('".$this->_type->getId()."', '".$newstatus."', '".$nid."', '".$this->_central_entity_item_id."')";
            }
            
            $insert_sql .= implode(',', $fragments);
            $this->database->rawQuery($insert_sql);
            
        }
        
    }
    
    /* public function connect($object_id, $entity_index){
        
        $l = new SmartestManyToManyLookup;
        $l->setType($this->_type);
        $l->setEntityForeignKeyValue($this->_central_entity_index, $this->_central_entity_item_id);
        $l->setEntityForeignKeyValue($entity_index, $object_id);
        
        if($this->_type->isPhased()){
            $l->setStatusFlag('SM_MTMLOOKUPSTATUS_DRAFT');
        }else{
            $l->setStatusFlag('SM_MTMLOOKUPSTATUS_LIVE');
        }
        
        $l->save();
        
    } */
    
    public function updateTo($new_ids){
        
        $phased = $this->_type->isPhased();
        $status = $phased ? 'SM_MTMLOOKUPMODE_DRAFT' : 'SM_MTMLOOKUPMODE_ALL';
        $existing_ids = $this->getExistingMappedObjectIds(constant($status));
        $ids_to_add = array();
        $ids_to_remove = array();
        
        foreach($new_ids as $id){
            if(!array_key_exists($id, $existing_ids)){
                // put this down as a new addition
                $ids_to_add[] = $id;
            }
        }
        
        foreach($existing_ids as $k=>$id){
            if(!in_array($k, $new_ids)){
                // put this down as a removal
                $ids_to_remove[] = $k;
            }
        }
        
        foreach($ids_to_add as $id){
            $this->addConnection($id);
        }
        
        foreach($ids_to_remove as $id){
            $this->removeConnection($id);
        }
        
        $this->deleteRemovedConnections();
        $this->saveNewConnections();
        
    }
    
    public function publish(){
        if($this->_type->isPhased()){
            
            // grab all ids
            $ids = $this->getExistingMappedObjectIds();
            $draft = array();
            $old = array();
            
            foreach($ids as $id=>$status){
                
                if($status == 'SM_MTMLOOKUPSTATUS_DRAFT'){
                    $draft[] = $id;
                }else if($status == 'SM_MTMLOOKUPSTATUS_OLD'){
                    $old[] = $id;
                }
                
            }
            
            // draft and live left alone
            
            // live only get deleted
            if(count($old)){
                $delete_sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='".$this->_type->getId()."' AND mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_OLD' AND mtmlookup_entity_".$this->_central_entity_index."_foreignkey='".$this->_central_entity_item_id."' AND mtmlookup_entity_".$this->_target_entity_index."_foreignkey IN (".implode(', ', $old).")";
                $this->database->rawQuery($delete_sql);
            }
            
            // draft only get made live
            if(count($draft)){
                $update_sql = "UPDATE ManyToManyLookups SET mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_LIVE' WHERE mtmlookup_type='".$this->_type->getId()."' AND mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_DRAFT' AND mtmlookup_entity_".$this->_central_entity_index."_foreignkey='".$this->_central_entity_item_id."' AND mtmlookup_entity_".$this->_target_entity_index."_foreignkey IN (".implode(', ', $draft).")";
                $this->database->rawQuery($update_sql);
            }
            
        }else{
            // can't be published because it's not phased
        }
    }

}