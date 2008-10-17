<?php

class SmartestDropdownOption extends SmartestBaseDropdownOption{
    
    protected function __objectConstruct(){
        
        $this->_table_prefix = 'dropdownvalue_';
		$this->_table_name = 'DropDownValues';
        
    }
    
    public function __toString(){
        
        return $this->_properties['value'];
        
    }
    
    public function hydrateByValueWithDropdownId($value, $dropdown_id){
        
        $sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$dropdown_id."' AND dropdownvalue_value='".$value."'";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $this->hydrate($result[0]);
        }
    }
    
}