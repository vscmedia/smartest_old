<?php

class SmartestDropdown extends SmartestBaseDropdown{
    
    protected $_options = array();
    
    public function getOptions(){
        
        if(!count($this->_options)){
        
            $sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$this->getId()."' ORDER BY dropdownvalue_order, dropdownvalue_label ASC";
            $result = $this->database->queryToArray($sql);
        
            $options = array();
        
            foreach($result as $opt){
                $option = new SmartestDropdownOption;
                $option->hydrate($opt);
                $options[] = $option;
            }
            
            $this->_options = $options;
        
        }
        
        return $this->_options;
        
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
    
    public function getNextOptionOrderIndex(){
        
        $index = 0;
        
        $sql = "SELECT DISTINCT dropdownvalue_order FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$this->getId()."' ORDER BY dropdownvalue_order DESC LIMIT 1";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $index = $result[0]['dropdownvalue_order']+1;
        }
        
        return $index;
        
    }
    
    public function fixOrderIndices(){
        
        $new_index = 0;
        
        foreach($this->getOptions() as $opt){
            $opt->setOrder($new_index);
            $opt->save();
            ++$new_index;
        }
        
    }
    
    public function getFieldsWhereUsed(){
        
    }
    
    public function getItemPropertiesWhereUsed(){
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "values":
            case "options":
            return $this->getOptions();
            
            case "num_values":
            case "num_options":
            return count($this->getOptions());
            
            case "render_options":
            return $this->getOptionsForRender();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}

