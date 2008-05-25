<?php

class SmartestTodoListHelper extends SmartestHelper{
    
    protected $database;
    
    public function __construct(){
        $this->database = SmartestPersistentObject::get('db:main');
    }
    
    static function getType($type_code){
        
        $types = self::getTypes();
        
        if(isset($types[$type_code])){
            $type_data = $types[$type_code];
            $type = new SmartestTodoItemType($type_data);
            return $type;
        }else{
            // the type is invalid
            return false;
        }
    }
    
    static function getTypesXmlData(){
	    
	    $file_path = SM_ROOT_DIR.'System/Core/Types/todoitemtypes.xml';
	    
	    if(SmartestCache::hasData('todoitemtypes_xml_file_hash', true)){
	        
	        $old_hash = SmartestCache::load('todoitemtypes_xml_file_hash', true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save('todoitemtypes_xml_file_hash', $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['type'];
	            SmartestCache::save('todoitemtypes_xml_file_data', $data, -1, true);
            }else{
                $data = SmartestCache::load('todoitemtypes_xml_file_data', true);
            }
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('todoitemtypes_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('todoitemtypes_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	static function getTypes(){
	    
	    $data = self::getTypesXmlData();
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        
	        $types[$raw_type['id']] = $raw_type;
	        
	        if(!defined($raw_type['id'])){
	            define($raw_type['id'], $raw_type['id']);
	        }
	
	    }
	    
	    return $types;
	}
	
	static function getCategoriesXmlData(){
	    
	    $file_path = SM_ROOT_DIR.'System/Core/Types/todoitemtypes.xml';
	    
	    if(SmartestCache::hasData('todoitemcats_xml_file_hash', true)){
	        
	        $old_hash = SmartestCache::load('todoitemcats_xml_file_hash', true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save('todoitemcats_xml_file_hash', $new_hash, -1, true);
	            $raw_data = SmartestXmlHelper::loadFile($file_path);
	            $data = $raw_data['category'];
	            SmartestCache::save('todoitemcats_xml_file_data', $data, -1, true);
            }else{
                $data = SmartestCache::load('todoitemcats_xml_file_data', true);
            }
            
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save('todoitemcats_xml_file_hash', $new_hash, -1, true);
            $raw_data = SmartestXmlHelper::loadFile($file_path);
            $data = $raw_data['type'];
            SmartestCache::save('todoitemcats_xml_file_data', $data, -1, true);
        }
        
        return $data;
        
	}
	
	static function getCategories(){
	    
	    $data = self::getCategoriesXmlData();
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        
	        $types[$raw_type['id']] = $raw_type;
	        
	        if(!defined($raw_type['id'])){
	            define($raw_type['id'], $raw_type['id']);
	        }
	        
	    }
	    
	    return $types;
	}
	
	static function isValidCategory($category_code){
	    $ids = array_keys(self::getCategories());
	    return isset($ids[$category_code]);
	}
	
	static function isValidType($type_code){
	    $ids = array_keys(self::getTypes());
	    return isset($ids[$type_code]);
	}
	
	static function getTypesByCategory($category, $include_all=false){
	    
	    $types = self::getTypes();
	    $select_types = array();
	    $type_objects = array();
	    
	    foreach($types as $t){
	        
	        if($t['category'] == $category){
	            $select_types[$t['id']] = $t;
	        }
	        
	        if($include_all){
	            if($category == 'SM_TODOITEMCATEGORY_ALL' && !isset($select_types[$t['id']])){
	                $select_types[$t['id']] = $t;
	            }
	        }
	    }
	    
	    foreach($select_types as $a){
	        
	        $type = new SmartestTodoItemType($a);
	        $type_objects[] = $type;
	        
	    }
	    
	    return $type_objects;
	    
	}
	
	static function getTypesByCategoryAsArrays($category, $include_all=false){
	    
	    $types = self::getTypes();
	    $select_types = array();
	    $type_objects = array();
	    
	    foreach($types as $t){
	        
	        if($t['category'] == $category){
	            $select_types[$t['id']] = $t;
	        }
	        
	        if($include_all){
	            if($category == 'SM_TODOITEMCATEGORY_ALL' && !isset($select_types[$t['id']])){
	                $select_types[$t['id']] = $t;
	            }
	        }
	    }
	    
	    return $select_types;
	    
	}
    
}