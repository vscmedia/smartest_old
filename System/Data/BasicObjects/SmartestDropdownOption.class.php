<?php

class SmartestDropdownOption extends SmartestBaseDropdownOption{
    
    protected $_dropdown = null;
    
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
    
    public function getDropdown(){
        
        $dropdown = new SmartestDropdown;
        
        if(!$this->_dropdown){
        
            if($dropdown->find($this->getDropdownId())){
                $this->_dropdown = $dropdown;
            }
        
        }
        
        return $dropdown;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "html":
            return '<option value="'.$this->_properties['value'].'">'.$this->_properties['value'].'</option>';
            
            case "dropdown":
            return $this->getDropdown();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}