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
    protected $_sortField = 'ManyToManyLookups.mtmlookup_instance_name';
    protected $_helper;
    protected $database;
    
    public function __construct($type){
        
        $this->database = SmartestPersistentObject::get('db:main');
        
        $this->_helper = new SmartestManyToManyHelper;
        
        if($this->_helper->isValidType($type)){
            
            $this->_typeId = $type;
            $this->_type = $this->_helper->buildTypeObject($this->_typeId);
            
        }
    }
    
    public function getTargetEntity(){
        return $this->_targetEntity;
    }
    
    public function setTargetEntityByIndex($target_entity_index){
        if(!in_array($target_entity_index, $this->_qualifyingEntityIndices)){
            if(is_numeric($target_entity_index) && ceil($target_entity_index) > 0 && ceil($target_entity_index) < 5){
                $this->_targetEntityIndex = $target_entity_index;
                $e = new SmartestManyToManyQualifyingEntity($this->_type->getEntityByIndex($target_entity_index));
                $this->_targetEntity = $e;
            }
        }
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
    
    public function addForeignTableConstraint($full_field, $value, $operator){
        $c = new SmartestManyToManyQueryForeignTableConstraint($full_field, $value, $operator);
        $this->_foreignTableConstraints[] = $c;
    }
    
    public function clearForeignTableConstraints(){
        $this->_foreignTableConstraints = array();
    }
    
    public function addSortField($full_field){
        $this->_sortField = $full_field;
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
    
    public function buildQuery(){
        
        // SELECT Assets.* FROM Assets, TextFragments, ManyToManyLookups WHERE Assets.asset_id=ManyToManyLookups.mtmlookup_entity_1_foreignkey, TextFragments.textfragment_id=ManyToManyLookups.mtmlookup_entity_2_foreignkey
        
        // What to select
        $query = "SELECT ";
        $query .= $this->_targetEntity->getEntity()->getTable().'.*, ManyToManyLookups.* FROM ';
        
        // Names of tables to select from in query
        $tablenames = array();
        
        for($i=0;$i<$this->_type->getNumberOfEntities();$i++){
            $e = $i+1;
            $tablenames[] = $this->_type->getEntityByIndex($e)->getTable();
        }
        
        $tablenames[] = 'ManyToManyLookups';
        $query .= implode(', ', $tablenames).' WHERE ';
        
        // Now, the WHERE clause. This is where it gets interesting!
        // For now, this won't support MTM relationships between entities in the same table
        // TODO: we need to build in a checker and alternative query builder for when this is the case, for instance the 'related pages' feature.
        
        $query .= 'ManyToManyLookups.mtmlookup_type=\''.$this->_type->getId().'\' AND ';
        
        // tie foreign and primary key together
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
                $qf = ' AND '.$ftc->getField().''.$this->convertOperatorConstant($ftc->getOperator(), $ftc->getValue());
                $query .= $qf;
            }
        }
        
        // restrict to certain instance names
        if(count($this->_allowedInstanceNames)){
            $qf = " AND ManyToManyLookups.mtmlookup_instance_name IN ('".implode("', '", $this->_allowedInstanceNames).'\')';
            $query .= $qf;
        }
        
        $query .= ' ORDER BY '.$this->_sortField;
        return $query;
        
    }
    
    public function getReturnClassName(){
        
        // $type = $this->getTypeInfo();
        
        if($this->_type->getReturnValueType() == 'meta:targetEntityClass'){
            // echo 'meta';
            return $this->getTargetEntity()->getClass();
        }else if(substr($this->_type->getReturnValueType(), 0, 6) == 'class:'){
            // echo 'specific';
            $class = substr($this->_type->getReturnValueType(), 6);
            
            if(class_exists($class)){
                $final_class = $class;
            }else{
                $final_class = $this->getTargetEntity()->getClass();
            }
            
            // echo $final_class;
            return $final_class;
            
        }
        
    }
    
    public function retrieve(){
        
        $result = $this->database->queryToArray($this->buildQuery());
        $objects = array();
        $object_type = $this->getReturnClassName();
        // var_dump($object_type);
        
        foreach($result as $r){
            $o = new $object_type;
            $o->hydrate($r);
            $objects[$r['mtmlookup_instance_name']] = $o;
        }
        
        return $objects;
        
    }
    
    protected function convertOperatorConstant($c, $value){
        switch($c){

		    case 0:
			return "='".$value."'";
			break;

			case 1:
			return " != '".$value."'";
			break;

			case 2:
			return " LIKE '%".$value."%'";
			break;

			case 3:
			return " NOT LIKE '%".$value."%'";
			break;

			case 4:
			return " LIKE '".$value."%'";
			break;

			case 5:
			return " LIKE '%".$value."'";
			break;
		
			case 6:
			return " > '".$value."'";
			break;
		
			case 7:
			return " < '".$value."'";
			break;
        }
    }
    
}