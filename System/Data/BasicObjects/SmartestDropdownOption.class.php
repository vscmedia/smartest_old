<?php

class SmartestDropdownOption extends SmartestDataObject{
    
    protected function __objectConstruct(){
        
        $this->_table_prefix = 'dropdownvalue_';
		$this->_table_name = 'DropDownValues';
        
    }
    
}