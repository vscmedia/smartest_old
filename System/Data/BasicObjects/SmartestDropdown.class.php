<?php

class SmartestDropdown extends SmartestBaseDropdown{
    
    protected function __objectConstruct(){
        
        $this->_table_prefix = 'dropdown_';
		$this->_table_name = 'DropDowns';
        
    }
    
    public function getOptions(){
        
        $sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$this->getId()."' ORDER BY dropdownvalue_order";
        $result = $this->database->queryToArray($sql);
        
        $options = array();
        
        foreach($result as $opt){
            $option = new SmartestDropdownOption;
            $option->hydrate($opt);
            $options[] = $option;
        }
        
        return $options;
        
    }
    
    public function getOptionsAsArrays(){
        
        $options = $this->getOptions();
        $arrays = array();
        
        foreach($options as $opt){
            $arrays[] = $opt->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getOptionsForRender(){
        
        $options = $this->getOptions();
        $arrays = array();
        $i = 0;
        
        foreach($options as $opt){
            $arrays[$i]['value'] = $opt-getValue();
            $arrays[$i]['label'] = $opt-getLabel();
            $i++;
        }
        
        return $arrays;
        
    }
    
}

