<?php

class SmartestManyToManyQuery{
    
    protected $_typeId = '';
    protected $_type;
    protected $_targetEntityIndex = 0;
    protected $_targetEntity = null;
    protected $_qualifyingEntityIndices = array();
    protected $_qualifyingEntities = array();
    protected $_allowedInstanceNames = array();
    protected $_foreignTableConstraints = array();
    protected $_sortFields = array();
    protected $_sortDirection = 'ASC';
    protected $_resultLimit = 0;
    protected $_helper;
    protected $_central_node_id; // only used in networks
    protected $_query = null;
    protected $database;
    protected $_draft_mode = false;
    
    public function __construct($type){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
        $this->_helper = new SmartestManyToManyHelper;
        
        $is_valid = $this->_helper->isValidType($type);
        
        if($is_valid){
            
            $this->_typeId = $type;
            $this->_type = $this->_helper->buildTypeObject($this->_typeId);
            
            if($this->_type->usesInstances()){
                $this->_sortField = 'ManyToManyLookups.mtmlookup_instance_name';
            }else{
                if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
                    $this->_sortField = $this->_type->getNetwork()->getForeignKeyField();
                }
            }
            
        }else{
            
            throw new SmartestException('The provided many-to-many relationship type:'.$type.' was invalid', SM_ERROR_USER);
            
        }
    }
    
    public function getTargetEntity(){
        return $this->_targetEntity;
    }
    
    public function setTargetEntityByIndex($target_entity_index){
        
        if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
            throw new SmartestException('You cannot set the target entity of a network method many-to-many relationship type:'.$this->_type->getMethod().'.', SM_ERROR_USER);
        }
        
        if(in_array($target_entity_index, $this->_qualifyingEntityIndices)){
            throw new SmartestException("Supplied target entity ID cannot also be a qualifying entity ID", SM_ERROR_USER);
        }else{
            if(is_numeric($target_entity_index) && ceil($target_entity_index) > 0 && ceil($target_entity_index) < 5){
                $this->_targetEntityIndex = $target_entity_index;
                $e = new SmartestManyToManyTargetEntity($this->_type->getEntityByIndex($target_entity_index));
                $this->_targetEntity = $e;
            }else{
                throw new SmartestException("Supplied target entity ID is not a number or is out of range", SM_ERROR_USER);
            }
        }
        
    }
    
    public function getSortFields(){
        
        return $this->_sortFields;
        
    }
    
    public function getSortFieldsForQuery(){
        
        if(empty($this->_sortFields)){
            if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
                
            }else{
                // $this->_sortField = $this->getTargetEntity()->getEntity()->getForeignKeyField();
                if($this->_type->usesInstances()){
                    return 'ManyToManyLookups.mtmlookup_instance_name';
                }else{
                    if($this->getTargetEntity()->getEntity()->hasDefaultSort()){
                        return $this->getTargetEntity()->getEntity()->getDefaultSort();
                    }else{
                        return $this->getTargetEntity()->getEntity()->getForeignKeyField();
                    }
                }
            }
        }else{
            return implode(', ', $this->_sortFields);
        }
        
    }
    
    public function createNetworkLinkBetween($id_1, $id_2){
        
        $id_1 = (int) $id_1;
        $id_2 = (int) $id_2;
        
        if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
            $link = new SmartestManyToManyLookup;
    	    $link->setEntityForeignKeyValue(1, $id_1);
    	    $link->setEntityForeignKeyValue(2, $id_2);
    	    $link->setType($this->_type->getId());
    	    $link->save();
        }else{
            throw new SmartestException('Error: SmartestManyToManyQuery::createNetworkLinkBetween() should only be used with Network method connections', SM_ERROR_USER);
        }
        
    }
    
    public function deleteNetworkLinkBetween($id_1, $id_2){
        
        $id_1 = (int) $id_1;
        $id_2 = (int) $id_2;
        
        if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
            $sql = "DELETE FROM ManyToManyLookups WHERE (mtmlookup_entity_1_foreignkey='".$id_1."' AND mtmlookup_entity_2_foreignkey='".$id_2."') OR (mtmlookup_entity_1_foreignkey='".$id_2."' AND mtmlookup_entity_2_foreignkey='".$id_1."')";
            $this->database->rawQuery($sql);
        }else{
            throw new SmartestException('Error: SmartestManyToManyQuery::deleteNetworkLinkBetween() should only be used with Network method connections', SM_ERROR_USER);
        }
        
    }
    
    public function deleteNetworkNodeById($id){
        
        $id = (int) $id;
        
        if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
            $sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_entity_1_foreignkey='".$id."' OR  mtmlookup_entity_2_foreignkey='".$id."'";
            $this->database->rawQuery($sql);
        }else{
            throw new SmartestException('Error: SmartestManyToManyQuery::deleteNetworkNodeById() should only be used with Network method connections', SM_ERROR_USER);
        }
        
        
    }
    
    // only for 'network' lookup types
    public function setCentralNodeId($id){
        if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
            $this->_central_node_id = $id;
        }else{
            throw new SmartestException('You cannot set the central node ID on a non-network method lookup type:'.$this->_type->getId().'.');
        }
    }
    
    // only for 'network' lookup types
    public function getCentralNodeId(){
        return $this->_central_node_id;
    }
    
    public function addQualifyingEntityByIndex($qualifying_entity_index, $value){
        if(!in_array($qualifying_entity_index, $this->_qualifyingEntityIndices)){
            if(is_numeric($qualifying_entity_index) && ceil($qualifying_entity_index) > 0 && ceil($qualifying_entity_index) < 5){
                $this->_qualifyingEntityIndices[] = $qualifying_entity_index;
                $e = new SmartestManyToManyQualifyingEntity($this->_type->getEntityByIndex($qualifying_entity_index));
                $e->setRequiredValue($value);
                $this->_qualifyingEntities[] = $e;
            }
        }
    }
    
    public function addForeignTableConstraint($full_field, $value, $operator=0){
        $c = new SmartestManyToManyQueryForeignTableConstraint($full_field, $value, $operator);
        $this->_foreignTableConstraints[] = $c;
    }
    
    public function addForeignTableOrConstraints(){
        
        $constraints = func_get_args();
        $group = new SmartestManyToManyQueryForeignTableConstraintGroup;
        
        foreach($constraints as $c){
            $operator = isset($c['operator']) ? $c['operator'] : 0;
            $group->addConstraint($c['field'], $c['value'], $operator);
        }
        
        $this->_foreignTableConstraints[] = $group;
        
    }
    
    public function clearForeignTableConstraints(){
        $this->_foreignTableConstraints = array();
    }
    
    public function addSortField($full_field){
        if(!in_array($full_field, $this->_sortFields)){
            $this->_sortFields[] = $full_field;
        }
    }
    
    public function setSortDirection($direction){
        
        if($direction != 'ASC' && $direction != 'DESC'){
            $direction = 'DESC';
            SmartestLog::getInstance('system')->log('SmartestManyToManyQuery->setSortDirection() only accepts ASC and DESC as values. '.$direction.' given.');
        }
        
        $this->_sortDirection = $direction;
    }
    
    public function setLimit($limit){
        $this->_resultLimit = (int) $limit;
    }
    
    public function addAllowedInstanceName($instance){
        if(!in_array($instance, $this->_allowedInstanceNames)){
            $this->_allowedInstanceNames[] = $instance;
            return true;
        }else{
            return false;
        }
    }
    
    public function restrictToInstanceName($instance){
        $this->_allowedInstanceNames = array($instance);
    }
    
    public function clearAllowedInstanceNames(){
        $this->_allowedInstanceNames = array();
    }
    
    public function getAllowedInstanceNames(){
        return $this->_allowedInstanceNames;
    }
    
    public function hasTableOverlap(){
        return in_array($this->_targetEntity->getEntity()->getTable(), $this->getQualifyingEntityTableNames());
    }
    
    public function getQualifyingEntityTableNames(){
        
        $names = array();
        
        foreach($this->_qualifyingEntities as $qe){
            $names[] = $qe->getEntity()->getTable();
        }
        
        return $names;
        
    }
    
    public function buildQuery($lookups_only=false, $all_phases=false){
        
        // SELECT Assets.* FROM Assets, TextFragments, ManyToManyLookups WHERE Assets.asset_id=ManyToManyLookups.mtmlookup_entity_1_foreignkey, TextFragments.textfragment_id=ManyToManyLookups.mtmlookup_entity_2_foreignkey
        
        if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
            
            // This type is a bi-directional mapping of the same entity type
            
            // What to select
            $query = "SELECT ";
            
            if($lookups_only){
                $query .= 'ManyToManyLookups.* FROM ';
            }else{
                $query .= $this->_type->getNetwork()->getTable().'.*, ManyToManyLookups.* FROM ';
            }
            
            $query .= $this->_type->getNetwork()->getTable().", ManyToManyLookups WHERE mtmlookup_type='".$this->_type->getId()."'";
            $query .= " AND ((mtmlookup_entity_1_foreignkey='".$this->_central_node_id."' AND mtmlookup_entity_2_foreignkey=".$this->_type->getNetwork()->getForeignKeyField()." AND mtmlookup_entity_2_foreignkey!='".$this->_central_node_id."') OR (mtmlookup_entity_2_foreignkey='".$this->_central_node_id."' AND mtmlookup_entity_1_foreignkey=".$this->_type->getNetwork()->getForeignKeyField()." AND mtmlookup_entity_1_foreignkey!='".$this->_central_node_id."'))";
            
            // observe foreign table constraints
            if(count($this->_foreignTableConstraints)){
                foreach($this->_foreignTableConstraints as $ftc){
                    $qf = ' AND '.$ftc->getSql();
                    $query .= $qf;
                }
            }
            
        }else{
            
            // This type is a directional mapping of different entity types
            
            // What to select
            $query = "SELECT ";
            
            if($lookups_only){
                $query .= 'ManyToManyLookups.* FROM ';
            }else{
                $query .= $this->_targetEntity->getEntity()->getTable().'.*, ManyToManyLookups.* FROM ';
            }

            // Names of tables to select from in query
            $tablenames = array();

            for($i=0;$i<$this->_type->getNumberOfEntities();$i++){
                $e = $i+1;
                $tablenames[] = $this->_type->getEntityByIndex($e)->getTable();
            }

            // add tables to select from
            $tablenames[] = 'ManyToManyLookups';
            $query .= implode(', ', $tablenames).' WHERE ';

            // filter out other lookup types
            $query .= 'ManyToManyLookups.mtmlookup_type=\''.$this->_type->getId().'\' AND ';
            
            if($this->_type->isPhased()){
                if($all_phases){
                    
                }else{
                    if($this->_draft_mode){
                        $query .= "(ManyToManyLookups.mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_DRAFT' OR ManyToManyLookups.mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_LIVE') AND ";
                    }else{
                        $query .= "(ManyToManyLookups.mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_OLD' OR ManyToManyLookups.mtmlookup_status_flag='SM_MTMLOOKUPSTATUS_LIVE') AND ";
                    }
                }
            }
            
            // Now, the WHERE clause. This is where it gets interesting!
            // For now, this won't support MTM relationships between entities in the same table - much of that functionality is covered in the 'network' type (see above)
            // TODO: we need to build in a checker and alternative query builder for when this is the case, for instance the 'related pages' feature.
        
            // tie foreign (ie Items.item_id) and local (ie ManyToManyLookups.mtmlookup_...) entity key together
            $qf = $this->_targetEntity->getFieldName()."=".$this->_targetEntity->getEntity()->getForeignKeyField();
            $query .= $qf;
        
            // loop through qualifying entities and values
            $ands = array();
        
            foreach($this->_qualifyingEntities as $q){
                $qf = '('.$q->getFieldName()."='".$q->getRequiredValue()."' AND ".$q->getFieldName()."=".$q->getEntity()->getForeignKeyField().")";
                $ands[] = $qf;
            }
        
            $qualifiers = implode(' AND ', $ands);
            $query .= ' AND '.$qualifiers;
        
            // parts of the query below this line can be left in either type of query
        
            // observe foreign table constraints
            if(count($this->_foreignTableConstraints)){
                foreach($this->_foreignTableConstraints as $ftc){
                    $qf = ' AND '.$ftc->getSql();
                    $query .= $qf;
                }
            }
        
            // restrict to certain instance names
            if(count($this->_allowedInstanceNames)){
                $qf = " AND ManyToManyLookups.mtmlookup_instance_name IN ('".implode("', '", $this->_allowedInstanceNames).'\')';
                $query .= $qf;
            }
        
        }
        
        $query .= ' ORDER BY '.$this->getSortFieldsForQuery();
        $query .= ' '.$this->_sortDirection;
        
        if($this->_resultLimit > 0){
            $query .= ' LIMIT '.$this->_resultLimit;
        }
        
        $this->_query = $query;
        return $query;
        
    }
    
    public function getLastQuery(){
        return $this->_query;
    }
    
    public function getReturnClassName(){
        
        if($this->_type->getReturnValueType() == 'meta:targetEntityClass'){
            
            return $this->getTargetEntity()->getEntity()->getClass();
            
        }else if(substr($this->_type->getReturnValueType(), 0, 6) == 'class:'){
        
            $class = substr($this->_type->getReturnValueType(), 6);
            
            if(class_exists($class)){
                $final_class = $class;
            }else{
                $final_class = $this->getTargetEntity()->getClass();
            }
            
            return $final_class;
            
        }
        
    }
    
    public function getLookupClassName(){
        
        if($this->getTargetEntity()->getEntity()->hasSpecifiedLookupClass()){
            return $this->getTargetEntity()->getEntity()->getSpecifiedLookupClass();
        }else{
            return 'SmartestManyToManyLookup';
        }
        
    }
    
    public function delete(){
        
        $query = $this->buildQuery();
        
        $result = $this->database->queryToArray($query);
        $mtml_ids = array();
        
        if(count($result)){
        
            foreach($result as $raw_lookup){
                $mtml_ids[] = $raw_lookup['mtmlookup_id'];
            }
        
            $sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_id IN ('".implode("', '", $mtml_ids)."')";
            $this->database->rawQuery($sql);
        
        }
        
    }
    
    public function retrieve($use_numeric_indices=false, $all_phases=false){
        
        $result = $this->database->queryToArray($this->buildQuery());
        $objects = array();
        $object_type = $this->getReturnClassName();
        
        foreach($result as $r){
            
            $o = new $object_type;
            $o->hydrate($r);
            
            if($use_numeric_indices){
                
                $objects[] = $o;
                
            }else{
            
                if($this->_type->usesInstances()){
                    $key = $r['mtmlookup_instance_name'];
                }else{
                    if($this->_type->getMethod() == 'SM_MTMLOOKUPMETHOD_NETWORK'){
                        $key = $r[$this->_type->getNetwork()->getForeignKeyField(false)];
                    }else{
                        $key = $r[$this->getTargetEntity()->getEntity()->getForeignKeyField(false)];
                    }
                }
            
                $objects[$key] = $o;
            
            }
            
        }
        
        return $objects;
        
    }
    
    public function retrieveLookups($all_phases=false){
        
        $result = $this->database->queryToArray($this->buildQuery(true, $all_phases));
        $objects = array();
        $object_type = $this->_type->getLookupClassname();
        
        foreach($result as $r){
            
            $o = new $object_type;
            $o->hydrate($r);
            
            if($use_numeric_indices){
                
                $objects[] = $o;
                
            }else{
            
                if($this->_type->usesInstances()){
                    $key = $r['mtmlookup_instance_name'];
                }else{
                    $k = 'mtmlookup_entity_'.$this->getTargetEntity()->getEntity()->getEntityIndex().'_foreignkey';
                    $key = $r[$k];
                }
            
                $objects[$key] = $o;
            
            }
            
        }
        
        return $objects;
        
    }
    
    public function setDraftMode($mode){
        
        /* if($status == 'SM_MTMLOOKUPSTATUS_LIVE' || $status == 'SM_MTMLOOKUPSTATUS_DRAFT' || $status == 'SM_MTMLOOKUPSTATUS_OLD'){
            $this->_status = $status;
        } */
        
        $this->_draft_mode = (bool) $mode;
        
    }
    
}