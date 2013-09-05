<?php

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
	            
	            if($types[$raw_type['id']]['method'] == 'SM_MTMLOOKUPMETHOD_MAP' && is_array($types[$raw_type['id']]['entity'])){
	            
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
        return $l->getId();
        
    }
    
    public static function convertOperatorConstant($c, $value){
        switch($c){

		    case 0:
			return " ='".$value."'";
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
			
			case 256:
			if(!is_array($value)){
			    $value = array($value);
			}
			return " IN ('".implode("', '", $value)."')";
			break;
			
        }
    }
        
}