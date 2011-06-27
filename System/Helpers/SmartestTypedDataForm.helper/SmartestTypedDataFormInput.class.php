<?php

class SmartestTypedDataFormInput{
    
    protected $_type_code;
    protected $_type_info;
    protected $_name;
    protected $_value;
    protected $_id;
    
    public function setType($t){
        
        $types = SmartestDataUtility::getDataTypes();
        
        if(array_key_exists($t, $types)){
            $this->_type_code = $t;
            $this->_type_info = $types[$t];
            unset($types);
        }else{
            throw new SmartestException("Tried to render unknown data type code: ".$t);
        }
        
    }
    
    public function setName($n){
        $this->_name = $n;
    }
    
    public function setValue($v){
        $this->_value = $v;
    }
    
    public function setId($id){
        $this->_id = $id;
    }
    
    public function render(){
        
        echo $this->_type_info['input']['template'];
        
    }
    
}