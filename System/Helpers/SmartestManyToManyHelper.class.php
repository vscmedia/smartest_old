<?php

// this class must be instantiated

class SmartestManyToManyEntity{
    
    protected $_table = '';
    protected $_foreignKey = '';
    protected $_entityIndex;
    protected $_class = '';
    protected $_required = false;
    
    public function __construct($table, $foreignKey, $entityIndex, $class, $required=false){
        $this->_table = $table;
        $this->_foreignKey = $foreignKey;
        $this->_entityIndex = $entityIndex;
        $this->_class = $class;
        $this->_required = $required;
    }
    
    public function getTable(){
        return $this->_table;
    }
    
    public function getForeignKeyField(){
        return $this->_table.'.'.$this->_foreignKey;
    }
    
    public function getEntityIndex(){
        return $this->_entityIndex;
    }
    
    public function getClass(){
        var_dump($this->_class);
        return $this->_class;
    }
    
    public function isRequired(){
        return $this->_required;
    }
    
}

class SmartestManyToManyTargetEntity{
    
    protected $_entity;
    
    public function __construct(SmartestManyToManyEntity $entity){
        $this->_entity = $entity;
    }
    
    public function getEntity(){
        return $this->_entity;
    }
    
}

class SmartestManyToManyQualifyingEntity extends SmartestManyToManyTargetEntity{
    
    protected $_required_value = '';
    
    public function getRequiredValue(){
        return $this->_required_value;
    }
    
    public function setRequiredValue($value){
        $this->_required_value = (int) $value;
    }
    
    public function getFieldName(){
        return 'ManyToManyLookups.mtmlookup_entity_'.$this->_entity->getEntityIndex().'_foreignkey';
    }
    
}

class SmartestManyToManyLookupType{
    
    protected $_id;
    protected $_return;
    protected $_entities = array();
    
    public function __construct($type){
        
        $this->_id = $type['id'];
        $this->_return = $type['return'];
        $this->_label = $type['label'];
        
        // build entity objects
        $entities = $type['entity'];
        foreach($entities as $e){
            $this->_entities[$e['index']] = new SmartestManyToManyEntity($e['table'], $e['foreignkey'], $e['index'], $e['class'], SmartestStringHelper::toRealBool($e['required']));
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
    
    public function getNumberOfEntities(){
        return count($this->_entities);
    }
    
    public function getEntityByIndex($index){
        if(array_key_exists($index, $this->_entities)){
            return $this->_entities[$index];
        }
    }
    
}

class SmartestManyToManyQueryForeignTableConstraint{
    
    protected $_field;
    protected $_value;
    protected $_operator;
    
    public function __construct($field, $value, $operator){
        $this->_field = $field;
        $this->_value = $value;
        $this->_operator = $operator;
    }
    
    public function getField(){
        return $this->_field;
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function getEscapedValue(){
        return mysql_real_escape_string($this->_value);
    }
    
    public function getOperator(){
        return $this->_operator;
    }
    
}

class SmartestManyToManyHelper{
    
    protected $_lookupTypes = array();
    
    public function __construct(){
        
        
        
    }
    
    static function getLookupTypesXmlData(){
	    
	    $file_path = SM_ROOT_DIR.'System/Core/Types/mtmrelationshiptypes.xml';
	    
	    if(SmartestCache::hasData('lookuptypes_xml_file_hash', true)){
	        
	        $old_hash = SmartestCache::load('lookuptypes_xml_file_hash', true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save('lookuptypes_xml_file_hash', $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['type'];
	            SmartestCache::save('lookuptypes_xml_file_data', $data, -1, true);
            }else{
                $data = SmartestCache::load('lookuptypes_xml_file_data', true);
            }
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('lookuptypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('lookuptypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	public function getLookupTypes(){
	    
	    if(empty($this->_lookupTypes)){
	        
	        $data = self::getLookupTypesXmlData();
	    
    	    $raw_types = $data;
    	    $types = array();
	    
    	    foreach($raw_types as $raw_type){
	        
    	        $types[$raw_type['id']] = $raw_type;
	        
    	        if(!defined($raw_type['id'])){
    	            define($raw_type['id'], $raw_type['id']);
    	        }
	        
    	        if(is_array($types[$raw_type['id']]['entity'])){
	            
    	            if(isset($types[$raw_type['id']]['entity']['table'])){
	                
    	                $entity = $types[$raw_type['id']]['entity'];
    	                $types[$raw_type['id']]['entity'] = array();
    	                $types[$raw_type['id']]['entity'][0] = $suffix;
	                
    	            }
	        
                }
        
    	    }
	        
	        $this->_lookupTypes = $types;
    	    return $types;
	    
        }else{
            return $this->_lookupTypes;
        }
	}
    
    public function isValidType($type_id){
        $types = $this->getLookupTypes();
        return array_key_exists($type_id, $types);
    }
    
    public function buildTypeObject($type){
        if($this->isValidType($type)){
            $types = $this->getLookupTypes();
            $t_array = $types[$type];
            $t = new SmartestManyToManyLookupType($t_array);
            return $t;
        }
    }
    
    public function createLookup($type, $e1_fk, $e2_fk, $e3_fk='', $e4_fk=''){
        
        $l = new SmartestManyToManyLookup;
        $l->setType($type);
        $l->setEntityForeignKeyValue(1, $e1_fk);
        $l->setEntityForeignKeyValue(2, $e2_fk);
        
        if($e3_fk){
            $l->setEntityForeignKeyValue(3, $e3_fk);
        }
        
        if($e4_fk){
            $l->setEntityForeignKeyValue(4, $e4_fk);
        }
        
        $l->save();
        
    }
        
}