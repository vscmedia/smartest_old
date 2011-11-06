<?php

class SmartestPageGroupsHelper{
    
    protected $database;
    
    public function __construct(){
        $this->database = SmartestPersistentObject::get('db:main');
    }
    
    public function getSiteGroups($site_id=''){
        
        $sql = "SELECT * FROM Sets WHERE set_type='SM_SET_PAGEGROUP_PERMANENT'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND set_site_id='".$site_id."'";
	    }
	    
	    $sql .= " ORDER BY set_name";
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $groups = array();
	    
	    foreach($result as $r){
	        $g = new SmartestPageGroup;
	        $g->hydrate($r);
	        $groups[] = $g;
	    }
	    
	    return $groups;
        
    }
    
}